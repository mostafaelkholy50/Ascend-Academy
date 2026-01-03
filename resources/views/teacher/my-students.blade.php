<x-dashboard-layout title="My Students">
    @php $teacher = auth()->user(); @endphp

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">My Students</h1>
        <p class="text-gray-600">Students you are currently teaching</p>
    </div>

    @if($students->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($students as $student)
                <div class="bg-white p-6 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
                    <!-- Student Header -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center text-white text-2xl font-bold shadow-xl">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-bold text-gray-800 text-lg">{{ $student->name }}</h3>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="space-y-3">
                        @if($student->phone)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fa-solid fa-phone text-indigo-600"></i>
                                <span>{{ $student->phone }}</span>
                            </div>
                        @endif

                        <!-- Enrolled Courses -->
                        @if($student->enrollments->count() > 0)
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase">Enrolled Courses</p>
                                <div class="space-y-2">
                                    @foreach($student->enrollments as $enrollment)
                                        <div class="flex items-center justify-between p-2 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-800">{{ $enrollment->course->title }}</span>
                                            <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $enrollment->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('teacher.reports.create', ['student_id' => $student->id]) }}" 
                               class="block w-full text-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                                <i class="fa-solid fa-file-lines mr-2"></i>Create Report
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-16 rounded-3xl shadow-md text-center border border-indigo-200">
            <div class="bg-gradient-to-br from-indigo-100 to-purple-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-users text-indigo-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Students Yet</h3>
            <p class="text-gray-600">You don't have any students assigned to you yet.</p>
        </div>
    @endif
</x-dashboard-layout>
