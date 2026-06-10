<x-dashboard-layout title="Financial Hub">
    <div class="space-y-10 py-6">
        <!-- Premium Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-deep-blue to-black p-10 rounded-[3rem] shadow-2xl text-white">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <h1 class="text-4xl font-black tracking-tight mb-2">Financial Collection Center</h1>
                    <p class="text-white/60 text-lg max-w-xl leading-relaxed">
                        Precision tracking for academy revenue. Manage student enrollments and streamline your collection process.
                    </p>
                </div>
                <div class="flex flex-wrap gap-4">
                    <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-3xl border border-white/10">
                        <div class="text-[10px] font-black uppercase tracking-widest text-white/50 mb-1">Collection Rate</div>
                        <div class="text-2xl font-black text-vibrant-green">
                            @php
                                $total = floatval($stats['total_this_month']);
                                $rate = $total > 0 ? ($stats['paid_this_month'] / $total) * 100 : 0;
                            @endphp
                            {{ number_format($rate, 1) }}%
                        </div>
                    </div>
                    <div class="bg-vibrant-green text-black px-6 py-4 rounded-3xl shadow-lg shadow-green-500/20 flex flex-col justify-center">
                        <div class="text-[10px] font-black uppercase tracking-widest text-black/50 mb-1">Expected Revenue</div>
                        <div class="text-2xl font-black">${{ number_format($stats['total_amount_this_month'], 2) }}</div>
                    </div>
                </div>
            </div>
            <!-- Decorative background elements -->
            <div class="absolute -right-20 -top-20 w-96 h-96 bg-vibrant-green/10 rounded-full blur-[100px]"></div>
            <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-blue-500/10 rounded-full blur-[80px]"></div>
        </div>

        <!-- Smart Search & Quick Filters -->
        <div class="bg-white/80 backdrop-blur-xl border border-slate-100 p-4 rounded-[2.5rem] shadow-sm sticky top-4 z-40">
            <form method="GET" action="{{ route('accountant.payments.index') }}" class="flex flex-wrap items-center gap-4">
                <input type="hidden" name="country" id="country-filter-input" value="{{ request('country') }}">
                <div class="relative flex-1 min-w-[300px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student name or email..." 
                        class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-vibrant-green/50 transition font-medium text-slate-900">
                </div>
                
                <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                    <select name="course_id" class="px-6 py-4 bg-slate-50 border-none rounded-3xl font-bold text-slate-700 focus:ring-2 focus:ring-vibrant-green/50 appearance-none min-w-[150px]">
                        <option value="">All Programs</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>

                    <select name="payment_status" class="px-6 py-4 bg-slate-50 border-none rounded-3xl font-bold text-slate-700 focus:ring-2 focus:ring-vibrant-green/50 appearance-none min-w-[150px]">
                        <option value="">Status: All</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Pending Only</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid Only</option>
                    </select>
                </div>

                <button type="submit" class="bg-slate-900 text-white px-8 py-4 rounded-3xl font-black uppercase tracking-widest text-xs hover:bg-black transition-all active:scale-95">
                    Filter
                </button>
            </form>
        </div>

        <!-- Country Pills Filter -->
        <div class="flex flex-wrap items-center gap-3 bg-white/80 backdrop-blur-xl border border-slate-100 p-6 rounded-[2.5rem] shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2">Filter by Country:</span>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-country="" 
                    class="country-pill px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition-all duration-300 active:scale-95 shadow-sm
                    {{ !request('country') ? 'bg-vibrant-green text-black ring-4 ring-vibrant-green/10' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                    All
                </button>
                @foreach($allowedCountries as $country)
                    <button type="button" data-country="{{ $country }}" 
                        class="country-pill px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition-all duration-300 active:scale-95 shadow-sm
                        {{ request('country') == $country ? 'bg-vibrant-green text-black ring-4 ring-vibrant-green/10' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        {{ $country }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Financial Dashboard Grid -->
        <div class="grid grid-cols-1 gap-8" id="payments-grid-container">
            @php
                $baseMonth = request('base_month') ? \Carbon\Carbon::parse(request('base_month') . '-01') : now();
                $startMonth = $baseMonth->copy()->startOfMonth();
                
                $months = collect();
                for ($i = 0; $i < 4; $i++) { // Show 4 months for better focus
                    $months->push($startMonth->copy()->addMonths($i));
                }
            @endphp

            <div class="flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('accountant.payments.index', array_merge(request()->except('base_month'), ['base_month' => $startMonth->copy()->subMonths(4)->format('Y-m')])) }}" 
                        class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-vibrant-green hover:border-vibrant-green transition-all">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <div class="text-center px-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Billing Period</span>
                        <div class="text-lg font-black text-slate-900">{{ $months->first()->format('M Y') }} — {{ $months->last()->format('M Y') }}</div>
                    </div>
                    <a href="{{ route('accountant.payments.index', array_merge(request()->except('base_month'), ['base_month' => $startMonth->copy()->addMonths(4)->format('Y-m')])) }}" 
                        class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-vibrant-green hover:border-vibrant-green transition-all">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center gap-6 text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-vibrant-green"></span> Paid</div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-400"></span> Unpaid</div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-200"></span> Future</div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                @forelse($enrollments as $enrollment)
                    <div class="group bg-white rounded-[3rem] p-8 shadow-sm border border-slate-50 hover:shadow-xl hover:border-vibrant-green/20 transition-all duration-500 relative overflow-hidden">
                        <!-- Card Glow Effect -->
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-slate-50 rounded-full blur-3xl group-hover:bg-vibrant-green/5 transition-colors duration-500"></div>
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <!-- Student Profile Info -->
                            <div class="flex items-start justify-between mb-8">
                                <div class="flex items-center gap-5">
                                    <div class="relative">
                                        <div class="w-16 h-16 rounded-[1.75rem] bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-2xl font-black text-slate-400 group-hover:from-vibrant-green group-hover:to-deep-blue group-hover:text-white transition-all duration-500 shadow-inner">
                                            {{ substr($enrollment->student->name, 0, 1) }}
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-white shadow-sm flex items-center justify-center border border-slate-50">
                                            <i class="fa-solid fa-circle-check text-[10px] {{ $enrollment->status === 'active' ? 'text-vibrant-green' : 'text-slate-300' }}"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-deep-blue transition-colors">{{ $enrollment->student->name }}</h3>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs font-bold text-slate-400"><i class="fa-solid fa-location-dot mr-1"></i>{{ $enrollment->student->country }}</span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span class="text-xs font-black text-vibrant-green uppercase tracking-tighter">{{ $enrollment->course->title }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center justify-end gap-2 mb-1">
                                        <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Monthly Rate</div>
                                        <button x-data="" x-on:click="$dispatch('open-modal', 'edit-enrollment-{{ $enrollment->id }}')" class="text-slate-400 hover:text-vibrant-green transition-colors">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </button>
                                    </div>
                                    <div class="text-xl font-black text-slate-900 group-hover:text-vibrant-green transition-colors">{{ $enrollment->getFormattedPrice() }}</div>
                                </div>
                            </div>

                            <!-- Payment Timeline -->
                            <div class="grid grid-cols-4 gap-4 mt-auto">
                                @foreach($months as $month)
                                    @php
                                        $payment = $enrollment->payments->firstWhere(function($p) use ($month) {
                                            return $p->month->format('Y-m') === $month->format('Y-m');
                                        });
                                        
                                        $isPaid = $payment && $payment->payment_status === 'paid';
                                        $isUnpaid = $payment && $payment->payment_status === 'unpaid';
                                    @endphp
                                    <div class="flex flex-col gap-2">
                                        <div class="text-[10px] font-bold text-slate-400 text-center uppercase">{{ $month->format('M') }}</div>
                                        @if($payment)
                                        <form method="POST" action="{{ route('accountant.payments.update-status', $payment->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="payment_status" value="{{ $isPaid ? 'unpaid' : 'paid' }}">
                                            <button type="submit" 
                                                class="w-full py-4 rounded-[1.5rem] flex flex-col items-center justify-center gap-1 transition-all duration-300 hover:scale-105 active:scale-95 shadow-sm
                                                {{ $isPaid ? 'bg-vibrant-green text-black ring-4 ring-vibrant-green/10' : ($isUnpaid ? 'bg-red-50 text-red-500 border border-red-100 hover:bg-red-500 hover:text-white' : 'bg-slate-50 text-slate-400') }}">
                                                <i class="fa-solid {{ $isPaid ? 'fa-check-circle' : 'fa-circle-dollar-to-slot' }} text-lg"></i>
                                                <span class="text-[8px] font-black uppercase tracking-widest">{{ $isPaid ? 'Paid' : 'Pay' }}</span>
                                            </button>
                                        </form>
                                        @else
                                            <div class="w-full py-4 rounded-[1.5rem] flex flex-col items-center justify-center gap-1 bg-slate-50 text-slate-300 shadow-sm opacity-50 cursor-not-allowed" title="No payment record generated">
                                                <i class="fa-solid fa-circle-dollar-to-slot text-lg"></i>
                                                <span class="text-[8px] font-black uppercase tracking-widest">N/A</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Footer Actions -->
                            <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                                <div class="flex gap-2">
                                    <a href="{{ route('accountant.payments.show', $enrollment->id) }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-vibrant-green hover:text-black transition-all">
                                        Full Statement
                                    </a>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Enrollment ID</div>
                                        <div class="text-[10px] font-bold text-slate-400">#{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Enrollment Modal -->
                    <x-modal name="edit-enrollment-{{ $enrollment->id }}" focusable>
                        <form method="POST" action="{{ route('accountant.payments.enrollment.update', $enrollment->id) }}" class="p-8">
                            @csrf
                            @method('PATCH')

                            <h2 class="text-2xl font-black text-slate-900 mb-6">
                                Update Enrollment Details
                            </h2>

                            <div class="space-y-6">
                                <div>
                                    <label for="admin_price_{{ $enrollment->id }}" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Monthly Price</label>
                                    <input type="number" step="0.01" id="admin_price_{{ $enrollment->id }}" name="admin_price" value="{{ $enrollment->admin_price }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-vibrant-green/50 transition font-bold text-slate-900" required>
                                </div>

                                <div>
                                    <label for="currency_{{ $enrollment->id }}" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Currency</label>
                                    <select id="currency_{{ $enrollment->id }}" name="currency" class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-vibrant-green/50 transition font-bold text-slate-900 appearance-none" required>
                                        <option value="USD" {{ $enrollment->currency === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="CAD" {{ $enrollment->currency === 'CAD' ? 'selected' : '' }}>CAD (CA$)</option>
                                        <option value="GBP" {{ $enrollment->currency === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="EUR" {{ $enrollment->currency === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="EGP" {{ $enrollment->currency === 'EGP' ? 'selected' : '' }}>EGP (E£)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close-modal', 'edit-enrollment-{{ $enrollment->id }}')" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-200 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-3 bg-vibrant-green text-black rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-green-400 shadow-lg shadow-vibrant-green/20 transition-all active:scale-95">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <i class="fa-solid fa-magnifying-glass-dollar text-slate-200 text-5xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 mb-2">No results found</h2>
                        <p class="text-slate-400 max-w-sm mx-auto">Try adjusting your filters or search terms to find the right students.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Container -->
            <div class="mt-12 px-6">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const courseSelect = document.querySelector('select[name="course_id"]');
            const countrySelect = document.querySelector('select[name="country"]');
            const statusSelect = document.querySelector('select[name="payment_status"]');
            const container = document.getElementById('payments-grid-container');
            const form = searchInput.closest('form');

            let timeout = null;

            function performSearch() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const url = `${form.action}?${params.toString()}`;

                // Add loading state
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('payments-grid-container');
                    
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                        // Update URL without reload
                        window.history.pushState({}, '', url);
                    }
                })
                .catch(error => console.error('Search failed:', error))
                .finally(() => {
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(performSearch, 300); // Debounce 300ms
            });

            courseSelect.addEventListener('change', performSearch);
            statusSelect.addEventListener('change', performSearch);

            const countryInput = document.getElementById('country-filter-input');
            const countryPills = document.querySelectorAll('.country-pill');

            countryPills.forEach(pill => {
                pill.addEventListener('click', function() {
                    countryInput.value = this.getAttribute('data-country');
                    
                    countryPills.forEach(p => {
                        p.classList.remove('bg-vibrant-green', 'text-black', 'ring-4', 'ring-vibrant-green/10');
                        p.classList.add('bg-slate-50', 'text-slate-600', 'hover:bg-slate-100');
                    });
                    
                    this.classList.remove('bg-slate-50', 'text-slate-600', 'hover:bg-slate-100');
                    this.classList.add('bg-vibrant-green', 'text-black', 'ring-4', 'ring-vibrant-green/10');
                    
                    performSearch();
                });
            });
        });
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        #payments-grid-container { transition: opacity 0.3s ease; }
    </style>
</x-dashboard-layout>
