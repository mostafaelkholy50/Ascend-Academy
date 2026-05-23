<x-dashboard-layout title="Payment History">
    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
            <a href="{{ route('accountant.payments.index') }}" class="hover:text-vibrant-green transition-colors">Financial Hub</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900">Ledger Details</span>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Statement of Account</h1>
                <p class="text-gray-500 mt-1">Detailed monthly tracking for student enrollment records.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-white rounded-2xl shadow-sm border border-gray-100 text-sm font-bold text-gray-700">
                    ID: #ENR-{{ str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-2xl mb-8 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-12 opacity-[0.03] pointer-events-none">
            <i class="fa-solid fa-address-card text-[12rem]"></i>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-3xl bg-vibrant-green text-white flex items-center justify-center text-2xl font-black shadow-lg shadow-green-100">
                    {{ substr($enrollment->student->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Student</p>
                    <p class="text-xl font-bold text-gray-900">{{ $enrollment->student->name }}</p>
                    <p class="text-sm text-gray-500">{{ $enrollment->student->email }}</p>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Enrolled Course</p>
                <p class="text-xl font-bold text-gray-900">{{ $enrollment->course->title }}</p>
                <p class="text-sm text-vibrant-green font-bold">Active Status</p>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Financial Terms</p>
                <p class="text-xl font-bold text-gray-900">{{ $enrollment->getFormattedPrice() }}</p>
                <p class="text-sm text-gray-500">Billed Monthly</p>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Commencement</p>
                <p class="text-xl font-bold text-gray-900">{{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : 'N/A' }}</p>
                <p class="text-sm text-gray-500">Registration Date</p>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Transaction History</h2>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-xs font-bold text-gray-500 uppercase">Automated Ledger</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Billing Period</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Current Status</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Paid Date</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Reference/Notes</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($enrollment->payments as $payment)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-8 py-5 whitespace-nowrap font-bold text-gray-900">
                                {{ $payment->month->format('F Y') }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-gray-900 font-medium">
                                {{ $payment->getFormattedAmount() }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-4 py-1.5 rounded-2xl text-[10px] font-black uppercase tracking-widest
                                    {{ $payment->payment_status === 'paid' ? 'bg-green-100 text-green-700 shadow-sm shadow-green-50' : '' }}
                                    {{ $payment->payment_status === 'unpaid' ? 'bg-red-50 text-red-600' : '' }}
                                    {{ $payment->payment_status === 'partial' ? 'bg-amber-50 text-amber-600' : '' }}">
                                    {{ $payment->payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">
                                @if($payment->paid_at)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-check text-green-500"></i>
                                        {{ $payment->paid_at->format('M d, Y') }}
                                    </div>
                                @else
                                    <span class="text-gray-300">Pending Receipt</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-500 italic">
                                {{ $payment->notes ?? 'No internal notes' }}
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right">
                                <button onclick="togglePaymentModal({{ $payment->id }}, '{{ $payment->payment_status }}', '{{ $payment->notes }}')" 
                                    class="px-5 py-2 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-gray-700 shadow-sm hover:bg-deep-blue hover:text-white hover:border-deep-blue transition-all active:scale-95">
                                    <i class="fa-solid fa-pen-to-square mr-2"></i>Adjust Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-receipt text-gray-200 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-bold">No Transaction History</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 z-[100] transition-opacity duration-300">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-[2.5rem] shadow-2xl p-10 max-w-lg w-full relative z-[110] transform transition-all duration-300 scale-95 opacity-0" id="modalContainer">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter uppercase">Adjust Ledger Status</h3>
                    <button onclick="closePaymentModal()" class="w-10 h-10 rounded-2xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <form id="paymentForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Payment Status</label>
                        <select name="payment_status" id="paymentStatus" class="w-full px-6 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-900 focus:ring-2 focus:ring-vibrant-green transition appearance-none">
                            <option value="unpaid">Unpaid / Delinquent</option>
                            <option value="paid">Fully Paid</option>
                            <option value="partial">Partial Payment</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Internal Reference Notes</label>
                        <textarea name="notes" id="paymentNotes" rows="4" 
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-[1.5rem] font-medium text-gray-900 focus:ring-2 focus:ring-vibrant-green transition resize-none"
                            placeholder="Describe any adjustments or payment details..."></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-vibrant-green text-white font-black py-4 rounded-[1.5rem] shadow-lg shadow-green-100 hover:bg-deep-blue transition-all active:scale-95 uppercase tracking-widest text-sm">
                            Commit Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePaymentModal(paymentId, status, notes) {
            const modal = document.getElementById('paymentModal');
            const container = document.getElementById('modalContainer');
            const form = document.getElementById('paymentForm');
            const statusSelect = document.getElementById('paymentStatus');
            const notesTextarea = document.getElementById('paymentNotes');

            form.action = `/accountant/payments/${paymentId}/status`;
            statusSelect.value = status;
            notesTextarea.value = notes || '';

            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            const container = document.getElementById('modalContainer');
            
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</x-dashboard-layout>
