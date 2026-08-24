<x-dashboard-layout title="Absent Students Dashboard">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">Absent Students Overview</h1>
                <p class="text-sm text-gray-500 mt-1">Overview of students who are at risk due to consecutive absences</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <a href="{{ route('admin.absent-students.list') }}" class="group block">
            <div class="bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white group-hover:scale-[1.02] transition-transform duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <i class="fa-solid fa-user-xmark text-2xl opacity-80 mb-2 group-hover:animate-pulse"></i>
                        <p class="text-white/80 text-sm font-medium">At Risk Students</p>
                        <p class="text-3xl font-bold mt-1">{{ $absentStudents->count() }}</p>
                        <p class="text-xs text-white/70 mt-2">Currently absent for 3+ consecutive sessions</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <i class="fa-solid fa-arrow-right text-white"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</x-dashboard-layout>
