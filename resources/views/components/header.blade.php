<header class="bg-[#022c22]/80 backdrop-blur-xl border-b border-emerald-500/10">
  <nav aria-label="Global" class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8">
    <div class="flex lg:flex-1">
      <a href="/" class="flex items-center gap-3">
        <span class="sr-only">NESTON</span>
        <img src="{{ asset('images/neston-batik.svg') }}" alt="NESTON" class="h-9 w-auto" />
        <span class="text-xl font-bold tracking-tight text-white uppercase">NESTON</span>
      </a>
    </div>
    <div class="flex lg:hidden">
      <button type="button" command="show-modal" commandfor="mobile-menu" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-emerald-500">
        <span class="sr-only">Open main menu</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
          <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>
    <div class="hidden lg:flex lg:gap-x-12">
      <a href="#fitur" class="text-[11px] font-bold text-emerald-400/70 hover:text-amber-400 transition-colors tracking-widest uppercase">Fitur</a>
      <a href="#teknologi" class="text-[11px] font-bold text-emerald-400/70 hover:text-amber-400 transition-colors tracking-widest uppercase">Teknologi</a>
      <a href="#faq" class="text-[11px] font-bold text-emerald-400/70 hover:text-amber-400 transition-colors tracking-widest uppercase">FAQ</a>
    </div>
    <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center gap-6">
        @auth
            <span class="text-[11px] font-bold text-emerald-100/60 uppercase tracking-widest">Hi, {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-emerald-900/40 hover:bg-emerald-800/60 text-emerald-400 px-5 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all">Log Out</button>
            </form>
        @else
            <a href="{{ route('login.create') }}" class="text-[11px] font-bold text-emerald-100/60 hover:text-white transition-all uppercase tracking-widest">Log in</a>
            <a href="{{ route('register.create') }}" class="bg-amber-400 text-[#022c22] hover:bg-amber-300 hover:shadow-[0_0_20px_rgba(251,191,36,0.3)] transition-all duration-300 px-6 py-2.5 rounded-full text-[11px] font-bold uppercase tracking-widest">Mulai</a>
        @endauth
    </div>
  </nav>
  <el-dialog>
    <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
      <div tabindex="0" class="fixed inset-0 focus:outline-none">
        <el-dialog-panel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-[#022c22] p-6 sm:max-w-sm sm:ring-1 sm:ring-emerald-500/10">
          <div class="flex items-center justify-between">
              <a href="/" class="flex items-center gap-3">
                <span class="sr-only">NESTON</span>
                <img src="{{ asset('images/neston-batik.svg') }}" alt="NESTON" class="h-8 w-auto" />
                <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
              </a>
            <button type="button" command="close" commandfor="mobile-menu" class="-m-2.5 rounded-md p-2.5 text-emerald-500">
              <span class="sr-only">Close menu</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
          <div class="mt-6 flow-root">
            <div class="-my-6 divide-y divide-emerald-500/10">
              <div class="space-y-2 py-6">
                <a href="#fitur" class="-mx-3 block rounded-lg px-3 py-2 text-xs font-bold text-emerald-400/70 hover:text-white hover:bg-emerald-500/10 uppercase tracking-widest transition-all">Fitur</a>
                <a href="#teknologi" class="-mx-3 block rounded-lg px-3 py-2 text-xs font-bold text-emerald-400/70 hover:text-white hover:bg-emerald-500/10 uppercase tracking-widest transition-all">Teknologi</a>
                <a href="#faq" class="-mx-3 block rounded-lg px-3 py-2 text-xs font-bold text-emerald-400/70 hover:text-white hover:bg-emerald-500/10 uppercase tracking-widest transition-all">FAQ</a>
              </div>
              <div class="py-6 space-y-4">
                @auth
                    <span class="block text-xs font-bold text-emerald-100/60 uppercase tracking-widest">Hi, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-900/40 text-emerald-400 px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-left">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login.create') }}" class="block text-xs font-bold text-emerald-100/60 hover:text-white uppercase tracking-widest">Log in</a>
                    <a href="{{ route('register.create') }}" class="block w-full bg-amber-400 text-[#022c22] px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-center">Mulai</a>
                @endauth
              </div>
            </div>
          </div>
        </el-dialog-panel>
      </div>
    </dialog>
  </el-dialog>
</header>
