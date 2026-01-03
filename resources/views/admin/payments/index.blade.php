<x-dashboard-layout title="Payment Management">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Monthly Payment Management</h1>
                <p class="text-gray-600 text-sm">Track and manage student payments by month for all enrollments</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Student</label>
                <select name="student_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Course</label>
                <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    <i class="fa-solid fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Enrollments with Monthly Payments -->
    @php
        // Get the base month from request or default to current month
        $baseMonth = request('base_month') ? \Carbon\Carbon::parse(request('base_month') . '-01') : now();
        $startMonth = $baseMonth->copy()->startOfMonth();
        
        $enrollments = \App\Models\Enrollment::with(['student', 'course', 'payments'])
            ->when(request('student_id'), fn($q) => $q->where('student_id', request('student_id')))
            ->when(request('course_id'), fn($q) => $q->where('course_id', request('course_id')))
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Generate 6 months starting from the base month
        $months = collect();
        for ($i = 0; $i < 6; $i++) {
            $months->push($startMonth->copy()->addMonths($i));
        }
    @endphp

    <!-- Month Navigation -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 flex items-center justify-between">
        <a href="{{ route('admin.payments.index', array_merge(request()->except('base_month'), ['base_month' => $startMonth->copy()->subMonths(6)->format('Y-m')])) }}" 
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fa-solid fa-chevron-left mr-2"></i>Previous 6 Months
        </a>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">Showing</p>
            <p class="font-bold text-gray-800">{{ $months->first()->format('M Y') }} - {{ $months->last()->format('M Y') }}</p>
        </div>
        
        <a href="{{ route('admin.payments.index', array_merge(request()->except('base_month'), ['base_month' => $startMonth->copy()->addMonths(6)->format('Y-m')])) }}" 
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            Next 6 Months<i class="fa-solid fa-chevron-right ml-2"></i>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-50">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Monthly Price</th>
                        @foreach($months as $month)
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ $month->format('M Y') }}
                            </th>
                        @endforeach
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                        @php
                            // Get or create payment records for each month
                            $enrollmentPayments = collect();
                            foreach ($months as $month) {
                                $payment = $enrollment->payments->firstWhere(function($p) use ($month) {
                                    return $p->month->format('Y-m') === $month->format('Y-m');
                                });
                                
                                // Auto-create if doesn't exist (past or future)
                                // Only create if enrollment has admin_price set
                                if (!$payment && $enrollment->admin_price) {
                                    $payment = \App\Models\EnrollmentPayment::create([
                                        'enrollment_id' => $enrollment->id,
                                        'month' => $month,
                                        'amount' => $enrollment->admin_price,
                                        'currency' => $enrollment->currency,
                                        'payment_status' => 'unpaid',
                                    ]);
                                }
                                
                                $enrollmentPayments->push($payment);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                <a href="{{ route('admin.students.show', $enrollment->student->id) }}" class="text-vibrant-green hover:text-deep-blue font-semibold">
                                    {{ $enrollment->student->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.courses.show', $enrollment->course->id) }}" class="text-gray-800 hover:text-vibrant-green">
                                    {{ $enrollment->course->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800">
                                {{ $enrollment->getFormattedPrice() }}
                            </td>
                            @foreach($enrollmentPayments as $payment)
                                <td class="px-6 py-4 text-center">
                                    @if($payment)
                                        <form method="POST" action="{{ route('admin.payments.update-status', $payment->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="payment_status" value="{{ $payment->payment_status === 'paid' ? 'unpaid' : 'paid' }}">
                                            <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium transition
                                                {{ $payment->payment_status === 'paid' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                                <i class="fa-solid {{ $payment->payment_status === 'paid' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                {{ ucfirst($payment->payment_status) }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.payments.show', $enrollment->id) }}" class="text-blue-600 hover:text-blue-800" title="View All Payments">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.payments.mark-all-paid', $enrollment->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Mark All Visible Months as Paid" onclick="return confirm('Mark all visible months as PAID for this student?')">
                                            <i class="fa-solid fa-check-double"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.mark-all-unpaid', $enrollment->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Mark All Visible Months as Unpaid" onclick="return confirm('Mark all visible months as UNPAID for this student?')">
                                            <i class="fa-solid fa-times-circle"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $months->count() }}" class="px-6 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                                <p>No active enrollments found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-blue-800 font-semibold mb-2">
            <i class="fa-solid fa-info-circle mr-2"></i>How to Use
        </p>
        <ul class="text-blue-700 text-sm space-y-1">
            <li>• Click on any month's status to toggle between Paid/Unpaid</li>
            <li>• Green = Paid, Red = Unpaid</li>
            <li>• Click the eye icon to view detailed payment history for an enrollment</li>
            <li>• Use "Generate Current Month Payments" to create payment records for all active enrollments</li>
        </ul>
    </div>
</x-dashboard-layout>
