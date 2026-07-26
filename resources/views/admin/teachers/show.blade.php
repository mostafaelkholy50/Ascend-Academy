<x-dashboard-layout title="Teacher Details">
    <div class="mb-6">
        <a href="{{ route('admin.teachers.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Teachers
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Teacher Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Teacher Information</h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.teachers.edit', $teacher->id) }}" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm font-semibold">
                            <i class="fa-solid fa-edit mr-2"></i>Edit Profile & Photo
                        </a>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $teacher->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $teacher->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ $teacher->name }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ $teacher->email }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Phone</label>
                            <input type="tel" name="phone" value="{{ $teacher->phone }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Gender</option>
                                <option value="male" {{ $teacher->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $teacher->gender == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Date of Birth</label>
                            <input type="date" name="birth_date" value="{{ $teacher->birth_date ? \Carbon\Carbon::parse($teacher->birth_date)->format('Y-m-d') : '' }}" max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                            <select name="active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="1" {{ $teacher->active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$teacher->active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Location / Country</label>
                            <select name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ $teacher->country == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-save mr-2"></i>Update Teacher Info
                    </button>
                </form>
            </div>

            <!-- Security -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Security</h2>
                <form action="{{ route('admin.teachers.update-password', $teacher->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">New Password</label>
                            <input type="password" name="password" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition">
                            <i class="fa-solid fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Class Schedules -->
            @if($teacher->teacherSchedules && $teacher->teacherSchedules->count() > 0)
                <details class="bg-white p-6 rounded-2xl shadow-sm group">
                    <summary class="flex justify-between items-center cursor-pointer list-none mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Class Schedules ({{ $teacher->teacherSchedules->count() }})</h2>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-gray-500"></i>
                        </span>
                    </summary>
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($teacher->teacherSchedules as $schedule)
                            <div class="border border-gray-200 rounded-xl p-4">
                                @php
                                    $now = now();
                                    $isPast = $schedule->ends_at && $now->greaterThan($schedule->ends_at);
                                    $isInProgress = $schedule->starts_at && $schedule->ends_at && $now->between($schedule->starts_at, $schedule->ends_at);
                                    
                                    $statusClass = 'bg-blue-100 text-blue-700';
                                    $statusText = 'Upcoming';

                                    if ($schedule->status === 'completed') {
                                        $statusClass = 'bg-green-100 text-green-700';
                                        $statusText = 'Completed';
                                        if ($schedule->attendance) {
                                            if ($schedule->attendance->student_present && $schedule->attendance->teacher_present) {
                                                $statusClass = 'bg-emerald-100 text-emerald-700';
                                                $statusText = 'Attended';
                                            } elseif (!$schedule->attendance->teacher_present && !$schedule->attendance->student_present) {
                                                $statusClass = 'bg-red-100 text-red-700';
                                                $statusText = 'Both Absent';
                                            } elseif (!$schedule->attendance->teacher_present) {
                                                $statusClass = 'bg-red-100 text-red-700';
                                                $statusText = 'Teacher Absent';
                                            } elseif (!$schedule->attendance->student_present) {
                                                $statusClass = 'bg-orange-100 text-orange-700';
                                                $statusText = 'Student Absent';
                                            }
                                        }
                                    } elseif ($isPast) {
                                        $statusClass = 'bg-gray-100 text-gray-700';
                                        $statusText = 'Past Unrecorded';
                                    } elseif ($isInProgress) {
                                        $statusClass = 'bg-yellow-100 text-yellow-700';
                                        $statusText = 'In Progress';
                                    } elseif ($schedule->status === 'cancelled') {
                                        $statusClass = 'bg-red-100 text-red-700';
                                        $statusText = 'Cancelled';
                                    }
                                @endphp
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $schedule->student->name ?? 'N/A' }}</h3>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-sm text-gray-600">
                                                <i class="fa-solid fa-calendar mr-1"></i>
                                                {{ $schedule->starts_at ? $schedule->starts_at->format('M d, Y') : 'N/A' }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                {{ $schedule->starts_at ? $schedule->starts_at->format('h:i A') : 'N/A' }} - {{ $schedule->ends_at ? $schedule->ends_at->format('h:i A') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif

            <!-- Reports / Evaluations -->
            @if($teacher->teacherEvaluations && $teacher->teacherEvaluations->count() > 0)
                <details class="bg-white p-6 rounded-2xl shadow-sm group mb-6">
                    <summary class="flex justify-between items-center cursor-pointer list-none mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Student Evaluations ({{ $teacher->teacherEvaluations->count() }})</h2>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-gray-500"></i>
                        </span>
                    </summary>
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($teacher->teacherEvaluations as $evaluation)
                            <a href="{{ route('admin.student-evaluations.show', $evaluation->id) }}" class="block border-l-4 border-vibrant-green pl-4 py-2 bg-gray-50 rounded-r-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">Evaluation: {{ \Carbon\Carbon::create()->month($evaluation->evaluation_month)->format('F') }} {{ $evaluation->evaluation_year }}</p>
                                        <p class="text-sm text-gray-600 mt-1">Student: {{ $evaluation->student->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 mt-2">Score: <span class="font-bold {{ $evaluation->total_score >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $evaluation->total_score }}/100</span> | {{ $evaluation->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </details>
            @endif

            <!-- Teacher Hours -->
            @if($teacher->teacherHours && $teacher->teacherHours->count() > 0)
                <details class="bg-white p-6 rounded-2xl shadow-sm group">
                    <summary class="flex justify-between items-center cursor-pointer list-none mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Teaching Hours Log ({{ $teacher->teacherHours->count() }})</h2>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-gray-500"></i>
                        </span>
                    </summary>
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($teacher->teacherHours as $hour)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::create()->year($hour->year)->month($hour->month)->format('F Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        {{ number_format($hour->total_hours, 2) }} hours
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $hour->is_paid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $hour->is_paid ? 'Paid' : 'Unpaid' }}
                                    @if($hour->is_paid && $hour->paid_at)
                                        ({{ $hour->paid_at->format('M d, Y') }})
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Students:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $totalStudents ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed Classes:</span>
                        <span class="text-lg font-bold text-green-600">{{ $completedClasses ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Upcoming Classes:</span>
                        <span class="text-lg font-bold text-blue-600">{{ $upcomingClasses ?? 0 }}</span>
                    </div>
                    @if($teacher->birth_date)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Age:</span>
                            <span class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($teacher->birth_date)->age }} years</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Joined:</span>
                        <span class="text-sm text-gray-800">{{ $teacher->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Update:</span>
                        <span class="text-sm text-gray-800">{{ $teacher->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $teacher->email }}"
                        class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-envelope mr-2"></i>Send Email
                    </a>

                    @if($teacher->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $teacher->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    @endif

                    <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure? This will delete the teacher account.')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Teacher
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
