<x-dashboard-layout title="Student Details">
    <div class="mb-6">
        <a href="{{ route('admin.students.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Students
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
            <!-- Student Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Student Information</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $student->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $student->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ $student->name }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ $student->email }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Phone</label>
                            <input type="tel" name="phone" value="{{ $student->phone }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Gender</option>
                                <option value="male" {{ $student->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $student->gender == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Date of Birth</label>
                            <input type="date" name="birth_date" value="{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : '' }}" max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                            <select name="active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="1" {{ $student->active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$student->active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-save mr-2"></i>Update Student Info
                    </button>
                </form>
            </div>

            <!-- Security -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Security</h2>
                <form action="{{ route('admin.students.update-password', $student->id) }}" method="POST" class="space-y-4">
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

            <!-- Parents List -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Parents ({{ $student->parents ? $student->parents->count() : 0 }})</h2>
                </div>

                @if($student->parents && $student->parents->count() > 0)
                    <div class="space-y-4">
                        @foreach($student->parents as $parent)
                            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
                                            {{ strtoupper(substr($parent->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800">{{ $parent->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $parent->email }}</p>
                                            @if($parent->phone)
                                                <p class="text-xs text-gray-500">{{ $parent->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.parents.show', $parent->id) }}" 
                                        class="text-vibrant-green hover:text-deep-blue transition">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fa-solid fa-users text-4xl mb-3 text-gray-300"></i>
                        <p>No parents linked to this student</p>
                    </div>
                @endif
            </div>

            <!-- Enrolled Courses -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Enrolled Courses ({{ $student->enrollments ? $student->enrollments->count() : 0 }})</h2>
                    <a href="{{ route('admin.enrollments.create', ['student_id' => $student->id]) }}" class="bg-vibrant-green text-white px-3 py-1.5 rounded-lg hover:bg-deep-blue transition text-sm">
                        <i class="fa-solid fa-plus mr-1"></i>Add Enrollment
                    </a>
                </div>

                @if($student->enrollments && $student->enrollments->count() > 0)
                    <div class="space-y-4">
                        @foreach($student->enrollments as $enrollment)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $enrollment->course->title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $enrollment->course->description }}</p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $enrollment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Started: {{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fa-solid fa-book text-4xl mb-3 text-gray-300"></i>
                        <p>No courses enrolled yet</p>
                    </div>
                @endif
            </div>

            <!-- Schedules -->
            @if($student->schedules && $student->schedules->count() > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Class Schedule</h2>
                    <div class="space-y-3">
                        @foreach($student->schedules as $schedule)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $schedule->day_of_week }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
                                </div>
                                @if($schedule->teacher)
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Teacher:</p>
                                        <p class="font-semibold text-gray-800">{{ $schedule->teacher->name }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reports -->
            @if($student->reports && $student->reports->count() > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Progress Reports</h2>
                    <div class="space-y-4">
                        @foreach($student->reports->take(5) as $report)
                            <div class="border-l-4 border-vibrant-green pl-4 py-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $report->title }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $report->content }}</p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            By {{ $report->teacher->name }} • {{ $report->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Parents:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $student->parents ? $student->parents->count() : 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Enrolled Courses:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $student->enrollments ? $student->enrollments->count() : 0 }}</span>
                    </div>
                    @if($student->birth_date)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Age:</span>
                            <span class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($student->birth_date)->age }} years</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Registered:</span>
                        <span class="text-sm text-gray-800">{{ $student->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Update:</span>
                        <span class="text-sm text-gray-800">{{ $student->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $student->email }}"
                        class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-envelope mr-2"></i>Send Email
                    </a>

                    @if($student->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    @endif

                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure? This will delete the student account.')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Student
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
