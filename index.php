<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once("partials/header.php");
require_once("partials/nav.php");
?>

<!-- Add animation libraries in your header.php file or include them here -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<!-- Hero Section with Parallax Effect and Animated Elements -->
<div class="hero-container position-relative overflow-hidden" style="height: 100vh;">
    <!-- Parallax Background with Zoom Effect -->
    <div class="parallax-bg position-absolute w-100 h-100" 
         style="background: url('public/img/cover.jpg') no-repeat center center/cover; 
                transform-style: preserve-3d;
                animation: slowZoom 15s infinite alternate;">
    </div>
    
    <!-- Animated Particles Background -->
    <div id="particles-js" class="position-absolute w-100 h-100"></div>
    
    <!-- Dark Overlay with Gradient -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.5) 100%);"></div>

    <!-- Floating Elements -->
    <div class="floating-elements">
        <div class="float-element position-absolute" style="left: 15%; top: 20%; animation: float 8s ease-in-out infinite;">
            <i class="fas fa-star text-warning" style="font-size: 2rem; opacity: 0.7;"></i>
        </div>
        <div class="float-element position-absolute" style="right: 20%; top: 25%; animation: float 6s ease-in-out infinite 1s;">
            <i class="fas fa-building text-light" style="font-size: 2.5rem; opacity: 0.5;"></i>
        </div>
        <div class="float-element position-absolute" style="left: 25%; bottom: 25%; animation: float 7s ease-in-out infinite 0.5s;">
            <i class="fas fa-map-marker-alt text-danger" style="font-size: 2.2rem; opacity: 0.6;"></i>
        </div>
        <div class="float-element position-absolute" style="right: 15%; bottom: 30%; animation: float 9s ease-in-out infinite 1.5s;">
            <i class="fas fa-plane text-info" style="font-size: 2.3rem; opacity: 0.6;"></i>
        </div>
    </div>

    <!-- Content with Animations -->
    <div class="d-flex align-items-center justify-content-center h-100 text-white text-center">
        <div class="content-wrapper position-relative" data-aos="fade-up" data-aos-duration="1000">
            <h1 class="fw-bold display-4 mb-4 animate__animated animate__fadeInDown">
                Find Your <span class="text-gradient">Perfect Stay</span>
            </h1>
            <p class="lead mx-auto animate__animated animate__fadeIn animate__delay-1s" 
               style="max-width: 600px; animation-delay: 0.5s;">
                Explore the best hotels at unbeatable prices. Book now and experience 
                <span class="typing-text">luxury and comfort</span> like never before!
            </p>
            <div class="mt-5 animate__animated animate__fadeInUp animate__delay-2s" style="animation-delay: 1s;">
                <a href="views/destinations.php" class="btn btn-lg fw-bold shadow-lg px-4 py-3 pulse-btn" 
                   style="background: linear-gradient(45deg, #e8b923 0%, #ffd700 100%); 
                          border: none; 
                          border-radius: 30px; 
                          transition: all 0.3s;
                          position: relative;
                          overflow: hidden;">
                    <span class="btn-content d-flex align-items-center justify-content-center">
                        Book Now 
                        <i class="fas fa-arrow-right ms-2 btn-icon"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Animations -->
<style>
    /* Slow Zoom Animation */
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    
    /* Floating Animation */
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }
    
    @keyframes typing {
        0% { width: 0; }
        30% { width: 100%; }
        80% { width: 100%; }
        90% { width: 0; }
        100% { width: 0; }
    }
    
    @keyframes cursor-blink {
        50% { border-color: transparent; }
    }
    
    /* Button Animation */
    .pulse-btn {
        animation: pulse 2s infinite;
    }
    
    .pulse-btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 10px 25px rgba(232, 185, 35, 0.5) !important;
    }
    
    .btn-icon {
        transition: all 0.3s;
    }
    
    .pulse-btn:hover .btn-icon {
        transform: translateX(5px);
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(232, 185, 35, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(232, 185, 35, 0); }
        100% { box-shadow: 0 0 0 0 rgba(232, 185, 35, 0); }
    }
    
    /* Mouse Scroll Animation */
    .mouse {
        width: 26px;
        height: 40px;
        border: 2px solid rgba(255, 255, 255, 0.7);
        border-radius: 20px;
        margin: 0 auto 10px;
        position: relative;
    }
    
    .wheel {
        width: 4px;
        height: 8px;
        background: #fff;
        border-radius: 2px;
        position: absolute;
        top: 6px;
        left: 50%;
        transform: translateX(-50%);
        animation: scroll 1.5s infinite;
    }
    
    @keyframes scroll {
        0% { transform: translateX(-50%) translateY(0); opacity: 1; }
        100% { transform: translateX(-50%) translateY(15px); opacity: 0; }
    }
    
    /* Text Gradient */
    .text-gradient {
        background: linear-gradient(45deg, #e8b923, #ffffff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }
    
    /* Search Button Animation */
    .search-btn {
        transition: all 0.3s;
    }
    
    .search-btn:hover {
        background-color: #e8b923 !important;
        color: white;
        transform: scale(1.05);
    }
</style>

<!-- Add JavaScript for the animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
<script>
    // Initialize AOS
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init();
        
        // Initialize particles.js
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#ffffff" },
                shape: { type: "circle" },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#ffffff",
                    opacity: 0.2,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" }
                },
                modes: {
                    repulse: { distance: 100, duration: 0.4 },
                    push: { particles_nb: 4 }
                }
            }
        });
    });
</script>

<?php require_once("partials/footer.php"); ?>