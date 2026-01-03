<x-dashboard-layout title="Payment History">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.payments.index') }}" class="hover:text-vibrant-green">Payments</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Payment History</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Payment History</h1>
        <p class="text-gray-600 text-sm">Monthly payment tracking for this enrollment</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Enrollment Info -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Student</p>
                <p class="text-lg font-bold text-gray-800">{{ $enrollment->student->name }}</p>
                <p class="text-sm text-gray-600">{{ $enrollment->student->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Course</p>
                <p class="text-lg font-bold text-gray-800">{{ $enrollment->course->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Monthly Price</p>
                <p class="text-lg font-bold text-vibrant-green">{{ $enrollment->getFormattedPrice() }}</p>
                <p class="text-sm text-gray-600">Started: {{ $enrollment->start_date->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Monthly Payments</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paid At</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($enrollment->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800">
                                {{ $payment->month->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ $payment->getFormattedAmount() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $payment->payment_status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $payment->payment_status === 'unpaid' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $payment->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                    <i class="fa-solid fa-circle mr-1"></i>{{ ucfirst($payment->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                @if($payment->paid_at)
                                    <i class="fa-solid fa-check-circle text-green-600 mr-1"></i>
                                    {{ $payment->paid_at->format('M d, Y') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $payment->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="togglePaymentModal({{ $payment->id }}, '{{ $payment->payment_status }}', '{{ $payment->notes }}')" 
                                    class="text-vibrant-green hover:text-deep-blue font-semibold">
                                    <i class="fa-solid fa-edit mr-1"></i>Update
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                                <p>No payment records found.</p>
                                <p class="text-sm mt-2">Payment records will be generated automatically for each month.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Update Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Update Payment Status</h3>
            
            <form id="paymentForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Status</label>
                    <select name="payment_status" id="paymentStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="paymentNotes" rows="3" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        placeholder="Add any notes about this payment..."></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-save mr-2"></i>Save
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                        <i class="fa-solid fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePaymentModal(paymentId, status, notes) {
            const modal = document.getElementById('paymentModal');
            const form = document.getElementById('paymentForm');
            const statusSelect = document.getElementById('paymentStatus');
            const notesTextarea = document.getElementById('paymentNotes');

            form.action = `/admin/payments/${paymentId}/status`;
            statusSelect.value = status;
            notesTextarea.value = notes || '';

            modal.classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });
    </script>
</x-dashboard-layout>
