<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHF Report - Integrated Healthcare Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary: #64748b;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #475569;
            --white: #ffffff;
            --glass: rgba(255, 255, 255, 0.8);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.5;
        }

        /* Hero Background Gradient */
        .bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 700px;
            background: radial-gradient(circle at top right, #dbeafe, transparent 50%),
                radial-gradient(circle at top left, #eff6ff, transparent 50%);
            z-index: -1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Navbar */
        nav {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .logo span {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white) !important;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-outline {
            border: 1px solid #e2e8f0;
            background: transparent;
            color: var(--text-main) !important;
        }

        .btn-outline:hover {
            background: #f1f5f9;
        }

        /* Hero */
        header {
            padding-top: 100px;
            padding-bottom: 80px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 1rem;
            background: #dbeafe;
            color: var(--primary);
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto 2.5rem;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        /* Features */
        .features {
            padding-bottom: 120px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
            border-color: #dbeafe;
        }

        .icon-box {
            width: 56px;
            height: 56px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .card p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }

            .hero-sub {
                font-size: 1.1rem;
            }

            .nav-links {
                display: none;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate {
            animation: fadeIn 0.8s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.2s;
        }

        .delay-3 {
            animation-delay: 0.3s;
        }
    </style>
</head>

<body>
    <div class="bg-gradient"></div>

    <nav>
        <div class="container" style="display: flex; width: 100%; align-items: center; justify-content: space-between;">
            <div class="logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="PHF Report Logo"
                    style="height: 48px; width: auto; object-fit: contain;">
                <span>PHF Report</span>
            </div>

            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <header class="container">
        <span class="badge animate">Premium Management Suite</span>
        <h1 class="animate delay-1">Integrated Public Health <br>Facility Management</h1>
        <p class="hero-sub animate delay-2">
            Streamline your facility operations with unified attendance tracking, automated financial reporting, and
            seamless subscription management.
        </p>
        <div class="hero-actions animate delay-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">Access Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Sign Up Now</a>
                <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Login Access</a>
            @endauth
        </div>
    </header>

    <section class="features container">
        <div class="section-title">
            <h2>Everything you need</h2>
            <p style="color: var(--secondary)">A comprehensive toolkit designed for healthcare facilities.</p>
        </div>

        <div class="grid">
            <div class="card animate delay-1">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <h3>Attendance Tracking</h3>
                <p>Digital check-ins and real-time headcounts at every entry point. Manage guests and members
                    effortlessly.</p>
            </div>

            <div class="card animate delay-2">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <h3>Financial Reporting</h3>
                <p>Track revenue from tickets, subscriptions, and services. Generate detailed shift reports for audits.
                </p>
            </div>

            <div class="card animate delay-3">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                </div>
                <h3>Ticket & Sub Management</h3>
                <p>Advanced ticket redemption system with unique codes and status tracking for all service levels.</p>
            </div>
        </div>
    </section>

    <footer
        style="padding: 40px 0; border-top: 1px solid #e2e8f0; text-align: center; color: var(--secondary); font-size: 0.875rem;">
        <div class="container">
            <p>&copy; {{ date('Y') }} PHF Report. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>