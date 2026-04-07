    <!-- Header -->
    <nav class="fixed w-full top-0 z-50 bg-[#020617]/70 backdrop-blur-xl border-b border-white/5" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-3 group z-10">
                    <img src="{{ asset('images/neston.svg') }}" alt="NESTON" class="w-8 h-8 shrink-0">
                    <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <!-- Language Switcher -->
                    <div class="flex items-center bg-white/5 rounded-lg p-1 border border-white/5">
                        <a href="{{ route('lang.switch', 'id') }}" class="px-2 py-0.5 text-[10px] font-bold rounded {{ App::getLocale() == 'id' ? 'bg-emerald-500 text-slate-950' : 'text-slate-500 hover:text-white' }} transition-all uppercase tracking-tighter">ID</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-0.5 text-[10px] font-bold rounded {{ App::getLocale() == 'en' ? 'bg-emerald-500 text-slate-950' : 'text-slate-500 hover:text-white' }} transition-all uppercase tracking-tighter">EN</a>
                    </div>

                    <a href="#about" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">{{ __('About') }}</a>
                    <a href="#workflow" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">{{ __('Workflow') }}</a>
                    <a href="#fitur" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">{{ __('Features') }}</a>
                    <a href="{{ route('docs') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">{{ __('Docs') }}</a>
                    <a href="#contact" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">{{ __('Contact') }}</a>
                    <div class="h-4 w-px bg-white/10"></div>
                    <button onclick="openCardLogin()" class="group flex items-center gap-2 text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                        <i class="fa-solid fa-id-card"></i>
                        {{ __('Card Login') }}
                    </button>
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">{{ __('Sign in') }}</a>
                    <a href="{{ route('register') }}" class="btn-pro-primary !py-2 !px-5 !text-[11px] uppercase tracking-wider">
                        {{ __('Get Started') }}
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen"
             x-cloak
             @click.away="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden absolute top-16 left-0 w-full bg-[#020617]/95 backdrop-blur-2xl border-b border-white/5 py-6 px-6 space-y-4 shadow-2xl">
            <a href="#about" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">About</a>
            <a href="#workflow" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Workflow</a>
            <a href="#fitur" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Features</a>
            <a href="{{ route('docs') }}" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Docs</a>
            <a href="#contact" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Contact</a>
            <div class="h-px w-full bg-white/5"></div>
            <button onclick="openCardLogin(); mobileMenuOpen = false" class="flex items-center gap-3 text-sm font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                <i class="fa-solid fa-id-card"></i>
                Card Login
            </button>
            <a href="{{ route('login') }}" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Sign in</a>
            <a href="{{ route('register') }}" class="btn-pro-primary block text-center !py-3 !text-sm uppercase tracking-wider">
                Get Started
            </a>
        </div>
    </nav>
