<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parkir App - Solusi Parkir Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">
    <!-- Navbar -->
    <nav class="fixed w-full top-0 z-50 bg-primary-900 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">P</span>
                    </div>
                    <span class="text-2xl font-bold text-white">PARKED</span>
                </a>

                <!-- Desktop Navigation -->
                <ul class="hidden md:flex space-x-8">
                    <li><a href="#beranda" class="text-white hover:text-primary-200 transition-colors duration-300 font-medium">Beranda</a></li>
                    <li><a href="#fitur" class="text-white hover:text-primary-200 transition-colors duration-300 font-medium">Fitur</a></li>
                    <li><a href="#cara-kerja" class="text-white hover:text-primary-200 transition-colors duration-300 font-medium">Cara Kerja</a></li>
                    <li><a href="#kontak" class="text-white hover:text-primary-200 transition-colors duration-300 font-medium">Kontak</a></li>
                </ul>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    <a href="{{ route('login.create') }}" class="bg-white text-primary-900 px-6 py-3 rounded-lg font-semibold hover:shadow-xl transition-all duration-300">
                        Login
                    </a>
                    <a href="{{ route('register.create') }}" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-xl transition-all duration-300">
                        Sign Up
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="pt-32 pb-20 bg-gradient-to-br from-gray-50 via-primary-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-center md:text-left">
                    <div class="inline-block mb-4">
                        <span class="bg-primary-100 text-primary-800 px-4 py-2 rounded-full text-sm font-semibold">
                            Solusi Parkir #1 di Indonesia
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Parkir Lebih Mudah dengan <span class="gradient-text">PARKED</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Temukan, pesan, dan bayar parkir dalam satu aplikasi. Hemat waktu dan nikmati pengalaman parkir yang efisien.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#download" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 inline-flex items-center justify-center group">
                            Mulai Sekarang
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#fitur" class="bg-white text-primary-600 border-2 border-primary-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-primary-600 hover:text-white transition-all duration-300 inline-flex items-center justify-center">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                        <div>
                            <p class="text-3xl font-bold text-primary-600">50K+</p>
                            <p class="text-sm text-gray-600">Pengguna Aktif</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-primary-600">200+</p>
                            <p class="text-sm text-gray-600">Lokasi Parkir</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-primary-600">4.8/5</p>
                            <p class="text-sm text-gray-600">Rating</p>
                        </div>
                    </div>
                </div>

                <!-- Phone Mockup -->
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="w-80 h-[600px] bg-white rounded-[3rem] shadow-2xl p-4 border-8 border-gray-900 float-animation">
                            <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 rounded-[2.5rem] flex flex-col items-center justify-center text-white p-8 overflow-hidden relative">
                                <!-- Phone Screen Content -->
                                <div class="absolute top-0 left-0 right-0 p-6">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold">9:41</span>
                                        <div class="flex space-x-1">
                                            <i class="fas fa-signal text-sm"></i>
                                            <i class="fas fa-wifi text-sm"></i>
                                            <i class="fas fa-battery-full text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center z-10">
                                    <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl">
                                        <span class="text-5xl font-bold text-primary-600">P</span>
                                    </div>
                                    <h3 class="text-3xl font-bold mb-2">PARKED</h3>
                                    <p class="text-lg text-primary-100 mb-8">Smart Parking Solution</p>

                                    <div class="space-y-3 w-full">
                                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-left">
                                            <div class="flex items-center">
                                                <i class="fas fa-parking text-2xl mr-3"></i>
                                                <div>
                                                    <p class="font-semibold">Real-time Availability</p>
                                                    <p class="text-xs text-primary-100">Cek slot parkir tersedia</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-left">
                                            <div class="flex items-center">
                                                <i class="fas fa-credit-card text-2xl mr-3"></i>
                                                <div>
                                                    <p class="font-semibold">Cashless Payment</p>
                                                    <p class="text-xs text-primary-100">Bayar tanpa tunai</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-left">
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-2xl mr-3"></i>
                                                <div>
                                                    <p class="font-semibold">GPS Navigation</p>
                                                    <p class="text-xs text-primary-100">Navigasi ke lokasi parkir</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Decorative circles -->
                                <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-white/10 rounded-full"></div>
                                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Floating Badge -->
                        <div class="absolute -top-4 -right-4 bg-white text-primary-600 px-5 py-3 rounded-xl text-sm font-bold shadow-xl">
                            <i class="fas fa-star text-yellow-500"></i> 4.8 Rating
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-primary-600 text-white px-5 py-3 rounded-xl text-sm font-bold shadow-xl">
                            <i class="fas fa-users"></i> 50K+ Users
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">FITUR UNGGULAN</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-3 mb-4">
                    Mengapa Memilih PARKED?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Solusi lengkap untuk semua kebutuhan parkir Anda dengan teknologi terkini
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-map-marked-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Ketersediaan Real-Time
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Monitor ketersediaan slot parkir secara langsung sebelum Anda berangkat. Hemat waktu dan hindari area yang penuh.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-wallet text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Pembayaran Digital
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Bayar parkir dengan mudah menggunakan e-wallet, kartu kredit, atau transfer bank. Tanpa perlu uang tunai.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-car text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Manajemen Kendaraan
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kelola semua kendaraan Anda dalam satu akun. Proses check-in lebih cepat dengan data tersimpan.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="group bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-route text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Navigasi Terintegrasi
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Navigasi GPS yang akurat membawa Anda langsung ke lokasi parkir dengan rute tercepat dan efisien.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="group bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-bell text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Notifikasi Pintar
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Dapatkan notifikasi otomatis untuk pembayaran, waktu parkir habis, dan informasi penting lainnya.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="group bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-history text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Riwayat Lengkap
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Akses riwayat parkir dan transaksi kapan saja. Unduh invoice digital untuk keperluan administrasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="cara-kerja" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">MUDAH & CEPAT</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-3 mb-4">
                    Cara Kerja
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Mulai parkir dengan mudah hanya dalam 3 langkah sederhana
                </p>
            </div>

            <!-- Steps -->
            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Connecting Line -->
                <div class="hidden md:block absolute top-20 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-primary-300 via-primary-400 to-primary-300"></div>

                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center relative hover:shadow-2xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg relative z-10">
                        01
                    </div>
                    <div class="mb-6">
                        <i class="fas fa-download text-5xl text-primary-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Download & Daftar
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Unduh aplikasi PARKED dari App Store atau Google Play. Daftar dengan email atau nomor telepon dalam hitungan detik.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center relative hover:shadow-2xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg relative z-10">
                        02
                    </div>
                    <div class="mb-6">
                        <i class="fas fa-search-location text-5xl text-primary-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Cari & Pilih Lokasi
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Temukan lokasi parkir terdekat menggunakan GPS. Lihat ketersediaan slot dan harga secara real-time.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center relative hover:shadow-2xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg relative z-10">
                        03
                    </div>
                    <div class="mb-6">
                        <i class="fas fa-check-circle text-5xl text-primary-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Parkir & Bayar
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Parkir kendaraan Anda dengan tenang. Bayar dengan cepat menggunakan metode pembayaran digital favorit Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Dipercaya oleh Ribuan Pengguna
                </h2>
                <p class="text-xl text-primary-100">
                    Bergabunglah dengan komunitas pengguna PARKED di seluruh Indonesia
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                <div class="transform hover:scale-110 transition-transform duration-300">
                    <div class="mb-2">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <h3 class="text-5xl md:text-6xl font-bold mb-2">50K+</h3>
                    <p class="text-lg md:text-xl text-primary-100">Pengguna Aktif</p>
                </div>
                <div class="transform hover:scale-110 transition-transform duration-300">
                    <div class="mb-2">
                        <i class="fas fa-map-pin text-4xl"></i>
                    </div>
                    <h3 class="text-5xl md:text-6xl font-bold mb-2">200+</h3>
                    <p class="text-lg md:text-xl text-primary-100">Lokasi Parkir</p>
                </div>
                <div class="transform hover:scale-110 transition-transform duration-300">
                    <div class="mb-2">
                        <i class="fas fa-exchange-alt text-4xl"></i>
                    </div>
                    <h3 class="text-5xl md:text-6xl font-bold mb-2">100K+</h3>
                    <p class="text-lg md:text-xl text-primary-100">Transaksi Berhasil</p>
                </div>
                <div class="transform hover:scale-110 transition-transform duration-300">
                    <div class="mb-2">
                        <i class="fas fa-star text-4xl text-yellow-300"></i>
                    </div>
                    <h3 class="text-5xl md:text-6xl font-bold mb-2">4.8</h3>
                    <p class="text-lg md:text-xl text-primary-100">Rating Pengguna</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">TESTIMONI</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-3 mb-4">
                    Apa Kata Pengguna Kami
                </h2>
                <p class="text-xl text-gray-600">
                    Ribuan pengguna telah merasakan kemudahan PARKED
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            A
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">Andi Wijaya</h4>
                            <p class="text-sm text-gray-600">Pengusaha</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        "Aplikasi ini sangat membantu dalam aktivitas bisnis saya. Tidak perlu lagi buang waktu mencari parkir. Sangat efisien dan profesional."
                    </p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            S
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">Siti Nurhaliza</h4>
                            <p class="text-sm text-gray-600">Karyawan Swasta</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        "Pembayaran non-tunai sangat praktis dan aman. Interface aplikasinya juga mudah dipahami. Sangat recommended untuk profesional muda."
                    </p>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            B
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">Budi Santoso</h4>
                            <p class="text-sm text-gray-600">Mahasiswa</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        "Fitur real-time slot parkir benar-benar membantu. Aplikasi modern dengan harga terjangkau. Cocok untuk mahasiswa seperti saya."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="download" class="py-20 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-3xl p-12 md:p-16 text-center shadow-2xl relative overflow-hidden">
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>

                <div class="relative z-10">
                    <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
                        Siap Untuk Pengalaman Parkir yang Lebih Baik?
                    </h2>
                    <p class="text-xl text-primary-50 mb-10 max-w-2xl mx-auto">
                        Masuk atau daftar sekarang untuk mulai menggunakan PARKED.
                    </p>

                    <!-- Auth CTA -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                        <a href="{{ route('login.create') }}" class="inline-flex items-center bg-white text-primary-700 px-8 py-4 rounded-xl font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            <i class="fas fa-sign-in-alt text-2xl mr-4"></i>
                            <div class="text-left">
                                <div class="text-xs">Sudah punya akun?</div>
                                <div class="text-lg font-bold">Login</div>
                            </div>
                        </a>

                        <a href="{{ route('register.create') }}" class="inline-flex items-center bg-primary-900 text-white px-8 py-4 rounded-xl font-semibold hover:bg-primary-800 transform hover:scale-105 transition-all duration-300 shadow-lg border border-white/20">
                            <i class="fas fa-user-plus text-2xl mr-4"></i>
                            <div class="text-left">
                                <div class="text-xs">Baru di PARKED?</div>
                                <div class="text-lg font-bold">Sign Up</div>
                            </div>
                        </a>
                    </div>

                    <div class="flex flex-wrap justify-center gap-6 text-white">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Akses Mudah</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Tanpa Biaya Langganan</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Support 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Scroll to Top Button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="fixed bottom-8 right-8 bg-gradient-to-r from-primary-500 to-primary-600 text-white w-14 h-14 rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center z-40">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>
</body>
</html>
