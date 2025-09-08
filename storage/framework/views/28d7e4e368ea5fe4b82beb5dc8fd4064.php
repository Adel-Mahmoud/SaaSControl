<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>العيادة الطبية - أهلاً وسهلاً</title>
    <meta name="description" content="عيادة طبية متخصصة تقدم أفضل الخدمات الطبية برعاية صحية شاملة ومتميزة">
    <meta name="author" content="العيادة الطبية">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="العيادة الطبية - أهلاً وسهلاً">
    <meta property="og:description" content="عيادة طبية متخصصة تقدم أفضل الخدمات الطبية برعاية صحية شاملة ومتميزة">
    <meta property="og:type" content="website">
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <style>
        /* Medical Clinic Welcome Page Styles */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    direction: rtl;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    height: 100vh;
    overflow: hidden;
}

/* Hero Background */
.hero-container {
    min-height: 100vh;
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.1), rgba(76, 175, 80, 0.1)), 
                url('https://thumbs.dreamstime.com/b/doctor-medical-background-24834402.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

/* Welcome Card */
.welcome-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1.5rem;
    padding: 3rem;
    max-width: 42rem;
    width: 100%;
    text-align: center;
    box-shadow: 0 25px 50px -12px rgba(33, 150, 243, 0.25);
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.welcome-card.fade-in {
    opacity: 1;
    transform: translateY(0);
}

/* Medical Icon */
.icon-container {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
    opacity: 0;
    transform: scale(0.9);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.icon-container.scale-in {
    opacity: 1;
    transform: scale(1);
}

.icon-bg {
    background: rgba(33, 150, 243, 0.1);
    padding: 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stethoscope-icon {
    width: 4rem;
    height: 4rem;
    color: #2196F3;
}

/* Typography */
.main-title {
    font-size: 3rem;
    font-weight: bold;
    color: #2196F3;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.sub-title {
    font-size: 1.875rem;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 2rem;
}

.description {
    font-size: 1.25rem;
    color: #718096;
    margin-bottom: 3rem;
    line-height: 1.6;
}

/* Features Section */
.features-container {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 3rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.features-container.fade-in {
    opacity: 1;
    transform: translateY(0);
}

.feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.feature-icon {
    width: 2rem;
    height: 2rem;
    margin-bottom: 0.5rem;
}

.heart-icon {
    color: #EF4444;
}

.shield-icon {
    color: #10B981;
}

.medical-icon {
    color: #3B82F6;
}

.feature-text {
    font-size: 0.875rem;
    color: #718096;
}

/* CTA Button */
.cta-button {
    background: linear-gradient(135deg, #2196F3, #03A9F4);
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
    padding: 1rem 3rem;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 0 40px rgba(33, 150, 243, 0.3);
    margin-bottom: 2rem;
    font-family: inherit;
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 35px 60px -12px rgba(33, 150, 243, 0.4);
}

.cta-button:active {
    transform: translateY(0);
}

/* Glow Animation */
.cta-button.glow-animation {
    animation: glow 2s ease-in-out infinite;
}

@keyframes glow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(33, 150, 243, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(33, 150, 243, 0.6);
    }
}

/* Footer Text */
.footer-text {
    color: #a0aec0;
    font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-card {
        padding: 2rem;
        margin: 1rem;
    }
    
    .main-title {
        font-size: 2.25rem;
    }
    
    .sub-title {
        font-size: 1.5rem;
    }
    
    .description {
        font-size: 1.125rem;
    }
    
    .features-container {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .cta-button {
        font-size: 1.125rem;
        padding: 0.875rem 2.5rem;
    }
}

@media (max-width: 480px) {
    .welcome-card {
        padding: 1.5rem;
    }
    
    .main-title {
        font-size: 2rem;
    }
    
    .sub-title {
        font-size: 1.25rem;
    }
    
    .description {
        font-size: 1rem;
        margin-bottom: 2rem;
    }
    
    .features-container {
        margin-bottom: 2rem;
    }
    
    .cta-button {
        font-size: 1rem;
        padding: 0.75rem 2rem;
    }
}

/* Animation Classes */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Smooth scrolling for internal links */
html {
    scroll-behavior: smooth;
}

/* Focus styles for accessibility */
.cta-button:focus {
    outline: 2px solid #2196F3;
    outline-offset: 2px;
}

/* Print styles */
@media print {
    .hero-container {
        background: white;
        color: black;
    }
    
    .welcome-card {
        background: white;
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .cta-button {
        background: #2196F3;
        color: white;
    }
}
    </style>
    <!-- <link rel="stylesheet" href="style.css"> -->
</head>
<body>
    <main class="hero-container">
        <!-- Main Welcome Card -->
        <div class="welcome-card">
            <!-- Medical Icon -->
            <div class="icon-container">
                <div class="icon-bg">
                    <svg class="stethoscope-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4.8 2.3A.3.3 0 105 2H4.8z"/>
                        <path d="M7 3V2a1 1 0 00-1-1H4a1 1 0 00-1 1v1h4z"/>
                        <path d="M7 3a1 1 0 011 1v5h-1a3 3 0 00-3 3v1a3 3 0 003 3h1"/>
                        <path d="M17 3a1 1 0 00-1-1h-2a1 1 0 00-1 1v1h4V3z"/>
                        <path d="M17 3a1 1 0 011 1v5h1a3 3 0 013 3v1a3 3 0 01-3 3h-1"/>
                        <path d="M7 21a4 4 0 100-8 4 4 0 000 8z"/>
                        <path d="M17 21a4 4 0 100-8 4 4 0 000 8z"/>
                        <path d="M12 12h0"/>
                    </svg>
                </div>
            </div>

            <!-- Welcome Message -->
            <h1 class="main-title">أهلاً وسهلاً بكم</h1>
            <h2 class="sub-title">في عيادتنا الطبية</h2>
            <p class="description">
                نقدم لكم أفضل الخدمات الطبية المتخصصة برعاية صحية شاملة ومتميزة. 
                صحتكم هي أولويتنا وسعادتكم هي هدفنا.
            </p>

            <!-- Features Icons -->
            <div class="features-container">
                <div class="feature-item">
                    <svg class="feature-icon heart-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                    </svg>
                    <span class="feature-text">رعاية متخصصة</span>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span class="feature-text">أمان وثقة</span>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon medical-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4.8 2.3A.3.3 0 105 2H4.8z"/>
                        <path d="M7 3V2a1 1 0 00-1-1H4a1 1 0 00-1 1v1h4z"/>
                        <path d="M7 3a1 1 0 011 1v5h-1a3 3 0 00-3 3v1a3 3 0 003 3h1"/>
                        <path d="M17 3a1 1 0 00-1-1h-2a1 1 0 00-1 1v1h4V3z"/>
                        <path d="M17 3a1 1 0 011 1v5h1a3 3 0 013 3v1a3 3 0 01-3 3h-1"/>
                        <path d="M7 21a4 4 0 100-8 4 4 0 000 8z"/>
                        <path d="M17 21a4 4 0 100-8 4 4 0 000 8z"/>
                        <path d="M12 12h0"/>
                    </svg>
                    <span class="feature-text">خبرة عالية</span>
                </div>
            </div>

            <!-- Main CTA Button -->
            <button class="cta-button" onclick="enterSystem()">
                دخول إلى النظام
            </button>

            <!-- Footer Text -->
            <p class="footer-text">نحن هنا لخدمتكم على مدار الساعة</p>
        </div>
    </main>

    <script>
        function enterSystem() {
            window.location.href = '/admin';
        }

        window.addEventListener('load', function() {
            const card = document.querySelector('.welcome-card');
            const icon = document.querySelector('.icon-container');
            const features = document.querySelector('.features-container');
            const button = document.querySelector('.cta-button');
            
            setTimeout(() => {
                card.classList.add('fade-in');
            }, 100);
            
            setTimeout(() => {
                icon.classList.add('scale-in');
            }, 300);
            
            setTimeout(() => {
                features.classList.add('fade-in');
            }, 600);
            
            setTimeout(() => {
                button.classList.add('glow-animation');
            }, 900);
        });
    </script>
</body>
</html><?php /**PATH C:\laragon\www\clinic\resources\views/welcome.blade.php ENDPATH**/ ?>