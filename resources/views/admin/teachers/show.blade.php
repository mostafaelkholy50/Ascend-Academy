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
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Class Schedules ({{ $teacher->teacherSchedules->count() }})</h2>
                    <div class="space-y-4">
                        @foreach($teacher->teacherSchedules->take(10) as $schedule)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $schedule->student->name ?? 'N/A' }}</h3>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-sm text-gray-600">
                                                <i class="fa-solid fa-calendar mr-1"></i>
                                                {{ $schedule->day_of_week ?? 'N/A' }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                {{ $schedule->start_time ?? 'N/A' }} - {{ $schedule->end_time ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if(isset($schedule->status))
                                        <span class="text-xs px-2 py-1 rounded-full
                                            {{ $schedule->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $schedule->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reports -->
            @if($teacher->teacherReports && $teacher->teacherReports->count() > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Student Reports ({{ $teacher->teacherReports->count() }})</h2>
                    <div class="space-y-4">
                        @foreach($teacher->teacherReports->take(5) as $report)
                            <div class="border-l-4 border-vibrant-green pl-4 py-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $report->title ?? 'Report' }}</p>
                                        <p class="text-sm text-gray-600 mt-1">Student: {{ $report->student->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 mt-2">{{ $report->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Teacher Hours -->
            @if($teacher->teacherHours && $teacher->teacherHours->count() > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Teaching Hours Log</h2>
                    <div class="space-y-3">
                        @foreach($teacher->teacherHours->take(10) as $hour)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $hour->date ? $hour->date->format('M d, Y') : 'N/A' }}</p>
                                    <p class="text-sm text-gray-600">{{ $hour->hours ?? 0 }} hours</p>
                                </div>
                                @if(isset($hour->status))
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $hour->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $hour->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                        {{ ucfirst($hour->status) }}
                                    </span>
                                @endif
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
