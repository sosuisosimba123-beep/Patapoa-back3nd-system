<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\PushNotificationService;
use App\Services\TomTomService;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $notifications;
    protected $tomtom;

    public function __construct(PushNotificationService $notifications, TomTomService $tomtom)
    {
        $this->notifications = $notifications;
        $this->tomtom = $tomtom;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_notes' => 'nullable|string',
            'payment_method' => 'required|in:mpesa,tigo_pesa,airtel_money,halopesa,card,wallet',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            return DB::transaction(function () use ($request) {
                $address = $request->user()->addresses()->findOrFail($request->address_id);

                $subtotal = 0;
                $orderItemsData = [];
                $merchantId = null;

                foreach ($request->items as $item) {
                    $product = Product::with('masterProduct')->findOrFail($item['product_id']);

                    // Enforce single merchant per order
                    if ($merchantId === null) {
                        $merchantId = $product->merchant_id;
                    } elseif ($merchantId !== $product->merchant_id) {
                        throw new \Exception("All items in an order must be from the same merchant.");
                    }

                    if (!$product->is_available || $product->stock_count < $item['quantity']) {
                        throw new \Exception("Product '{$product->displayName}' is not available or insufficient stock");
                    }

                    $itemSubtotal = $product->price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'merchant_id' => $product->merchant_id,
                        'product_name' => $product->display_name,
                        'product_description' => $product->description ?? $product->masterProduct?->description,
                        'product_image' => $product->display_image,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $itemSubtotal,
                    ];

                    // Update stock
                    $product->decrement('stock_count', $item['quantity']);
                }

                $merchant = \App\Models\Merchant::findOrFail($merchantId);
                $pickupLat = $merchant->latitude;
                $pickupLng = $merchant->longitude;

                $distance = 0;
                $estimatedDuration = 25; // Default fallback

                if ($pickupLat && $pickupLng && $address->latitude && $address->longitude) {
                    $route = $this->tomtom->getRoute($pickupLat, $pickupLng, $address->latitude, $address->longitude);
                    if ($route) {
                        $distance = $route['distance_km'];
                        $estimatedDuration = ceil($route['travel_time_seconds'] / 60) + 10; // +10 mins for pickup
                    } else {
                        // Fallback to Haversine if TomTom fails
                        $distance = $this->calculateDistance($pickupLat, $pickupLng, $address->latitude, $address->longitude);
                        $estimatedDuration = ceil($distance * 3) + 15;
                    }
                }

                $deliveryFee = $this->calculateDeliveryFee($distance);

                // For simulation purposes, cap the delivery fee for the Global Simulation Store
                if ($merchant->store_name === 'Global Simulation Store') {
                    $deliveryFee = min($deliveryFee, 2000); // Max 2000 TZS for simulation
                }

                // Platform takes 5% from Merchant (on subtotal) and 5% from Rider (on delivery fee)
                $merchantCommission = $subtotal * 0.05;
                $riderCommission = $deliveryFee * 0.05;
                $platformFee = $merchantCommission + $riderCommission;

                // Customer only pays Product Price + Delivery Fee
                $total = $subtotal + $deliveryFee;

                $order = Order::create([
                    'order_number' => 'PAT-' . strtoupper(Str::random(6)),
                    'customer_id' => $request->user()->id,
                    'address_id' => $request->address_id,
                    'status' => 'placed',
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'platform_fee' => $platformFee,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'customer_notes' => $request->customer_notes,
                    'placed_at' => now(),
                    'pickup_latitude' => $pickupLat,
                    'pickup_longitude' => $pickupLng,
                    'dropoff_latitude' => $address->latitude,
                    'dropoff_longitude' => $address->longitude,
                    'estimated_distance_km' => $distance,
                    'estimated_duration_minutes' => $estimatedDuration,
                ]);

                foreach ($orderItemsData as $item) {
                    $order->orderItems()->create($item);
                }

                // Notify Merchant
                if ($merchant->user) {
                    $this->notifications->sendToUser(
                        $merchant->user,
                        'New Order Request ' . $order->display_id,
                        'You have a new order with ' . count($orderItemsData) . ' items waiting for confirmation.',
                        ['type' => 'new_order', 'order_id' => (string)$order->id, 'order_number' => $order->display_id]
                    );
                }

                // Notify Riders
                $this->notifications->sendToTopic(
                    'riders',
                    'New Delivery Request',
                    'Order ' . $order->display_id . ' is available. Earn TZS ' . number_format($order->delivery_fee) . '!',
                    ['type' => 'new_delivery', 'order_id' => (string)$order->id, 'order_number' => $order->display_id]
                );

                return $this->successResponse($order->load('orderItems'), 'Order created successfully', 201);
            });
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function customerOrders(Request $request)
    {
        $query = Order::where('customer_id', $request->user()->id)
            ->with(['orderItems', 'address', 'rider.user'])
            ->orderBy('created_at', 'desc');

        $orders = $this->paginateQuery($query, $request, 20, 100);

        return $this->paginatedResponse($orders, 'Orders retrieved successfully');
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['orderItems', 'address', 'rider.user', 'customer'])->findOrFail($id);

        if ($order->customer_id !== $request->user()->id &&
            !$request->user()->isAdmin() &&
            (!$request->user()->merchant || !$order->orderItems()->where('merchant_id', $request->user()->merchant->id)->exists()) &&
            (!$request->user()->rider || $order->rider_id !== $request->user()->rider->id)) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($order, 'Order retrieved successfully');
    }

    public function tracking(Request $request, $id)
    {
        $order = Order::with(['address'])->findOrFail($id);

        if ($order->customer_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $distance = $order->estimated_distance_km;
        $eta = $order->estimated_duration_minutes;

        $riderData = null;
        if ($order->rider_id) {
            $cachedLocation = \Illuminate\Support\Facades\Cache::get("rider_location:{$order->rider_id}");

            if ($cachedLocation) {
                $riderData = [
                    'id' => $order->rider_id,
                    'current_latitude' => $cachedLocation['latitude'],
                    'current_longitude' => $cachedLocation['longitude'],
                    'last_location_update' => $cachedLocation['updated_at'],
                ];
            } else {
                $rider = $order->rider()->select('id', 'current_latitude', 'current_longitude', 'last_location_update', 'user_id')->first();
                if ($rider) {
                    $riderData = [
                        'id' => $rider->id,
                        'current_latitude' => $rider->current_latitude,
                        'current_longitude' => $rider->current_longitude,
                        'last_location_update' => $rider->last_location_update,
                    ];
                }
            }
        }

        if ($riderData && $riderData['current_latitude'] && $riderData['current_longitude']) {
            $route = $this->tomtom->getRoute(
                (float)$riderData['current_latitude'],
                (float)$riderData['current_longitude'],
                (float)$order->dropoff_latitude,
                (float)$order->dropoff_longitude
            );

            if ($route) {
                $distance = $route['distance_km'];
                $eta = ceil($route['travel_time_seconds'] / 60);
            } else {
                $liveDistance = $this->calculateDistance(
                    $riderData['current_latitude'],
                    $riderData['current_longitude'],
                    $order->dropoff_latitude,
                    $order->dropoff_longitude
                );
                $distance = $liveDistance;
                $eta = ceil($liveDistance * 3);
            }
        }

        return $this->successResponse([
            'order_id' => $order->id,
            'order_number' => $order->display_id,
            'status' => $order->status,
            'rider' => $riderData,
            'dropoff_location' => [
                'latitude' => $order->dropoff_latitude,
                'longitude' => $order->dropoff_longitude,
                'address' => $order->address->address_line_1,
            ],
            'estimated_duration' => $eta,
            'distance_km' => round($distance, 2),
        ], 'Tracking data retrieved successfully');
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->customer_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if (!in_array($order->status, ['placed', 'confirmed'])) {
            return $this->errorResponse('Order cannot be cancelled at this stage', 422);
        }

        $order->update(['status' => 'cancelled']);

        foreach ($order->orderItems as $item) {
            Product::where('id', $item->product_id)->increment('stock_count', $item->quantity);
        }

        return $this->successResponse(null, 'Order cancelled successfully');
    }

    private function calculateDeliveryFee($distance)
    {
        $baseFee = 2000;
        $perKmFee = 500;
        return round($baseFee + ($distance * $perKmFee), 2);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}
