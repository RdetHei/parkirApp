   <!-- Footer -->
    <footer class="py-24 border-t border-white/5 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-16">
                <div class="col-span-2">
                    <a href="#" class="flex items-center space-x-3 mb-6 group">
                        <img src="{{ asset('images/neston.svg') }}" alt="NESTON" class="h-8 w-auto shrink-0">
                        <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                    </a>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mb-8 font-medium">
                        Solusi ekosistem parkir modern berbasis AI untuk manajemen kendaraan yang lebih cerdas dan efisien.
                    </p>
                    <div class="flex gap-4">
                        <a href="https://x.com/neston2026" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-twitter text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-github text-xs"></i>
                        </a>
                         <a href="https://www.youtube.com/@NESTON-2026" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fab fa-youtube text-xs"></i>
                        </a>
                         <a href="https://www.instagram.com/neston2026" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fab fa-instagram text-xs"></i>
                        </a>
                    </div>
                </div>

                 <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Product</h5>
                    <ul class="space-y-4">
                        <li><a href="{{ url('/#fitur') }}" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Features</a></li>
                        <li><a href="{{ url('/#workflow') }}" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Workflow</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Resources</h5>
                    <ul class="space-y-4">
                        <li><a href="{{ route('docs') }}" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Documentation</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Company</h5>
                    <ul class="space-y-4">
                        <li><a href="{{ url('/#about') }}" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">About Us</a></li>
                        <li><a href="{{ url('/#contact') }}" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                    © 2026 NESTON CORE SYSTEM. ALL RIGHTS RESERVED.
                </p>
                <div class="flex gap-6">
                    <span class="text-[10px] font-bold text-slate-700 uppercase tracking-widest">Designed for Excellence</span>
                </div>
            </div>
        </div>
    </footer>
