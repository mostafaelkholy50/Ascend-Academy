<x-dashboard-layout title="Enrollment Details">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.enrollments.index') }}" class="hover:text-vibrant-green">Enrollments</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Details</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Enrollment Details</h1>
                <p class="text-gray-600 text-sm">View complete enrollment and payment information</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                    <i class="fa-solid fa-edit mr-2"></i>Edit
                </a>
                <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment->id) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this enrollment?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fa-solid fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student Information -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-user-graduate mr-2 text-vibrant-green"></i>
                    Student Information
                </h2>
                
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $enrollment->student->name }}</h3>
                        <p class="text-gray-600">{{ $enrollment->student->email }}</p>
                        @if($enrollment->student->phone)
                            <p class="text-gray-600 text-sm">
                                <i class="fa-solid fa-phone mr-1"></i>{{ $enrollment->student->phone }}
                            </p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('admin.students.show', $enrollment->student->id) }}" 
                    class="inline-block text-vibrant-green hover:text-deep-blue text-sm font-semibold">
                    <i class="fa-solid fa-arrow-right mr-1"></i>View Student Profile
                </a>
            </div>

            <!-- Course Information -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-book mr-2 text-vibrant-green"></i>
                    Course Information
                </h2>
                
                <div class="mb-4">
                    @if($enrollment->course->photo)
                        <img src="{{ asset('storage/' . $enrollment->course->photo) }}" 
                             alt="{{ $enrollment->course->title }}" 
                             class="w-full h-48 object-cover rounded-lg mb-4">
                    @endif
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $enrollment->course->title }}</h3>
                    <p class="text-gray-600 mb-3">{{ $enrollment->course->description }}</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Level</p>
                            <p class="text-lg font-bold text-gray-800">{{ $enrollment->course->level ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Language</p>
                            <p class="text-lg font-bold text-gray-800">{{ $enrollment->course->language ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.courses.show', $enrollment->course->id) }}" 
                    class="inline-block text-vibrant-green hover:text-deep-blue text-sm font-semibold">
                    <i class="fa-solid fa-arrow-right mr-1"></i>View Course Details
                </a>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-dollar-sign mr-2 text-vibrant-green"></i>
                    Monthly Payments
                </h2>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-blue-800 font-semibold mb-2">
                        <i class="fa-solid fa-info-circle mr-2"></i>Payment Tracking
                    </p>
                    <p class="text-blue-700 text-sm mb-3">
                        Payments for this enrollment are tracked monthly. Each month automatically gets a payment record.
                    </p>
                    <a href="{{ route('admin.payments.show', $enrollment->id) }}" 
                        class="inline-block bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm font-semibold">
                        <i class="fa-solid fa-calendar-check mr-2"></i>View & Manage Monthly Payments
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Enrollment Status</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Current Status</p>
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-medium 
                            {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $enrollment->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                            <i class="fa-solid fa-circle-dot mr-1"></i>{{ ucfirst($enrollment->status) }}
                        </span>
                    </div>

                    @if($enrollment->start_date)
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Start Date</p>
                            <p class="text-gray-800 font-medium">{{ $enrollment->start_date->format('F d, Y') }}</p>
                        </div>
                    @endif

                    @if($enrollment->end_date)
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">End Date</p>
                            <p class="text-gray-800 font-medium">{{ $enrollment->end_date->format('F d, Y') }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Created</p>
                        <p class="text-gray-800 font-medium">{{ $enrollment->created_at->format('F d, Y') }}</p>
                    </div>

                    @if($enrollment->updated_at != $enrollment->created_at)
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Last Updated</p>
                            <p class="text-gray-800 font-medium">{{ $enrollment->updated_at->format('F d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
                
                <div class="space-y-2">
                    <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" 
                        class="block w-full text-center bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-edit mr-2"></i>Edit Enrollment
                    </a>
                    
                    {{-- Legacy payment action removed --}}
                    
                    <a href="{{ route('admin.students.show', $enrollment->student->id) }}" 
                        class="block w-full text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                        <i class="fa-solid fa-user mr-2"></i>View Student
                    </a>
                    
                    <a href="{{ route('admin.courses.show', $enrollment->course->id) }}" 
                        class="block w-full text-center bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition">
                        <i class="fa-solid fa-book mr-2"></i>View Course
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
