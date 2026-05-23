<x-dashboard-layout title="Pending Evaluations">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pending Evaluations</h1>
                <p class="text-gray-600 text-sm">Students who need evaluation for this month</p>
            </div>
        </div>
    </div>

    <!-- Bonus Banner -->
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                <i class="fa-solid fa-gift text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Complete all evaluations to get a bonus!</h3>
                <p class="text-sm text-gray-600">Finish all your pending evaluations for this month and we will add 30 minutes (0.5 hours) to your total time! 🎁</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('bonus_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Bonus Earned!',
                    text: "{{ session('bonus_success') }}",
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 6000,
                    timerProgressBar: true,
                    background: '#fff',
                    color: '#333',
                    iconColor: '#f59e0b'
                });
            });
        </script>
    @endif

    <!-- Pending Evaluations -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Evaluations for This Month</h2>
        @if($pendingStudents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pendingStudents as $student)
                    <div class="border border-gray-200 rounded-xl p-4 flex justify-between items-center">
                        <div>
                            <div class="font-bold text-gray-800">{{ $student->name }}</div>
                            <div class="text-sm text-gray-500">Needs evaluation for {{ now()->format('F Y') }}</div>
                        </div>
                        <a href="{{ route('teacher.student-evaluations.create', ['student_id' => $student->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                            Evaluate
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-check-circle text-green-500 text-5xl mb-4"></i>
                <p class="text-lg font-medium">All students evaluated for this month!</p>
                <p class="text-sm text-gray-400">Great job! You have completed all evaluations.</p>
            </div>
        @endif
    </div>
</x-dashboard-layout>
