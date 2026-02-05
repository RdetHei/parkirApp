<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <p class="text-sm text-gray-500 ml-9">{{ $description ?? '' }}</p>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800 mb-2">Ada beberapa masalah dengan input Anda:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="max-w-3xl mx-auto"> {{-- Added mx-auto for explicit centering --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        {!! $cardIcon ?? '' !!}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $cardTitle ?? 'Form' }}</h2>
                        <p class="text-sm text-gray-600">{{ $cardDescription ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ $action ?? '#' }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if(isset($method) && ($method == 'PUT' || $method == 'PATCH'))
                    @method($method)
                @endif

                {{ $slot }} {{-- This slot will contain the actual form fields --}}

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ $backUrl ?? '#' }}"
                       class="px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border border-gray-200 transition-colors">
                        {{ $cancelText ?? 'Batal' }}
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors">
                        {{ $submitText ?? 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
