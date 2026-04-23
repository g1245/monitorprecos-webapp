<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Monitor de Preços - Encontre os melhores preços!')</title>
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicon/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ Vite::asset('resources/images/favicon/site.webmanifest') }}" />
    <meta name="description" content="@yield('description', 'Compare preços de produtos de lojas virtuais de todo o Brasil e encontre as melhores ofertas.')">
    @stack('meta')
    @include('partials.tracking.meta-pixel')
    @include('partials.tracking.ga4')
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    @php
        $isMobile = (bool) preg_match(
            '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',
            request()->userAgent() ?? ''
        );
    @endphp

    <!-- Header -->
    <header class="bg-primary shadow-md">
        <div class="container mx-auto px-4">
            @if($isMobile)
                <!-- Mobile Layout -->
                <div class="py-3">
                    <!-- Top Row: Logo + Account -->
                    <div class="flex items-center justify-between mb-3">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <a href="/" title="Monitor de Preços">
                                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Monitor de Preços" class="h-8">
                            </a>
                        </div>

                        <!-- Account Icon -->
                        <div class="text-white">
                            @auth
                                <a href="{{ route('account.dashboard') }}" class="cursor-pointer hover:text-yellow-300 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('auth.login') }}" class="cursor-pointer hover:text-yellow-300 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Bottom Row: Search -->
                    <div>
                        <form action="{{ route('search.index') }}" method="GET" class="relative">
                            <input type="search" 
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Buscar produtos ou departamentos" 
                                   class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                   required>
                            <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Desktop Layout -->
                <div class="flex items-center justify-between py-3">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" title="Monitor de Preços">
                            <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Monitor de Preços" class="h-10">
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex-1 max-w-2xl mx-8">
                        <form action="{{ route('search.index') }}" method="GET" class="relative">
                            <input type="search" 
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Buscar produtos ou departamentos" 
                                   class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                   required>
                            <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- User Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Login/Account -->
                        <div class="text-white text-sm">
                            @auth
                                <a href="{{ route('account.dashboard') }}" class="flex items-center space-x-1 cursor-pointer hover:text-blue-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Olá, {{ Auth::user()->name }}</div>
                                        <div class="text-xs">Minha conta</div>
                                    </div>
                                </a>
                            @else
                                <a href="{{ route('auth.login') }}" class="flex items-center space-x-1 cursor-pointer hover:text-blue-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Olá, faça seu login</div>
                                        <div class="text-xs">ou cadastre-se</div>
                                    </div>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="bg-blue-800 text-white shadow-lg relative">
        <div class="container mx-auto px-4">
            @if($isMobile)
                <!-- Mobile Layout -->
                <div class="py-3">
                    <button id="departmentsBtn" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-blue-600 rounded hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <span class="font-medium">Navegue por aqui</span>
                        <svg id="chevronIcon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            @else
                <!-- Desktop Layout -->
                <div class="flex items-center justify-between py-3">
                    <!-- Departments Menu -->
                    <div class="flex items-center space-x-8">
                        <div class="relative">
                            <button id="departmentsBtnDesktop" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 rounded hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <span class="font-medium">Navegue por departamentos</span>
                                <svg id="chevronIconDesktop" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Menu Items -->
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('stores.index') }}" class="flex items-center space-x-1 hover:text-blue-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <span>Lojas</span>
                            </a>
                            <a href="{{ route('destaques.index') }}" class="flex items-center space-x-1 hover:text-yellow-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <span>Destaques</span>
                            </a>
                            <a href="{{ route('pages.how') }}" class="flex items-center space-x-1 hover:text-blue-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Como Funciona</span>
                            </a>
                            <a href="{{ route('pages.grupo') }}" class="flex items-center space-x-1 hover:text-green-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                                <span>Grupo WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if(!$isMobile)
            @include('partials.departments-menu-desktop')
        @endif
    </nav>

    @if($isMobile)
        @include('partials.departments-menu-mobile')
    @endif

    <!-- Promo Banner -->
    <div class="bg-blue-900 text-white py-2">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-sm">O Monitor de Preços você encontra as melhores ofertas do Brasil.</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Monitor de Preços</h3>
                    <p class="text-gray-300 text-sm mb-4">
                        Encontre as melhores ofertas de produtos em lojas virtuais de todo o Brasil. Compare preços, acompanhe histórico e economize!
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="{{ route('pages.about') }}" class="hover:text-blue-500 transition-colors">Sobre Nós</a></li>
                        <li><a href="{{ route('pages.how') }}" class="hover:text-blue-500 transition-colors">Como Funciona</a></li>
                        <li><a href="{{ route('pages.grupo') }}" class="hover:text-blue-500 transition-colors">Grupo no WhatsApp</a></li>
                        <li><a href="{{ route('stores.index') }}" class="hover:text-blue-500 transition-colors">Lojas Parceiras</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="font-semibold mb-4">Atalhos</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="/search?q=tenis+corrida+nike" class="hover:text-blue-500 transition-colors">Tênis NIke</a></li>
                        <li><a href="/search?q=tenis+corrida+adidas" class="hover:text-blue-500 transition-colors">Tênis Adidas</a></li>
                        <li><a href="/search?q=tenis+corrida+asics" class="hover:text-blue-500 transition-colors">Tênis Asics</a></li>
                        <li><a href="/search?q=tenis+corrida+olympikus" class="hover:text-blue-500 transition-colors">Tênis Olympikus</a></li>
                        <li><a href="/search?q=roupa+corrida" class="hover:text-blue-500 transition-colors">Roupa para Corrida</a></li>
                    </ul>
                </div>

                <!-- Help -->
                <div>
                    <h4 class="font-semibold mb-4">Ajuda</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="{{ route('pages.help-center') }}" class="hover:text-blue-400 transition-colors">Central de Ajuda</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-blue-400 transition-colors">Política de Privacidade</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-blue-400 transition-colors">Termos de Uso</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-blue-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p class="mb-2">&copy; {{ date('Y') }} Monitor de Preços. Todos os direitos reservados. Group 1245 LTDA - 52.171.773/0001-34</p>
                <p>AVISO LEGAL: Somos um site de publicidade gratuito. O uso está condicionado à aceitação de nossa Política de Privacidade e Termos de Uso. Preços e disponibilidade podem variar a qualquer momento sem aviso prévio; consulte sempre a loja antes de comprar. Não utilizamos drop cookie ou cookie stuffing. Nossa receita é gerada por comissões em acordos comerciais com algumas varejistas parceiras.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($isMobile)
                const departmentsBtn = document.getElementById('departmentsBtn');
                const mobileDepartmentsMenu = document.getElementById('mobileDepartmentsMenu');
                const closeMobileMenu = document.getElementById('closeMobileMenu');
                const chevronIcon = document.getElementById('chevronIcon');
                let isMenuOpen = false;

                if (departmentsBtn) {
                    departmentsBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        isMenuOpen = !isMenuOpen;
                        mobileDepartmentsMenu.classList.toggle('hidden', !isMenuOpen);
                        document.body.style.overflow = isMenuOpen ? 'hidden' : 'auto';
                        chevronIcon.style.transform = isMenuOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                    });
                }

                if (closeMobileMenu) {
                    closeMobileMenu.addEventListener('click', function() {
                        mobileDepartmentsMenu.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                        chevronIcon.style.transform = 'rotate(0deg)';
                        isMenuOpen = false;
                    });
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && isMenuOpen) {
                        mobileDepartmentsMenu.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                        chevronIcon.style.transform = 'rotate(0deg)';
                        isMenuOpen = false;
                    }
                });
            @else
                const departmentsBtnDesktop = document.getElementById('departmentsBtnDesktop');
                const departmentsMenu = document.getElementById('departmentsMenu');
                const chevronIconDesktop = document.getElementById('chevronIconDesktop');
                let isMenuOpen = false;

                if (departmentsBtnDesktop) {
                    departmentsBtnDesktop.addEventListener('click', function(e) {
                        e.stopPropagation();
                        isMenuOpen = !isMenuOpen;
                        departmentsMenu.classList.toggle('hidden', !isMenuOpen);
                        chevronIconDesktop.style.transform = isMenuOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                    });
                }

                document.addEventListener('click', function(e) {
                    if (isMenuOpen &&
                        !departmentsBtnDesktop.contains(e.target) &&
                        !departmentsMenu.contains(e.target)
                    ) {
                        departmentsMenu.classList.add('hidden');
                        chevronIconDesktop.style.transform = 'rotate(0deg)';
                        isMenuOpen = false;
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && isMenuOpen) {
                        departmentsMenu.classList.add('hidden');
                        chevronIconDesktop.style.transform = 'rotate(0deg)';
                        isMenuOpen = false;
                    }
                });
            @endif
        });
    </script>
    
    <!-- Toast Container -->
    <x-toast-container />

    @livewireScripts
    @stack('scripts')
    @stack('tracking_events')
</body>
</html>