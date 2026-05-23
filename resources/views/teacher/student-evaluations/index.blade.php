<x-dashboard-layout title="Student Evaluations">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-700 bg-clip-text text-transparent">
                    Student Evaluations
                </h1>
                <p class="text-gray-600 text-sm mt-1">Manage and track your students' monthly academic evaluations</p>
            </div>
            <a href="{{ route('teacher.student-evaluations.create') }}" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold flex items-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>New Evaluation</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-emerald-600 text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-emerald-600 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center space-x-3 transform transition-all duration-500 ease-in-out opacity-0 translate-y-2';
                toast.innerHTML = `
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-check text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold">Success!</div>
                        <div class="text-sm opacity-90">{{ session('success') }}</div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                }, 100);
                
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => { toast.remove(); }, 500);
                }, 4000);
            });
        </script>
    @endif

    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-600 text-red-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-times-circle text-red-600 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('bonus_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center space-x-3 transform transition-all duration-500 ease-in-out opacity-0 translate-y-2';
                toast.innerHTML = `
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-gift text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold">Bonus Earned!</div>
                        <div class="text-sm opacity-90">{{ session('bonus_success') }}</div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                }, 100);
                
                // Animate out and remove
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                }, 6000);
            });
        </script>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex items-center mb-4">
            <i class="fa-solid fa-filter text-emerald-600 mr-2"></i>
            <h3 class="text-lg font-bold text-gray-800">Filter Evaluations</h3>
        </div>
        <form method="GET" action="{{ route('teacher.student-evaluations.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Student Filter -->
            <div class="group">
                <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-user-graduate text-xs mr-1 text-emerald-600"></i>Student
                </label>
                <select name="student_id" id="student_id" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Month Filter -->
            <div class="group">
                <label for="month" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-calendar text-xs mr-1 text-emerald-600"></i>Month
                </label>
                <select name="month" id="month" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:scale-102 transition-all duration-200 font-semibold">
                    <i class="fa-solid fa-search mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('teacher.student-evaluations.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 hover:shadow-md transition-all duration-200 font-semibold text-center">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Past Evaluations List -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Past Evaluations</h2>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full">
                {{ $evaluations->total() }} Records
            </span>
        </div>
        @if($evaluations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Assessment Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Overall Score</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($evaluations as $evaluation)
                            @php
                                $score = $evaluation->total_score;
                                $badgeColor = $score >= 90 ? 'bg-green-50 text-green-700 border-green-200' : ($score >= 80 ? 'bg-teal-50 text-teal-700 border-teal-200' : ($score >= 70 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($score >= 60 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-700 border-red-200')));
                                $barGrad = $score >= 90 ? 'from-green-400 to-emerald-500' : ($score >= 80 ? 'from-teal-400 to-cyan-500' : ($score >= 70 ? 'from-blue-400 to-indigo-500' : ($score >= 60 ? 'from-yellow-400 to-amber-500' : 'from-red-400 to-pink-500')));
                                $ratingLabel = $score >= 90 ? 'Excellent' : ($score >= 80 ? 'Very Good' : ($score >= 70 ? 'Good' : ($score >= 60 ? 'Satisfactory' : 'Needs Improvement')));
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ strtoupper(substr($evaluation->student->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-800">{{ $evaluation->student->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $evaluation->student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-600">
                                    {{ $evaluation->evaluation_date->format('F d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-black text-gray-800 mr-3 w-12">{{ $score }}%</span>
                                        <div class="w-24 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-2.5 rounded-full bg-gradient-to-r {{ $barGrad }}" style="width: {{ $score }}%"></div>
                                        </div>
                                        <span class="ml-3 px-2 py-0.5 text-3xs font-bold rounded-lg border {{ $badgeColor }}">
                                            {{ $ratingLabel }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('teacher.student-evaluations.show', $evaluation->id) }}" 
                                       class="inline-flex items-center gap-1.5 bg-gradient-to-r from-emerald-50 to-teal-50 hover:from-emerald-100 hover:to-teal-100 text-emerald-700 hover:text-emerald-800 px-4 py-2 rounded-lg border border-emerald-100 hover:border-emerald-200 transition-all duration-200 font-bold text-xs shadow-sm">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>View Results</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($evaluations->hasPages())
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    {{ $evaluations->links() }}
                </div>
            @endif
        @else
            <div class="bg-gradient-to-br from-gray-50 to-emerald-50/20 rounded-3xl p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="bg-emerald-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-square-poll-vertical text-emerald-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Evaluations Found</h3>
                    <p class="text-gray-500 text-sm mb-6">There are no student evaluations logged in the system matching your search criteria.</p>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
