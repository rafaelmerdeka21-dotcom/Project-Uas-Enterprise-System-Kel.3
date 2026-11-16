<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">
    <title>@yield('title', 'Dashboard') - EMS Dagang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0F172A',
                        accent: '#4F46E5',
                        'accent-hover': '#4338CA',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        .bottom-tab-bar { padding-bottom: env(safe-area-inset-bottom, 0px); }
        .tab-item { -webkit-tap-highlight-color: transparent; tap-highlight-color: transparent; }

        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: #fff !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full text-slate-700 bg-slate-50"
      x-data="{ profileMenuOpen: false }"
      @keydown.escape.window="profileMenuOpen = false">
    @php
        $isAdmin = auth()->user()->isAdmin();
        $primaryNavigation = $isAdmin
            ? [
                ['label' => 'Beranda', 'route' => 'dashboard', 'active' => ['dashboard'], 'icon' => 'home'],
                ['label' => 'Kasir', 'route' => 'sales.create', 'active' => ['sales.create'], 'icon' => 'cashier'],
                ['label' => 'Penjualan', 'route' => 'sales.index', 'active' => ['sales.index', 'sales.show'], 'icon' => 'history'],
                ['label' => 'Barang', 'route' => 'products.index', 'active' => ['products.*'], 'icon' => 'box'],
                ['label' => 'Laporan', 'route' => 'reports.index', 'active' => ['reports.*'], 'icon' => 'chart'],
            ]
            : [
                ['label' => 'Kasir', 'route' => 'sales.create', 'active' => ['sales.create'], 'icon' => 'cashier'],
                ['label' => 'Riwayat', 'route' => 'sales.index', 'active' => ['sales.index', 'sales.show'], 'icon' => 'history'],
                ['label' => 'Katalog', 'route' => 'products.catalog', 'active' => ['products.catalog'], 'icon' => 'box'],
            ];
    @endphp

    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur no-print">
        <div class="mx-auto flex h-14 max-w-screen-2xl items-center justify-between gap-3 px-4 sm:px-6 lg:h-16 lg:px-8">
            <a href="{{ $isAdmin ? route('dashboard') : route('sales.create') }}" class="flex shrink-0 items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </span>
                <span class="text-sm font-bold tracking-tight text-slate-900 sm:text-base">EMS Dagang</span>
            </a>

            <nav aria-label="Navigasi utama" class="hidden flex-1 items-center justify-center gap-1 lg:flex">
                @foreach($primaryNavigation as $item)
                    @php($isActive = request()->routeIs(...$item['active']))
                    <a href="{{ route($item['route']) }}"
                       @if($isActive) aria-current="page" @endif
                       class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ $isActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="relative shrink-0" @click.outside="profileMenuOpen = false">
                <button type="button"
                        @click="profileMenuOpen = !profileMenuOpen"
                        :aria-expanded="profileMenuOpen.toString()"
                        aria-label="Buka menu akun"
                        class="flex h-10 items-center gap-2 rounded-xl p-1.5 pr-2.5 transition hover:bg-slate-100">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden max-w-32 text-left sm:block">
                        <span class="block truncate text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                        <span class="block text-[10px] text-slate-400">{{ $isAdmin ? 'Administrator' : 'Staf Kasir' }}</span>
                    </span>
                    <svg class="hidden h-4 w-4 text-slate-400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div x-cloak x-show="profileMenuOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="translate-y-1 opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="translate-y-0 opacity-100"
                     x-transition:leave-end="translate-y-1 opacity-0"
                     class="absolute right-0 top-full mt-2 w-64 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                    <div class="border-b border-slate-100 px-2 pb-3">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        <span class="mt-2 inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $isAdmin ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                            {{ $isAdmin ? 'Administrator' : 'Staf Kasir' }}
                        </span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar dari Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto w-full max-w-screen-2xl px-4 pt-5 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-3 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm text-emerald-800" role="alert">
                <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-3 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-800" role="alert">
                <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-3 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3.5 text-sm text-amber-800" role="alert">
                <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">{{ session('warning') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-3 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-800">
                <div class="mb-1 flex items-center gap-2 font-semibold">
                    <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Terdapat kesalahan pada input:
                </div>
                <ul class="list-inside list-disc space-y-0.5 text-xs text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <main class="mx-auto w-full max-w-screen-2xl flex-1 px-4 pb-[calc(6rem+env(safe-area-inset-bottom))] sm:px-6 lg:px-8 lg:pb-8">
        <div class="mb-5">
            <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">@yield('page-title', 'Enterprise Management System')</h1>
            <p class="mt-1 text-sm text-slate-500">@yield('page-subtitle', 'Sistem Manajemen Terpadu Usaha Dagang')</p>
        </div>
        @yield('content')
    </main>

    <nav aria-label="Navigasi bawah" class="bottom-tab-bar fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 shadow-[0_-4px_18px_rgba(15,23,42,0.06)] backdrop-blur lg:hidden no-print">
        <div class="mx-auto grid h-16 max-w-screen-sm" style="grid-template-columns: repeat({{ count($primaryNavigation) }}, minmax(0, 1fr));">
            @foreach($primaryNavigation as $item)
                @php($isActive = request()->routeIs(...$item['active']))
                <a href="{{ route($item['route']) }}"
                   @if($isActive) aria-current="page" @endif
                   class="tab-item flex min-w-0 flex-col items-center justify-center gap-0.5 text-[10px] font-medium leading-tight transition-colors {{ $isActive ? 'text-indigo-700' : 'text-slate-500 hover:text-slate-800' }}">
                    <span class="flex h-8 min-w-11 items-center justify-center rounded-2xl transition-colors {{ $isActive ? 'bg-indigo-50' : '' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            @if($item['icon'] === 'home')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 10 9-7 9 7M5 9v11h14V9M9 20v-6h6v6"/>
                            @elseif($item['icon'] === 'cashier')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h14v16H5zM8 8h8M8 12h2m4 0h2m-8 4h2m4 0h2"/>
                            @elseif($item['icon'] === 'history')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 4h10a2 2 0 012 2v14H6a2 2 0 01-2-2V6a2 2 0 012-2m4 0v4h4V4M9 13h6m-6 3h6"/>
                            @elseif($item['icon'] === 'chart')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V5m0 14h16M8 15v-4m4 4V7m4 8v-6"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 9 4.5v9L12 21l-9-4.5v-9L12 3zm0 9 9-4.5M12 12 3 7.5m9 4.5v9"/>
                            @endif
                        </svg>
                    </span>
                    <span class="truncate px-1">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
