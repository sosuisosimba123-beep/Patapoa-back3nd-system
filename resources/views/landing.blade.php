<!DOCTYPE html>

<html class="dark" lang="sw"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Patapoa - Agiza tukuletee | Powered by NACCI SOFTLABS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "primary": "#42ee90",
          "primary-container": "#00d177",
          "on-primary": "#00391c",
          "on-primary-fixed": "#00210e",
          "surface": "#131313",
          "surface-container": "#201f1f",
          "surface-container-high": "#2a2a2a",
          "surface-container-lowest": "#0e0e0e",
          "surface-variant": "#353534",
          "secondary": "#c8c6c5",
          "outline": "#859587",
          "outline-variant": "#3c4a3f",
          "on-surface": "#e5e2e1",
          "on-surface-variant": "#bbcbbb"
        },
        fontFamily: {
          "body": ["Inter", "sans-serif"],
          "display": ["Inter", "sans-serif"]
        },
        spacing: {
          "margin-desktop": "64px",
          "margin-mobile": "24px",
          "container-max": "1280px"
        }
      }
    }
  }
</script>
<style>
  body {
    background-color: #0b0d0c;
    color: #e5e2e1;
    overflow-x: hidden;
    font-family: 'Inter', sans-serif;
  }

  .glass-panel {
    background: rgba(255, 255, 255, 0.035);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 50px rgba(0, 209, 119, 0.06);
  }

  .glass-hero-card {
    background: radial-gradient(120% 120% at 50% 0%, rgba(0, 209, 119, 0.12) 0%, rgba(20, 26, 22, 0.65) 60%, rgba(11, 13, 12, 0.85) 100%);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
    border: 1px solid rgba(66, 238, 144, 0.22);
    box-shadow: 0 35px 80px rgba(0, 0, 0, 0.7), 0 0 50px rgba(0, 209, 119, 0.15);
    transform-style: preserve-3d;
    perspective: 1000px;
    transition: transform 0.2s cubic-bezier(0.2, 0, 0.2, 1);
  }

  .ambient-glow {
    position: absolute;
    width: 650px;
    height: 650px;
    background: radial-gradient(circle, rgba(0, 209, 119, 0.16) 0%, rgba(11, 13, 12, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    filter: blur(75px);
  }

  /* Two strict buttons styling */
  .btn-download-app {
    background: linear-gradient(135deg, #42ee90 0%, #00d177 100%);
    color: #00210e;
    box-shadow: 0 0 25px rgba(66, 238, 144, 0.35), inset 0 1px 1px rgba(255, 255, 255, 0.6);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .btn-download-app:hover {
    box-shadow: 0 0 45px rgba(66, 238, 144, 0.6), inset 0 0 12px rgba(255, 255, 255, 0.8);
    transform: translateY(-2px) scale(1.02);
  }

  .btn-whatsapp-support {
    background: rgba(37, 211, 102, 0.12);
    border: 1px solid rgba(37, 211, 102, 0.5);
    color: #42ee90;
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .btn-whatsapp-support:hover {
    background: rgba(37, 211, 102, 0.22);
    border-color: #25d366;
    box-shadow: 0 0 35px rgba(37, 211, 102, 0.35);
    color: #ffffff;
    transform: translateY(-2px) scale(1.02);
  }

  /* Ignition Disruption Animation */
  #ignition-overlay {
    transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.9s cubic-bezier(0.4, 0, 0.2, 1), filter 0.8s ease;
  }
  #ignition-overlay.faded-out {
    opacity: 0;
    pointer-events: none;
    transform: scale(1.15);
    filter: blur(20px);
  }

  .rider-vehicle {
    transition: transform 0.45s cubic-bezier(0.55, 0.055, 0.675, 0.19);
  }
  .rider-vehicle.accelerate {
    transform: translateX(260px) scale(1.15);
  }
  .rider-vehicle.disrupt {
    opacity: 0;
    transform: translateX(450px) scale(1.4);
    filter: blur(12px) brightness(2.5);
  }

  @keyframes particleExplode {
    0% {
      transform: translate(0, 0) scale(1);
      opacity: 1;
    }
    100% {
      transform: translate(var(--tx), var(--ty)) scale(0);
      opacity: 0;
    }
  }

  .disrupt-particle {
    position: absolute;
    border-radius: 50%;
    background: #42ee90;
    box-shadow: 0 0 12px #00d177;
    pointer-events: none;
  }

  .disrupt-shockwave {
    position: absolute;
    border-radius: 50%;
    border: 2px solid #42ee90;
    transform: scale(0);
    opacity: 0;
    pointer-events: none;
  }
  .disrupt-shockwave.active {
    animation: shockwaveBlow 0.9s cubic-bezier(0.1, 0.8, 0.2, 1) forwards;
  }
  @keyframes shockwaveBlow {
    0% { transform: scale(0.1); opacity: 0.9; border-width: 8px; }
    50% { opacity: 0.6; }
    100% { transform: scale(12); opacity: 0; border-width: 1px; }
  }

  /* Headlight Beam Pulsing */
  @keyframes beamPulse {
    0%, 100% { opacity: 0.35; }
    50% { opacity: 0.85; filter: drop-shadow(0 0 14px #42ee90); }
  }
  .animate-beam {
    animation: beamPulse 1.4s ease-in-out infinite;
  }

  /* Speed lines kinetic infinite pulse */
  @keyframes dashMove {
    0% { stroke-dashoffset: 20; opacity: 0.2; }
    50% { opacity: 0.9; }
    100% { stroke-dashoffset: -20; opacity: 0.2; }
  }
  .speed-line {
    stroke-dasharray: 8 6;
    animation: dashMove 0.8s linear infinite;
  }

  /* Motion Graphic Dispatch Animations */
  @keyframes flowTrackDash {
    0% { stroke-dashoffset: 120; }
    100% { stroke-dashoffset: 0; }
  }
  .flow-dashed-line {
    stroke-dasharray: 10 10;
    animation: flowTrackDash 2.5s linear infinite;
  }

  @keyframes pulseNodeGlow {
    0%, 100% { box-shadow: 0 0 15px rgba(66, 238, 144, 0.2), inset 0 0 10px rgba(66, 238, 144, 0.1); }
    50% { box-shadow: 0 0 32px rgba(66, 238, 144, 0.45), inset 0 0 18px rgba(66, 238, 144, 0.3); }
  }
  .glow-pulse-box {
    animation: pulseNodeGlow 2.4s ease-in-out infinite;
  }

  @keyframes floatPackage {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-4px); }
  }
  .animate-float-package {
    animation: floatPackage 2s ease-in-out infinite;
  }

  /* Continuous In-Page Rider Motion Track Animation */
  @keyframes dispatchCycle {
    0% {
      left: 12%;
      opacity: 0;
      transform: translateY(-50%) scale(0.9);
    }
    6% {
      left: 15%;
      opacity: 1;
      transform: translateY(-50%) scale(1);
    }
    48% {
      left: 49%;
      opacity: 1;
      transform: translateY(-50%) scale(1.04);
    }
    92% {
      left: 82%;
      opacity: 1;
      transform: translateY(-50%) scale(1);
    }
    98%, 100% {
      left: 85%;
      opacity: 0;
      transform: translateY(-50%) scale(0.95);
    }
  }

  .rider-dispatch-transit {
    animation: dispatchCycle 9s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
  }

  @keyframes checkBounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); filter: drop-shadow(0 0 10px #42ee90); }
  }
  .animate-check-glow {
    animation: checkBounce 2.2s ease-in-out infinite;
  }

  /* INTRO SEQUENCE TIMELINE ANIMATIONS */
  .intro-phase-hidden {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.96);
    transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .intro-phase-visible {
    opacity: 1;
    pointer-events: auto;
    transform: scale(1);
    transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  }

  /* Sequential Rider Travel in Overlay (Merchant -> Corridor -> Doorstep) */
  .intro-travel-rider {
    left: 12%;
    opacity: 0;
    transform: translateY(-50%) scale(0.85);
    transition: left 2.6s cubic-bezier(0.45, 0.05, 0.2, 0.95), transform 0.4s ease, opacity 0.3s ease;
  }
  .intro-travel-rider.moving {
    opacity: 1;
    left: 84%;
    transform: translateY(-50%) scale(1);
  }

  /* Flash / Doorstep portal arrival expansion */
  #intro-doorstep-flash {
    opacity: 0;
    transform: scale(0.2);
    pointer-events: none;
    transition: opacity 0.4s ease-out, transform 0.7s cubic-bezier(0.1, 0.9, 0.2, 1);
  }
  #intro-doorstep-flash.burst {
    opacity: 1;
    transform: scale(8);
  }

  /* Cinematic page reveal unmasking */
  #main-website-root {
    transition: filter 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
  }
  #main-website-root.revealing {
    animation: websiteCurtainReveal 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes websiteCurtainReveal {
    0% {
      opacity: 0;
      transform: scale(0.97) translateY(18px);
      filter: blur(14px);
    }
    100% {
      opacity: 1;
      transform: scale(1) translateY(0);
      filter: blur(0px);
    }
  }
</style>
</head>
<body class="antialiased min-h-screen relative selection:bg-primary selection:text-on-primary-fixed bg-[#0b0d0c]">
<!-- ============================================================== -->
<!-- 1. FULL SEQUENTIAL INTRO FLOW OVERLAY                          -->
<!--    Phase 1: Cinematic Ignition & Disruption Burst              -->
<!--    Phase 2: Delivery Flow Journey (Merchant -> Track -> Door)  -->
<!--    Phase 3: Spectacular Doorstep Arrival & Luminous Wipe       -->
<!-- ============================================================== -->
<div class="fixed inset-0 z-[120] flex flex-col items-center justify-center bg-[#070908] overflow-hidden select-none" id="ignition-overlay">
<!-- Subtle Background Grid & Vignette -->
<div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_transparent_10%,_#040505_90%)] pointer-events-none"></div>
<div class="absolute inset-0 bg-[linear-gradient(to_right,#00d17708_1px,transparent_1px),linear-gradient(to_bottom,#00d17708_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>
<!-- Top Intro Navigation Bar: Non-clickable info chip + Skip Action -->
<div class="absolute top-6 left-0 right-0 max-w-container-max mx-auto px-6 flex items-center justify-between z-50">
<div class="flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/[0.04] border border-white/10 backdrop-blur-md">
<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
<span class="text-xs font-mono font-bold text-white tracking-wider">Patapoa Experience</span>
<span class="text-[10px] font-mono text-primary uppercase px-1.5 py-0.5 rounded bg-primary/10 border border-primary/20">60 FPS</span>
</div>
<button class="cursor-pointer px-4 py-1.5 rounded-full bg-white/[0.06] hover:bg-primary/20 border border-white/15 hover:border-primary/40 text-secondary hover:text-white transition-all text-xs font-mono font-bold flex items-center gap-1.5 shadow-lg" id="btn-skip-intro" onclick="skipIntroSequence()">
<span>Skip Intro</span>
<span class="material-symbols-outlined text-sm">fast_forward</span>
</button>
</div>
<!-- Shockwave Ring Container -->
<div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10" id="shockwave-container">
<div class="disrupt-shockwave w-36 h-36" id="shockwave-ring-1"></div>
<div class="disrupt-shockwave w-36 h-36 delay-150" id="shockwave-ring-2"></div>
</div>
<!-- Particle Burst Container -->
<div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20" id="particle-burst-box"></div>
<!-- STAGE A: IGNITION & DISRUPTION BURST -->
<div class="relative z-30 flex flex-col items-center justify-center transition-all duration-500 w-full max-w-xl px-4" id="intro-phase-ignition">
<div class="rider-vehicle relative w-[320px] h-[240px] flex items-center justify-center cursor-pointer" id="rider-wrap" onclick="startSequentialJourney()">
<svg class="filter drop-shadow-[0_0_20px_rgba(0,209,119,0.5)]" fill="none" height="240" viewbox="0 0 160 120" width="320" xmlns="http://www.w3.org/2000/svg">
<circle cx="80" cy="65" fill="#00d177" fill-opacity="0.12" filter="blur(18px)" r="45"></circle>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="2.5" x1="12" x2="38" y1="52" y2="52"></line>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="2" x1="8" x2="42" y1="68" y2="68"></line>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="18" x2="48" y1="84" y2="84"></line>
<circle cx="50" cy="85" fill="#09090b" r="16" stroke="#00d177" stroke-width="3.5"></circle>
<circle cx="50" cy="85" fill="#00d177" fill-opacity="0.3" r="7" stroke="#00d177" stroke-width="2"></circle>
<circle cx="120" cy="85" fill="#09090b" r="16" stroke="#00d177" stroke-width="3.5"></circle>
<circle cx="120" cy="85" fill="#00d177" fill-opacity="0.3" r="7" stroke="#00d177" stroke-width="2"></circle>
<path d="M 50 85 L 72 85 L 88 64 L 112 64 L 120 85" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<path d="M 72 85 L 82 54" stroke="#00d177" stroke-linecap="round" stroke-width="3"></path>
<path d="M 120 85 L 110 50 L 102 50" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<polygon class="animate-beam" fill="#afffd2" fill-opacity="0.4" points="118,53 154,43 158,67 119,57"></polygon>
<circle class="filter drop-shadow-[0_0_8px_#ffffff]" cx="117" cy="54" fill="#afffd2" r="3.5"></circle>
<rect fill="#00d177" fill-opacity="0.3" height="26" rx="4" stroke="#00d177" stroke-width="3" width="22" x="52" y="42"></rect>
<line stroke="#00d177" stroke-width="2" x1="52" x2="74" y1="50" y2="50"></line>
<path d="M 60 56 L 66 56 L 65 63 L 61 63 Z" fill="none" stroke="#00d177" stroke-width="1.5"></path>
<path d="M 61.5 56 C 61.5 54 64.5 54 64.5 56" fill="none" stroke="#00d177" stroke-width="1.2"></path>
<path d="M 74 54 C 74 48, 86 46, 95 52 L 106 52" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<line stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="84" x2="88" y1="54" y2="70"></line>
<line stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="88" x2="98" y1="70" y2="72"></line>
<circle cx="98" cy="38" fill="#09090b" r="8" stroke="#00d177" stroke-width="3"></circle>
<path d="M 100 36 Q 106 38 104 42" fill="none" stroke="#afffd2" stroke-linecap="round" stroke-width="2.5"></path>
</svg>
</div>
<div class="mt-4 flex flex-col items-center gap-2">
<div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/30 backdrop-blur-md">
<span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
<span class="font-mono text-xs uppercase tracking-widest text-primary font-bold">Ignition in progress</span>
</div>
<p class="text-sm font-semibold tracking-wide text-[#e5e2e1] mt-1">
      Patapoa • <span class="text-primary">Agiza tukuletee</span>
</p>
<span class="text-[11px] font-mono text-secondary/60">Launching journey in <span id="ignite-timer">1.0</span>s...</span>
</div>
</div>
<!-- STAGE B: SEAMLESS FULL-SCREEN KINETIC DISPATCH JOURNEY -->
<div class="intro-phase-hidden absolute inset-0 z-30 flex flex-col items-center justify-center px-4 sm:px-8 max-w-5xl mx-auto" id="intro-phase-journey">
<!-- Journey Status Header -->
<div class="text-center mb-6">
<div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/15 border border-primary/30 text-primary text-xs font-mono font-bold tracking-widest uppercase mb-3 shadow-[0_0_20px_rgba(66,238,144,0.3)]">
<span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
<span id="intro-journey-badge">Safari ya Mzigo Wako Inaanza</span>
</div>
<h2 class="text-2xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
      Kutoka Dukani Mpaka <span class="text-primary italic">Mlangoni Kwako</span>
</h2>
<p class="text-xs sm:text-sm text-secondary mt-1 font-mono">
      Muuzaji (Merchant) → Rider wa Patapoa → Mlangoni kwa Mteja
    </p>
</div>
<!-- Realtime Checkpoints in Overlay -->
<div class="grid grid-cols-3 gap-2 sm:gap-4 w-full max-w-2xl mx-auto mb-6">
<div class="flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-primary/20 border border-primary/50 text-center shadow-[0_0_15px_rgba(66,238,144,0.3)] transition-all" id="intro-pill-1">
<span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
<span class="text-[10px] sm:text-xs font-mono font-bold text-primary">1. Dukani (Picked Up)</span>
</div>
<div class="flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all" id="intro-pill-2">
<span class="w-2 h-2 rounded-full bg-secondary/50"></span>
<span class="text-[10px] sm:text-xs font-mono font-bold text-secondary/70">2. Safarini (In Transit)</span>
</div>
<div class="flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all" id="intro-pill-3">
<span class="w-2 h-2 rounded-full bg-secondary/50"></span>
<span class="text-[10px] sm:text-xs font-mono font-bold text-secondary/70">3. Mlangoni (Delivered)</span>
</div>
</div>
<!-- Cinematic Track Box -->
<div class="relative w-full h-[250px] sm:h-[280px] rounded-3xl border border-primary/30 bg-[#090c0b]/90 backdrop-blur-2xl overflow-hidden flex items-center justify-between px-4 sm:px-12 shadow-[0_20px_60px_rgba(0,0,0,0.8),0_0_50px_rgba(0,209,119,0.15)]">
<!-- Grid pattern inside arena -->
<div class="absolute inset-0 bg-[linear-gradient(to_right,#00d1770c_1px,transparent_1px),linear-gradient(to_bottom,#00d1770c_1px,transparent_1px)] bg-[size:2rem_2rem] pointer-events-none"></div>
<!-- Neon Transit Track Line -->
<svg class="absolute inset-0 w-full h-full pointer-events-none z-0" preserveaspectratio="none" viewbox="0 0 1000 250">
<defs>
<lineargradient id="introTrackGrad" x1="0%" x2="100%" y1="0%" y2="0%">
<stop offset="0%" stop-color="#00d177" stop-opacity="0.35"></stop>
<stop offset="50%" stop-color="#42ee90" stop-opacity="0.95"></stop>
<stop offset="100%" stop-color="#00d177" stop-opacity="0.4"></stop>
</lineargradient>
</defs>
<path d="M 120 150 C 340 150, 420 150, 500 150 C 580 150, 660 150, 880 150" fill="none" opacity="0.3" stroke="#00d177" stroke-width="6"></path>
<path class="flow-dashed-line" d="M 120 150 C 340 150, 420 150, 500 150 C 580 150, 660 150, 880 150" fill="none" stroke="url(#introTrackGrad)" stroke-linecap="round" stroke-width="3.5"></path>
</svg>
<!-- NODE 1: MERCHANT (Duka la Muuzaji) -->
<div class="relative z-20 flex flex-col items-center select-none" id="intro-node-store">
<div class="glow-pulse-box w-24 sm:w-32 h-28 sm:h-36 rounded-2xl bg-gradient-to-b from-[#1b221d] to-[#111413] border border-primary/40 flex flex-col items-center justify-between p-3.5 backdrop-blur-md">
<div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-primary/20 border border-primary/40">
<span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
<span class="text-[9px] font-mono font-bold text-primary uppercase">Open Store</span>
</div>
<svg class="w-11 sm:w-12 h-11 sm:h-12 text-primary filter drop-shadow-[0_0_12px_rgba(66,238,144,0.4)]" fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24">
<path d="M3 21h18M3 7v1a3 3 0 006 0V7m0 1a3 3 0 006 0V7m0 1a3 3 0 006 0V7H3l2-4h14l2 4M5 21V10.85a4.978 4.978 0 002-1.85m10 1.85V21" stroke-linecap="round" stroke-linejoin="round"></path>
<rect fill="#00d177" fill-opacity="0.25" height="5" rx="1" stroke="#42ee90" width="6" x="9" y="15"></rect>
</svg>
<div class="animate-float-package flex items-center gap-1 bg-[#090b0a] border border-primary/50 px-2 py-0.5 rounded-lg text-[10px] font-mono text-white shadow-[0_0_12px_rgba(0,209,119,0.3)]">
<span class="material-symbols-outlined text-primary text-xs" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
<span>Mzigo Tayari</span>
</div>
</div>
<span class="text-xs font-bold text-white mt-2">Duka la Muuzaji</span>
<span class="text-[10px] font-mono text-secondary/70">Merchant Store</span>
</div>
<!-- TRAVELING DELIVERY RIDER (Programmed displacement across timeline) -->
<div class="intro-travel-rider absolute top-[60%] z-30 pointer-events-none -translate-x-1/2 flex items-center justify-center" id="intro-rider-traveler">
<div class="relative flex items-center justify-center">
<!-- Headlight Beam cone -->
<div class="absolute left-24 w-36 h-16 bg-gradient-to-r from-primary/40 via-primary/15 to-transparent blur-md transform -translate-y-1 rotate-1 pointer-events-none"></div>
<!-- Cyber Neon Rider SVG -->
<svg class="w-32 sm:w-44 h-auto filter drop-shadow-[0_0_18px_rgba(0,209,119,0.7)]" fill="none" viewbox="0 0 160 120" xmlns="http://www.w3.org/2000/svg">
<circle cx="80" cy="65" fill="#00d177" fill-opacity="0.2" filter="blur(14px)" r="40"></circle>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="2.5" x1="12" x2="38" y1="52" y2="52"></line>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="2" x1="8" x2="42" y1="68" y2="68"></line>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="18" x2="48" y1="84" y2="84"></line>
<circle cx="50" cy="85" fill="#09090b" r="16" stroke="#00d177" stroke-width="3.5"></circle>
<circle cx="50" cy="85" fill="#00d177" fill-opacity="0.3" r="7" stroke="#00d177" stroke-width="2"></circle>
<circle cx="120" cy="85" fill="#09090b" r="16" stroke="#00d177" stroke-width="3.5"></circle>
<circle cx="120" cy="85" fill="#00d177" fill-opacity="0.3" r="7" stroke="#00d177" stroke-width="2"></circle>
<path d="M 50 85 L 72 85 L 88 64 L 112 64 L 120 85" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<path d="M 72 85 L 82 54" stroke="#00d177" stroke-linecap="round" stroke-width="3"></path>
<path d="M 120 85 L 110 50 L 102 50" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<polygon class="animate-beam" fill="#afffd2" fill-opacity="0.45" points="118,53 150,44 154,66 119,57"></polygon>
<circle cx="117" cy="54" fill="#afffd2" r="3.5"></circle>
<rect fill="#00d177" fill-opacity="0.3" height="26" rx="4" stroke="#00d177" stroke-width="3" width="22" x="52" y="42"></rect>
<line stroke="#00d177" stroke-width="2" x1="52" x2="74" y1="50" y2="50"></line>
<path d="M 74 54 C 74 48, 86 46, 95 52 L 106 52" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<line stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="84" x2="88" y1="54" y2="70"></line>
<line stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="88" x2="98" y1="70" y2="72"></line>
<circle cx="98" cy="38" fill="#09090b" r="8" stroke="#00d177" stroke-width="3"></circle>
<path d="M 100 36 Q 106 38 104 42" fill="none" stroke="#afffd2" stroke-linecap="round" stroke-width="2.5"></path>
</svg>
</div>
</div>
<!-- NODE 2: DOORSTEP (Mlangoni kwa Mteja) -->
<div class="relative z-20 flex flex-col items-center select-none" id="intro-node-doorstep">
<div class="glow-pulse-box w-24 sm:w-32 h-28 sm:h-36 rounded-2xl bg-gradient-to-b from-[#1b221d] to-[#111413] border border-primary/40 flex flex-col items-center justify-between p-3.5 backdrop-blur-md relative" id="intro-doorstep-card">
<!-- Awesome Flash Portal Effect Triggered upon Arrival -->
<div class="absolute inset-0 rounded-2xl bg-primary/30 blur-xl" id="intro-doorstep-flash"></div>
<div class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary/20 border border-primary/40">
<span class="material-symbols-outlined text-[12px] text-primary" style="font-variation-settings: 'FILL' 1;">home</span>
<span class="text-[9px] font-mono font-bold text-primary uppercase">Doorstep</span>
</div>
<svg class="w-11 sm:w-12 h-11 sm:h-12 text-primary filter drop-shadow-[0_0_12px_rgba(66,238,144,0.4)]" fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24">
<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
<div class="animate-check-glow flex items-center gap-1 bg-primary text-[#00210e] px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold shadow-[0_0_15px_rgba(66,238,144,0.5)]" id="intro-check-badge">
<span class="material-symbols-outlined text-xs">done_all</span>
<span>Imefika!</span>
</div>
</div>
<span class="text-xs font-bold text-white mt-2">Mlangoni kwa Mteja</span>
<span class="text-[10px] font-mono text-secondary/70">Customer Doorstep</span>
</div>
</div>
<!-- Telemetry Subtext -->
<div class="mt-4 flex items-center gap-3 text-xs font-mono text-secondary/80">
<span class="text-primary font-bold">Haraka</span>
<span>•</span>
<span>Salama Mlangoni Mwako</span>
<span>•</span>
<span>Dakika 15 Pekee</span>
</div>
</div>
<!-- Bottom Brand Footnote -->
<div class="absolute bottom-6 flex items-center gap-2 text-xs font-mono text-secondary/40 tracking-wider">
<span>NACCI SOFTLABS</span>
<span>•</span>
<span>HOTLINE: +255 715 080 235</span>
</div>
</div>
<!-- ============================================================== -->
<!-- 2. MAIN WEBSITE LANDING ROOT (Revealed seamlessly)              -->
<!-- ============================================================== -->
<div class="relative" id="main-website-root">
<!-- BACKGROUND AMBIENT GLOWS -->
<div class="ambient-glow -top-32 -left-32"></div>
<div class="ambient-glow top-[45%] -right-40"></div>
<div class="ambient-glow bottom-0 left-1/3"></div>
<!-- HEADER / BRAND IDENTIFIER (Non-clickable info bar + Replay Motion Trigger) -->
<header class="fixed top-0 w-full z-40 bg-[#0b0d0c]/75 backdrop-blur-xl border-b border-white/[0.07]">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-4 flex items-center justify-between">
<!-- Brand Logo Mark (Informational) -->
<div class="flex items-center gap-3 select-none cursor-pointer" onclick="replayFullIntro()" title="Bonyeza kurudia Intro ya Patapoa">
<div class="w-10 h-10 rounded-xl bg-primary/15 border border-primary/40 flex items-center justify-center text-primary shadow-[0_0_18px_rgba(66,238,144,0.3)]">
<span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">electric_moped</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-black text-2xl tracking-tighter text-white">Patapoa</span>
<span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-primary/20 text-primary border border-primary/30">Official</span>
</div>
<p class="text-[11px] text-secondary font-medium tracking-wide">Agiza tukuletee</p>
</div>
</div>
<!-- Official Hotline Status Chip & Replay Intro Control -->
<div class="flex items-center gap-3">
<button class="cursor-pointer hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.04] hover:bg-primary/20 border border-white/10 hover:border-primary/40 text-xs font-mono text-secondary hover:text-white transition-all shadow-md" onclick="replayFullIntro()">
<span class="material-symbols-outlined text-sm text-primary">replay</span>
<span>Rudia Safari</span>
</button>
<div class="hidden sm:flex items-center gap-3 px-4 py-2 rounded-full glass-panel border-white/10 select-none">
<span class="relative flex h-2.5 w-2.5">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
<span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
</span>
<span class="text-xs font-mono text-secondary">Hotline:</span>
<span class="text-xs font-mono font-bold text-white tracking-wider">+255 715 080 235</span>
</div>
</div>
</div>
</header>
<!-- MAIN CONTENT -->
<main class="relative z-10 pt-[120px] pb-24 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<!-- HERO SECTION: 3D Mouse Tilt Glass Card -->
<section class="mb-24 flex flex-col items-center text-center relative">
<div class="w-full max-w-4xl py-6 perspective-[1200px]" id="hero-card-container">
<div class="glass-hero-card rounded-[32px] p-8 md:p-16 relative overflow-hidden border border-primary/30 text-left md:text-center flex flex-col items-center" id="hero-glass-card">
<!-- Top Tag Info Chip -->
<div class="inline-flex items-center gap-2 bg-black/40 backdrop-blur-md border border-primary/30 rounded-full px-4 py-1.5 mb-6 shadow-lg">
<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
<span class="text-xs font-mono text-primary font-bold uppercase tracking-widest">Huduma Ya Haraka Ya Delivery • Tanzania</span>
</div>
<!-- Slogan & Sub-slogan -->
<h1 class="text-4xl sm:text-6xl md:text-7xl font-black tracking-tight text-white leading-[1.08] mb-6">
          Agiza <span class="text-primary italic drop-shadow-[0_0_35px_rgba(66,238,144,0.45)]">tukuletee.</span>
</h1>
<p class="text-xl md:text-2xl text-white/90 font-semibold max-w-2xl leading-relaxed mb-4">
          Tujuze unachohitaji tukuletee.
        </p>
<p class="text-sm md:text-base text-secondary max-w-xl leading-relaxed mb-10">
          Chagua chochote unachotaka: vyakula, maduka ya jumla, vifaa vya nyumbani au dawa. Timu yetu ya madereva waliojitayarisha itakuletea popote ulipo kwa kasi ya ajabu.
        </p>
<!-- STRICT ACTION BUTTONS: ONLY 2 BUTTONS PERMITTED -->
<div class="flex flex-col sm:flex-row items-center justify-center gap-5 w-full max-w-lg">
<!-- Button 1: Download App -->
<a class="btn-download-app w-full sm:w-auto flex-1 px-8 py-4 rounded-full font-bold text-base flex items-center justify-center gap-3 text-[#00210e] text-center" href="#download" title="Pakua Patapoa App">
<span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">install_mobile</span>
<div class="text-left">
<div class="text-[10px] uppercase font-mono tracking-widest leading-none text-[#00391c]/80">Pata sasa</div>
<div class="text-base font-extrabold leading-tight">Download App</div>
</div>
</a>
<!-- Button 2: WhatsApp Contact Support -->
<a class="btn-whatsapp-support w-full sm:w-auto flex-1 px-8 py-4 rounded-full font-bold text-base flex items-center justify-center gap-3 text-center" href="https://wa.me/255715080235?text=Habari%20Patapoa,%20nahitaji%20huduma%20ya%20kuletewa%20mzigo." rel="noopener noreferrer" target="_blank" title="Wasiliana nasi WhatsApp Hotline">
<svg class="w-6 h-6 fill-current text-primary" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"></path>
</svg>
<div class="text-left">
<div class="text-[10px] uppercase font-mono tracking-widest leading-none text-primary">Msaada Wa Papo Hapo</div>
<div class="text-base font-bold leading-tight">WhatsApp Support</div>
</div>
</a>
</div>
<!-- Official verification badge under action buttons -->
<div class="mt-8 flex flex-wrap items-center justify-center gap-6 pt-6 border-t border-white/10 text-xs text-secondary font-mono select-none">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-base">verified</span>
<span>Product of NACCI SOFTLABS</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-base">local_shipping</span>
<span>Haraka &amp; Salama</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-base">support_agent</span>
<span>Msaada Masaa 24/7</span>
</div>
</div>
</div>
</div>
</section>
<!-- CATEGORIES TICKER CORRIDOR (Non-clickable informational chips) -->
<section class="mb-24">
<div class="text-center mb-6">
<span class="text-xs font-mono text-primary uppercase tracking-widest">Huduma Tunazozifikisha Mlangoni Mwako</span>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 select-none">
<div class="glass-panel rounded-2xl p-5 border border-white/10 flex flex-col items-center text-center">
<div class="w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-3">
<span class="material-symbols-outlined text-2xl">home_repair_service</span>
</div>
<h4 class="font-bold text-white text-sm">Household Essentials</h4>
<p class="text-xs text-secondary/80 mt-1">Vifaa vya usafi &amp; mahitaji ya nyumbani</p>
</div>
<div class="glass-panel rounded-2xl p-5 border border-white/10 flex flex-col items-center text-center">
<div class="w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-3">
<span class="material-symbols-outlined text-2xl">local_grocery_store</span>
</div>
<h4 class="font-bold text-white text-sm">Supermarket Groceries</h4>
<p class="text-xs text-secondary/80 mt-1">Vyakula vibichi, nafaka na bidhaa za jikoni</p>
</div>
<div class="glass-panel rounded-2xl p-5 border border-white/10 flex flex-col items-center text-center">
<div class="w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-3">
<span class="material-symbols-outlined text-2xl">devices</span>
</div>
<h4 class="font-bold text-white text-sm">Electronics &amp; Accessories</h4>
<p class="text-xs text-secondary/80 mt-1">Chaja, cables, earphones na vifaa vidogo</p>
</div>
<div class="glass-panel rounded-2xl p-5 border border-white/10 flex flex-col items-center text-center">
<div class="w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-3">
<span class="material-symbols-outlined text-2xl">fastfood</span>
</div>
<h4 class="font-bold text-white text-sm">Snacks &amp; Drinks</h4>
<p class="text-xs text-secondary/80 mt-1">Vinywaji baridi, bites na vitafunwa haraka</p>
</div>
</div>
</section>
<!-- "HOW IT WORKS" 3-STEP FROSTED GLASS DEPTH GRID -->
<section class="mb-24">
<div class="text-center max-w-xl mx-auto mb-12 select-none">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-mono uppercase tracking-widest mb-3">
        Mchakato Rahisi
      </div>
<h2 class="text-3xl md:text-4xl font-black text-white">Jinsi Patapoa Inavyofanya Kazi</h2>
<p class="text-sm md:text-base text-secondary mt-2">
        Hatua tatu pekee rahisi kukamilisha oda yako na kupokea mzigo mlangoni kwako.
      </p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 select-none">
<div class="glass-panel rounded-3xl p-8 border border-white/10 relative overflow-hidden flex flex-col">
<div class="text-5xl font-black text-primary/20 absolute top-4 right-6 font-mono pointer-events-none">01</div>
<div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/15 flex items-center justify-center text-primary mb-6 shadow-lg">
<span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">search</span>
</div>
<h3 class="text-xl font-bold text-white mb-2">1. Tafuta (Browse)</h3>
<p class="text-secondary text-sm leading-relaxed mb-4">
          Fungua App au tuma orodha yako ya ununuzi. Chagua bidhaa yoyote kutoka maduka jirani au masoko yanayokuzunguka.
        </p>
<div class="mt-auto pt-4 border-t border-white/5 flex items-center gap-2 text-xs font-mono text-primary">
<span class="material-symbols-outlined text-sm">check_circle</span>
<span>Zaidi ya bidhaa 5,000+ zilizoorodheshwa</span>
</div>
</div>
<div class="glass-panel rounded-3xl p-8 border border-primary/25 relative overflow-hidden flex flex-col shadow-[0_10px_35px_rgba(0,209,119,0.1)]">
<div class="text-5xl font-black text-primary/30 absolute top-4 right-6 font-mono pointer-events-none">02</div>
<div class="w-14 h-14 rounded-2xl bg-primary/20 border border-primary/40 flex items-center justify-center text-primary mb-6 shadow-lg">
<span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">shopping_cart_checkout</span>
</div>
<h3 class="text-xl font-bold text-white mb-2">2. Agiza (Order)</h3>
<p class="text-secondary text-sm leading-relaxed mb-4">
          Tujuze unachohitaji tukuletee! Thibitisha anwani yako na njia ya malipo uipendayo, au wasiliana moja kwa moja na timu yetu.
        </p>
<div class="mt-auto pt-4 border-t border-white/5 flex items-center gap-2 text-xs font-mono text-primary">
<span class="material-symbols-outlined text-sm">bolt</span>
<span>Uthibitisho wa papo hapo ndani ya sekunde chache</span>
</div>
</div>
<div class="glass-panel rounded-3xl p-8 border border-white/10 relative overflow-hidden flex flex-col">
<div class="text-5xl font-black text-primary/20 absolute top-4 right-6 font-mono pointer-events-none">03</div>
<div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/15 flex items-center justify-center text-primary mb-6 shadow-lg">
<span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">package_2</span>
</div>
<h3 class="text-xl font-bold text-white mb-2">3. Pokea (Receive)</h3>
<p class="text-secondary text-sm leading-relaxed mb-4">
          Dereva wetu wa Patapoa aliye na mkoba maalum wa joto atakuletea mzigo wako haraka sana ukiwa safi na salama.
        </p>
<div class="mt-auto pt-4 border-t border-white/5 flex items-center gap-2 text-xs font-mono text-primary">
<span class="material-symbols-outlined text-sm">schedule</span>
<span>Delivery ya haraka chini ya dakika 15</span>
</div>
</div>
</div>
</section>
<!-- IN-PAGE KINETIC DISPATCH MOTION GRAPHIC (Merchant -> Rider -> Doorstep) -->
<section class="mb-24 relative select-none" id="dispatch-flow-section">
<div class="text-center max-w-2xl mx-auto mb-10">
<div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/30 text-primary text-xs font-mono font-bold tracking-widest uppercase mb-3 shadow-[0_0_20px_rgba(66,238,144,0.2)]">
<span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
<span>Kinetic Dispatch Flow • End-to-End Motion</span>
</div>
<h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
      Kutoka Dukani Mpaka <span class="text-primary italic">Mlangoni Kwako</span>
</h2>
<p class="text-sm md:text-base text-secondary mt-3 leading-relaxed">
      Fuatilia safari ya oda yako kwa wakati halisi: Duka la Muuzaji (Merchant) → Rider wa Patapoa → Mlangoni kwa Mteja
</p>
</div>
<div class="glass-panel rounded-[32px] p-6 sm:p-10 border border-primary/25 relative overflow-hidden bg-gradient-to-b from-[#131313]/90 via-[#0e0e0e]/95 to-[#131313]/90 shadow-[0_25px_60px_rgba(0,0,0,0.8),0_0_40px_rgba(0,209,119,0.08)]">
<div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(0,209,119,0.09)_0%,transparent_70%)] pointer-events-none"></div>
<div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-[#0e0e0e] to-transparent pointer-events-none"></div>
<!-- Checkpoint Status Pill Bar -->
<div class="grid grid-cols-3 gap-2 sm:gap-4 max-w-3xl mx-auto mb-8 relative z-20">
<div class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all" id="status-pill-1">
<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
<span class="text-[11px] sm:text-xs font-mono font-bold text-white tracking-wide">1. Dukani (Picked Up)</span>
</div>
<div class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-primary/15 border border-primary/40 text-center shadow-[0_0_15px_rgba(66,238,144,0.25)] transition-all" id="status-pill-2">
<span class="w-2 h-2 rounded-full bg-primary"></span>
<span class="text-[11px] sm:text-xs font-mono font-bold text-primary tracking-wide">2. Safarini (In Transit)</span>
</div>
<div class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all" id="status-pill-3">
<span class="w-2 h-2 rounded-full bg-secondary/50"></span>
<span class="text-[11px] sm:text-xs font-mono font-bold text-secondary/70 tracking-wide">3. Mlangoni (Delivered)</span>
</div>
</div>
<!-- Arena -->
<div class="relative w-full h-[240px] sm:h-[270px] flex items-center justify-between px-2 sm:px-6 overflow-hidden rounded-2xl border border-white/5 bg-[#090b0a]/70">
<div class="absolute inset-0 bg-[linear-gradient(to_right,#00d17709_1px,transparent_1px),linear-gradient(to_bottom,#00d17709_1px,transparent_1px)] bg-[size:2rem_2rem] pointer-events-none"></div>
<svg class="absolute inset-0 w-full h-full pointer-events-none z-0" preserveaspectratio="none" viewbox="0 0 1000 240">
<defs>
<lineargradient id="trackGradient" x1="0%" x2="100%" y1="0%" y2="0%">
<stop offset="0%" stop-color="#00d177" stop-opacity="0.3"></stop>
<stop offset="50%" stop-color="#42ee90" stop-opacity="0.85"></stop>
<stop offset="100%" stop-color="#00d177" stop-opacity="0.35"></stop>
</lineargradient>
<filter id="trackGlow">
<fegaussianblur result="coloredBlur" stddeviation="4"></fegaussianblur>
<femerge>
<femergenode in="coloredBlur"></femergenode>
<femergenode in="SourceGraphic"></femergenode>
</femerge>
</filter>
</defs>
<path d="M 120 145 C 320 145, 380 145, 500 145 C 620 145, 680 145, 880 145" fill="none" filter="url(#trackGlow)" opacity="0.4" stroke="#00d177" stroke-width="5"></path>
<path class="flow-dashed-line" d="M 120 145 C 320 145, 380 145, 500 145 C 620 145, 680 145, 880 145" fill="none" stroke="url(#trackGradient)" stroke-linecap="round" stroke-width="3"></path>
</svg>
<!-- STAGE NODE 1: MERCHANT STORE -->
<div class="relative z-20 flex flex-col items-center">
<div class="glow-pulse-box relative w-24 sm:w-32 h-28 sm:h-36 rounded-2xl bg-gradient-to-b from-[#1b221d] to-[#111413] border border-primary/40 flex flex-col items-center justify-between p-3.5 backdrop-blur-md">
<div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-primary/20 border border-primary/40">
<span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
<span class="text-[9px] font-mono font-bold text-primary uppercase">Open Store</span>
</div>
<svg class="w-12 h-12 text-primary filter drop-shadow-[0_0_12px_rgba(66,238,144,0.4)]" fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24">
<path d="M3 21h18M3 7v1a3 3 0 006 0V7m0 1a3 3 0 006 0V7m0 1a3 3 0 006 0V7H3l2-4h14l2 4M5 21V10.85a4.978 4.978 0 002-1.85m10 1.85V21" stroke-linecap="round" stroke-linejoin="round"></path>
<rect fill="#00d177" fill-opacity="0.25" height="5" rx="1" stroke="#42ee90" width="6" x="9" y="15"></rect>
</svg>
<div class="animate-float-package flex items-center gap-1 bg-[#090b0a] border border-primary/50 px-2 py-1 rounded-lg text-[10px] font-mono text-white shadow-[0_0_12px_rgba(0,209,119,0.3)]">
<span class="material-symbols-outlined text-primary text-xs" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
<span>Mzigo Tayari</span>
</div>
</div>
<span class="text-xs font-bold text-white mt-2.5">Merchant Store</span>
<span class="text-[10px] font-mono text-secondary/70">Duka la Muuzaji</span>
</div>
<!-- CENTRAL CONTINUOUS KINETIC RIDER -->
<div class="rider-dispatch-transit absolute top-[57%] z-30 pointer-events-none -translate-x-1/2 flex items-center justify-center">
<div class="relative flex items-center justify-center">
<div class="absolute left-28 w-32 h-14 bg-gradient-to-r from-primary/35 via-primary/10 to-transparent blur-md transform -translate-y-1 rotate-1 pointer-events-none"></div>
<svg class="w-32 sm:w-44 h-auto filter drop-shadow-[0_0_18px_rgba(0,209,119,0.7)]" fill="none" viewbox="0 0 160 120" xmlns="http://www.w3.org/2000/svg">
<circle cx="80" cy="65" fill="#00d177" fill-opacity="0.2" filter="blur(14px)" r="40"></circle>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="2.5" x1="12" x2="38" y1="52" y2="52"></line>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="2" x1="8" x2="42" y1="68" y2="68"></line>
<line class="speed-line" stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="18" x2="48" y1="84" y2="84"></line>
<circle cx="50" cy="85" fill="#09090b" r="16" stroke="#00d177" stroke-width="3.5"></circle>
<circle cx="50" cy="85" fill="#00d177" fill-opacity="0.3" r="7" stroke="#00d177" stroke-width="2"></circle>
<circle cx="120" cy="85" fill="#09090b" r="16" stroke="#00d177" stroke-width="3.5"></circle>
<circle cx="120" cy="85" fill="#00d177" fill-opacity="0.3" r="7" stroke="#00d177" stroke-width="2"></circle>
<path d="M 50 85 L 72 85 L 88 64 L 112 64 L 120 85" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<path d="M 72 85 L 82 54" stroke="#00d177" stroke-linecap="round" stroke-width="3"></path>
<path d="M 120 85 L 110 50 L 102 50" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<polygon class="animate-beam" fill="#afffd2" fill-opacity="0.45" points="118,53 150,44 154,66 119,57"></polygon>
<circle cx="117" cy="54" fill="#afffd2" r="3.5"></circle>
<rect fill="#00d177" fill-opacity="0.3" height="26" rx="4" stroke="#00d177" stroke-width="3" width="22" x="52" y="42"></rect>
<line stroke="#00d177" stroke-width="2" x1="52" x2="74" y1="50" y2="50"></line>
<path d="M 74 54 C 74 48, 86 46, 95 52 L 106 52" stroke="#00d177" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"></path>
<line stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="84" x2="88" y1="54" y2="70"></line>
<line stroke="#00d177" stroke-linecap="round" stroke-width="3" x1="88" x2="98" y1="70" y2="72"></line>
<circle cx="98" cy="38" fill="#09090b" r="8" stroke="#00d177" stroke-width="3"></circle>
<path d="M 100 36 Q 106 38 104 42" fill="none" stroke="#afffd2" stroke-linecap="round" stroke-width="2.5"></path>
</svg>
</div>
</div>
<!-- STAGE NODE 2: CUSTOMER DOORSTEP -->
<div class="relative z-20 flex flex-col items-center">
<div class="glow-pulse-box relative w-24 sm:w-32 h-28 sm:h-36 rounded-2xl bg-gradient-to-b from-[#1b221d] to-[#111413] border border-primary/40 flex flex-col items-center justify-between p-3.5 backdrop-blur-md">
<div class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary/20 border border-primary/40">
<span class="material-symbols-outlined text-[12px] text-primary" style="font-variation-settings: 'FILL' 1;">home</span>
<span class="text-[9px] font-mono font-bold text-primary uppercase">Doorstep</span>
</div>
<svg class="w-12 h-12 text-primary filter drop-shadow-[0_0_12px_rgba(66,238,144,0.4)]" fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24">
<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
<div class="animate-check-glow flex items-center gap-1 bg-primary text-[#00210e] px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold shadow-[0_0_15px_rgba(66,238,144,0.5)]">
<span class="material-symbols-outlined text-xs">done_all</span>
<span>Imefika!</span>
</div>
</div>
<span class="text-xs font-bold text-white mt-2.5">Customer Doorstep</span>
<span class="text-[10px] font-mono text-secondary/70">Mlangoni kwa Mteja</span>
</div>
</div>
<!-- Telemetry Bottom Bar -->
<div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-1 sm:grid-cols-3 gap-3">
<div class="flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 rounded-xl bg-white/[0.03] border border-white/5 text-xs text-secondary font-mono">
<span class="material-symbols-outlined text-primary text-base">timelapse</span>
<span>Muda wa Wastani: <strong class="text-white">12 - 15 Mins</strong></span>
</div>
<div class="flex items-center justify-center gap-2.5 px-4 py-2.5 rounded-xl bg-white/[0.03] border border-white/5 text-xs text-secondary font-mono">
<span class="material-symbols-outlined text-primary text-base">near_me</span>
<span>Ufuatiliaji wa Moja kwa Moja: <strong class="text-white">Live GPS</strong></span>
</div>
<div class="flex items-center justify-center sm:justify-end gap-2.5 px-4 py-2.5 rounded-xl bg-white/[0.03] border border-white/5 text-xs text-secondary font-mono">
<span class="material-symbols-outlined text-primary text-base">verified_user</span>
<span>Usalama &amp; Ufanisi: <strong class="text-white">100% Salama na Kasi</strong></span>
</div>
</div>
</div>
</section>
<!-- VALUE PROPOSITION STATS BAR -->
<section class="mb-20 glass-panel rounded-3xl p-8 md:p-10 border border-white/10 relative overflow-hidden select-none">
<div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-y md:divide-y-0 md:divide-x divide-white/10 text-center">
<div class="pt-4 md:pt-0">
<div class="text-3xl md:text-5xl font-black text-primary font-mono tracking-tight">&lt; 15 min</div>
<div class="text-xs md:text-sm font-semibold text-white mt-2">Muda Wa Delivery</div>
<div class="text-[11px] text-secondary mt-0.5">Uwasilishaji wa haraka popote mtaani</div>
</div>
<div class="pt-4 md:pt-0 md:pl-6">
<div class="text-3xl md:text-5xl font-black text-primary font-mono tracking-tight">TZ Ready</div>
<div class="text-xs md:text-sm font-semibold text-white mt-2">Moshi &amp; Dar es Salaam</div>
<div class="text-[11px] text-secondary mt-0.5">Mtandao mpana unaotanuka kote nchini</div>
</div>
<div class="pt-4 md:pt-0 md:pl-6">
<div class="text-3xl md:text-5xl font-black text-primary font-mono tracking-tight">120+</div>
<div class="text-xs md:text-sm font-semibold text-white mt-2">Active Couriers</div>
<div class="text-[11px] text-secondary mt-0.5">Madereva weledi na vifaa vya kisasa</div>
</div>
<div class="pt-4 md:pt-0 md:pl-6">
<div class="text-3xl md:text-5xl font-black text-primary font-mono tracking-tight">0.8s</div>
<div class="text-xs md:text-sm font-semibold text-white mt-2">Mobile Settlement</div>
<div class="text-[11px] text-secondary mt-0.5">M-Pesa, TigoPesa, Airtel Money &amp; Cash</div>
</div>
</div>
</section>
<!-- DOWNLOAD & CONTACT FOCUS FOOTER PANEL -->
<section class="glass-panel rounded-3xl p-8 md:p-12 border border-primary/30 relative overflow-hidden text-center flex flex-col items-center">
<div class="w-14 h-14 rounded-2xl bg-primary/15 border border-primary/40 flex items-center justify-center text-primary mb-4 shadow-[0_0_25px_rgba(66,238,144,0.3)]">
<span class="material-symbols-outlined text-3xl">bolt</span>
</div>
<h3 class="text-2xl md:text-3xl font-extrabold text-white mb-2">
      Tayari kuagiza sasa?
    </h3>
<p class="text-secondary text-sm md:text-base max-w-md mb-8">
      Pakua Patapoa App kwa urahisi zaidi, au wasiliana nasi moja kwa moja kupitia WhatsApp kwa namba ya usaidizi: <strong class="text-white">+255 715 080 235</strong>.
    </p>
<!-- STRICT ACTION BUTTONS (2 Allowed buttons duplicated for accessible footer call to action) -->
<div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full max-w-md">
<a class="btn-download-app w-full sm:w-auto flex-1 px-7 py-3.5 rounded-full font-bold text-sm flex items-center justify-center gap-2.5 text-[#00210e]" href="#download" title="Download Patapoa App">
<span class="material-symbols-outlined text-xl">install_mobile</span>
<span>Download App</span>
</a>
<a class="btn-whatsapp-support w-full sm:w-auto flex-1 px-7 py-3.5 rounded-full font-bold text-sm flex items-center justify-center gap-2.5" href="https://wa.me/255715080235?text=Habari%20Patapoa,%20nahitaji%20huduma%20ya%20kuletewa%20mzigo." rel="noopener noreferrer" target="_blank" title="Wasiliana nasi WhatsApp Hotline">
<svg class="w-5 h-5 fill-current text-primary" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"></path>
</svg>
<span>WhatsApp Support</span>
</a>
</div>
</section>
</main>
<!-- FOOTER (Attribution to NACCI SOFTLABS, Hotline & Strict Info) -->
<footer class="w-full bg-[#080909] border-t border-white/10 py-10 relative z-30 select-none">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row justify-between items-center gap-6 text-center md:text-left">
<div>
<div class="flex items-center justify-center md:justify-start gap-2">
<span class="font-extrabold text-lg text-primary">Patapoa</span>
<span class="text-xs text-secondary">• Agiza tukuletee</span>
</div>
<p class="text-xs text-secondary/70 mt-1">
        A proud technology product of <strong class="text-white">NACCI SOFTLABS</strong>
</p>
<p class="text-xs text-primary font-mono mt-0.5">
        Official Hotline: +255 715 080 235
      </p>
</div>
<div class="text-xs text-secondary/60">
      © 2025 Patapoa by NACCI SOFTLABS. Haki zote zimehifadhiwa.
    </div>
</div>
</footer>
</div>
<!-- ============================================================== -->
<!-- SCRIPT: SEQUENTIAL TIMELINE CONTROLLER (PURE VANILLA JS 60FPS)  -->
<!-- ============================================================== -->
<script>
  let introActive = true;
  let timelineTimers = [];

  function registerTimer(fn, delay) {
    const t = setTimeout(fn, delay);
    timelineTimers.push(t);
    return t;
  }

  function clearAllTimelineTimers() {
    timelineTimers.forEach(t => clearTimeout(t));
    timelineTimers = [];
  }

  function createParticleBurst() {
    const container = document.getElementById('particle-burst-box');
    if (!container) return;
    container.innerHTML = '';
    
    const count = 42;
    for (let i = 0; i < count; i++) {
      const p = document.createElement('div');
      p.className = 'disrupt-particle';
      
      const size = Math.random() * 9 + 3;
      p.style.width = `${size}px`;
      p.style.height = `${size}px`;
      
      const angle = Math.random() * Math.PI * 2;
      const distance = Math.random() * 360 + 80;
      const tx = Math.cos(angle) * distance;
      const ty = Math.sin(angle) * distance;
      
      p.style.setProperty('--tx', `${tx}px`);
      p.style.setProperty('--ty', `${ty}px`);
      
      const duration = Math.random() * 0.5 + 0.5;
      p.style.animation = `particleExplode ${duration}s cubic-bezier(0.12, 0.8, 0.33, 1) forwards`;
      
      container.appendChild(p);
    }
  }

  // Phase 1 -> Phase 2 -> Phase 3 Sequential Execution
  function startSequentialJourney() {
    if (!introActive) return;

    const ignitionBlock = document.getElementById('intro-phase-ignition');
    const journeyBlock = document.getElementById('intro-phase-journey');
    const riderWrap = document.getElementById('rider-wrap');
    const shockwave1 = document.getElementById('shockwave-ring-1');
    const shockwave2 = document.getElementById('shockwave-ring-2');

    // 1. Initial Rider Accelerates
    if (riderWrap) riderWrap.classList.add('accelerate');

    // 2. Disruption explosion & shockwaves
    registerTimer(() => {
      if (riderWrap) {
        riderWrap.classList.remove('accelerate');
        riderWrap.classList.add('disrupt');
      }
      createParticleBurst();
      if (shockwave1) shockwave1.classList.add('active');
      if (shockwave2) shockwave2.classList.add('active');
    }, 450);

    // 3. Seamless Transition to Full Delivery Motion Graphic Flow
    registerTimer(() => {
      if (ignitionBlock) {
        ignitionBlock.style.opacity = '0';
        ignitionBlock.style.transform = 'scale(0.9)';
        ignitionBlock.style.pointerEvents = 'none';
      }

      if (journeyBlock) {
        journeyBlock.classList.remove('intro-phase-hidden');
        journeyBlock.classList.add('intro-phase-visible');
      }

      // Start traveling delivery rider from Merchant Store (Duka)
      const travelRider = document.getElementById('intro-rider-traveler');
      const badge = document.getElementById('intro-journey-badge');
      const p1 = document.getElementById('intro-pill-1');
      const p2 = document.getElementById('intro-pill-2');
      const p3 = document.getElementById('intro-pill-3');

      // Step 2a: Pickup confirmed at Merchant
      if (badge) badge.innerText = "1. Mzigo Umechukuliwa Kutoka Dukani";
      
      // Step 2b: Rider starts journey along kinetic corridor
      registerTimer(() => {
        if (travelRider) travelRider.classList.add('moving');
        if (badge) badge.innerText = "2. Rider Safarini Kasi Kuelekea Kwako";
        if (p1 && p2) {
          p1.className = "flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all";
          p1.querySelector('span:first-child').className = "w-2 h-2 rounded-full bg-secondary/50";
          p1.querySelector('span:last-child').className = "text-[10px] sm:text-xs font-mono font-bold text-secondary/70";

          p2.className = "flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-primary/20 border border-primary/50 text-center shadow-[0_0_15px_rgba(66,238,144,0.3)] transition-all";
          p2.querySelector('span:first-child').className = "w-2 h-2 rounded-full bg-primary animate-ping";
          p2.querySelector('span:last-child').className = "text-[10px] sm:text-xs font-mono font-bold text-primary";
        }
      }, 550);

      // Step 2c: Rider arrives at Customer Doorstep
      registerTimer(() => {
        if (badge) badge.innerText = "3. Mzigo Umefika Mlangoni Kwako!";
        if (p2 && p3) {
          p2.className = "flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all";
          p2.querySelector('span:first-child').className = "w-2 h-2 rounded-full bg-secondary/50";
          p2.querySelector('span:last-child').className = "text-[10px] sm:text-xs font-mono font-bold text-secondary/70";

          p3.className = "flex items-center justify-center gap-2 py-2 px-2.5 rounded-xl bg-primary/25 border border-primary text-center shadow-[0_0_20px_rgba(66,238,144,0.4)] transition-all";
          p3.querySelector('span:first-child').className = "w-2 h-2 rounded-full bg-primary animate-pulse";
          p3.querySelector('span:last-child').className = "text-[10px] sm:text-xs font-mono font-bold text-primary";
        }

        // Trigger Spectacular Doorstep Portal Flash & Glow wipe
        const doorstepFlash = document.getElementById('intro-doorstep-flash');
        if (doorstepFlash) {
          doorstepFlash.classList.add('burst');
        }
      }, 2950);

      // Step 3: Spectacular generic awesome transition into full website experience
      registerTimer(() => {
        completeIntroReveal();
      }, 3650);

    }, 850);
  }

  // Smooth curtain reveal into full landing page
  function completeIntroReveal() {
    introActive = false;
    clearAllTimelineTimers();

    const overlay = document.getElementById('ignition-overlay');
    const mainRoot = document.getElementById('main-website-root');

    if (overlay) {
      overlay.classList.add('faded-out');
      registerTimer(() => {
        overlay.style.display = 'none';
      }, 850);
    }

    if (mainRoot) {
      mainRoot.classList.add('revealing');
    }
  }

  // Skip button handler
  function skipIntroSequence() {
    completeIntroReveal();
  }

  // Replay intro sequence from header button or logo click
  function replayFullIntro() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    introActive = true;
    clearAllTimelineTimers();

    const overlay = document.getElementById('ignition-overlay');
    const ignitionBlock = document.getElementById('intro-phase-ignition');
    const journeyBlock = document.getElementById('intro-phase-journey');
    const riderWrap = document.getElementById('rider-wrap');
    const travelRider = document.getElementById('intro-rider-traveler');
    const doorstepFlash = document.getElementById('intro-doorstep-flash');
    const timerElem = document.getElementById('ignite-timer');

    if (overlay) {
      overlay.style.display = 'flex';
      overlay.classList.remove('faded-out');
    }

    if (ignitionBlock) {
      ignitionBlock.style.opacity = '1';
      ignitionBlock.style.transform = 'scale(1)';
      ignitionBlock.style.pointerEvents = 'auto';
    }

    if (journeyBlock) {
      journeyBlock.classList.add('intro-phase-hidden');
      journeyBlock.classList.remove('intro-phase-visible');
    }

    if (riderWrap) {
      riderWrap.classList.remove('accelerate', 'disrupt');
    }

    if (travelRider) {
      travelRider.classList.remove('moving');
    }

    if (doorstepFlash) {
      doorstepFlash.classList.remove('burst');
    }

    if (timerElem) timerElem.innerText = '0.6';

    registerTimer(() => {
      startSequentialJourney();
    }, 600);
  }

  // Synchronize in-page dispatch section tracking pills
  function initInPageDispatchTracker() {
    const p1 = document.getElementById('status-pill-1');
    const p2 = document.getElementById('status-pill-2');
    const p3 = document.getElementById('status-pill-3');
    if (!p1 || !p2 || !p3) return;

    let phase = 0;
    setInterval(() => {
      phase = (phase + 1) % 3;
      [p1, p2, p3].forEach(p => {
        p.className = "flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-white/[0.04] border border-white/10 text-center transition-all";
        const dot = p.querySelector('span:first-child');
        const txt = p.querySelector('span:last-child');
        if (dot) dot.className = "w-2 h-2 rounded-full bg-secondary/50";
        if (txt) txt.className = "text-[11px] sm:text-xs font-mono font-bold text-secondary/70 tracking-wide";
      });

      const active = [p1, p2, p3][phase];
      if (active) {
        active.className = "flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-primary/15 border border-primary/40 text-center shadow-[0_0_15px_rgba(66,238,144,0.25)] transition-all";
        const dot = active.querySelector('span:first-child');
        const txt = active.querySelector('span:last-child');
        if (dot) dot.className = "w-2 h-2 rounded-full bg-primary animate-pulse";
        if (txt) txt.className = "text-[11px] sm:text-xs font-mono font-bold text-primary tracking-wide";
      }
    }, 3000);
  }

  // Initialization on load
  window.addEventListener('DOMContentLoaded', () => {
    initInPageDispatchTracker();

    // Auto-launch ignition sequence after brief cinematic countdown (1.0s)
    let timeLeft = 1.0;
    const timerElem = document.getElementById('ignite-timer');
    
    const interval = setInterval(() => {
      timeLeft = Math.max(0, +(timeLeft - 0.1).toFixed(1));
      if (timerElem) timerElem.innerText = timeLeft.toString();
      if (timeLeft <= 0) {
        clearInterval(interval);
        startSequentialJourney();
      }
    }, 100);

    // Interactive 3D Mouse Tilt on Hero Glass Card
    const heroCard = document.getElementById('hero-glass-card');
    const container = document.getElementById('hero-card-container');

    if (container && heroCard) {
      container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left - (rect.width / 2);
        const y = e.clientY - rect.top - (rect.height / 2);

        const rotateX = -(y / rect.height) * 16;
        const rotateY = (x / rect.width) * 16;

        heroCard.style.transform = `rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.02, 1.02, 1.02)`;
      });

      container.addEventListener('mouseleave', () => {
        heroCard.style.transform = 'rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
      });
    }
  });
</script>
</body></html>