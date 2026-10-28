<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedro's Artisan One-Stop Shop - @yield('title', 'Dashboard')</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D4AF37',
                        'primary-foreground': '#0B0B0B',
                        card: '#121212',
                        muted: '#1A1A1A',
                        'muted-foreground': '#D1D1D1',
                        border: '#2A2A2A',
                        input: '#1A1A1A'
                    }
                }
            }
        }
    </script>
    <style>
        .bg-primary { background-color: #D4AF37 !important; color: #0B0B0B !important; }
        .text-primary { color: #D4AF37 !important; }
        .bg-card { background-color: #121212 !important; color: #FFFFFF !important; }
        .bg-muted { background-color: #1A1A1A !important; }
        .text-muted-foreground { color: #D1D1D1 !important; }
        .border-border { border-color: #2A2A2A !important; }
        .bg-input { background-color: #1A1A1A !important; color: #FFFFFF !important; }
    </style>
</head>
<body class="bg-dark text-white font-sans" style="background-color: var(--background, #0B0B0B); color: var(--foreground, #FFFFFF);">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-sidebar text-white hidden md:flex flex-col border-r border-secondary" style="background-color: #0f0f0f; border-color: #2a2a2a;">
            <div class="p-6 border-b border-zinc-800">
                <h2 class="text-xl font-bold tracking-wider" style="color: var(--primary, #D4AF37); font-family: var(--font-bebas, sans-serif);">PEDRO'S ARTISAN</h2>
                <p class="text-xs opacity-70">One-Stop Shop</p>
            </div>
            <nav class="flex-1 px-4 space-y-2 mt-4">
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Admin Overview
                    </a>
                    <a href="{{ route('admin.appointments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Appointments
                    </a>
                    <a href="{{ route('admin.pos') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Point Of Sale
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Services
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Customers
                    </a>
                @elseif(auth()->check() && auth()->user()->isStaff())
                    <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Staff Dashboard
                    </a>
                    <a href="{{ route('staff.appointments') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Appointments
                    </a>
                @else
                    <a href="{{ route('customer.home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Home
                    </a>
                    <a href="{{ route('customer.book') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                        Book Appointment
                    </a>
                @endif
                <div class="border-t border-zinc-800 my-4 pt-4"></div>
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white transition-all font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-black shadow-lg transition-all font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Sign In
                    </a>
                @endauth
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto" style="background-color: var(--background, #0B0B0B);">
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-900/40 border border-green-600 text-green-300 p-4 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-900/40 border border-red-600 text-red-300 p-4 rounded-lg mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
