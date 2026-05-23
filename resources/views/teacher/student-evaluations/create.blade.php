<x-dashboard-layout title="Evaluate Student">
<div class="p-0 max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.student-evaluations.index') }}" class="mr-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-gray-500 hover:text-green-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Student Evaluation</h1>
            <p class="text-gray-500 mt-1">Evaluating student performance on a scale of 1-10</p>
        </div>
    </div>
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('teacher.student-evaluations.store') }}" method="POST">
        @csrf
        
        @if($selectedStudent)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 bg-gradient-to-r from-green-50 to-transparent border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">Student</h2>
                    <p class="text-sm text-gray-500">Evaluating this student for this month.</p>
                </div>
                <div class="p-6">
                    <div class="text-lg font-bold text-gray-800">{{ $selectedStudent->name }}</div>
                    <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 bg-gradient-to-r from-green-50 to-transparent border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">Select Student</h2>
                    <p class="text-sm text-gray-500">Choose a student to evaluate for this month.</p>
                </div>
                <div class="p-6">
                    <select name="student_id" class="w-full p-4 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" required>
                        <option value="">Select Student</option>
                        @foreach($pendingStudents as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-6 bg-gradient-to-r from-green-50 to-transparent border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">Evaluation Criteria</h2>
                <p class="text-sm text-gray-500">Please rate the student on a scale of 1-10 for each question. Total score will be out of 100.</p>
            </div>

            <div class="p-6 space-y-8">
                @php
                    $questions = [
                        ['id' => 'q1', 'title' => 'Attendance & Punctuality', 'desc' => 'Does the student attend sessions on time regularly?'],
                        ['id' => 'q2', 'title' => 'Participation & Engagement', 'desc' => 'Does the student actively participate and engage during class?'],
                        ['id' => 'q3', 'title' => 'Homework Completion', 'desc' => 'Does the student complete assigned homework on time?'],
                        ['id' => 'q4', 'title' => 'Understanding & Comprehension', 'desc' => 'Does the student understand and grasp the concepts well?'],
                        ['id' => 'q5', 'title' => 'Behavior & Discipline', 'desc' => 'Is the student well-behaved and disciplined during sessions?'],
                        ['id' => 'q6', 'title' => 'Focus & Attention', 'desc' => 'Does the student maintain focus and attention during the lesson?'],
                        ['id' => 'q7', 'title' => 'Interaction with Teacher', 'desc' => 'Does the student interact positively with the teacher?'],
                        ['id' => 'q8', 'title' => 'Progress & Improvement', 'desc' => 'Has the student shown noticeable progress or improvement?'],
                        ['id' => 'q9', 'title' => 'Effort & Motivation', 'desc' => 'Does the student show effort and motivation to learn?'],
                        ['id' => 'q10', 'title' => 'Retention of Previous Lessons', 'desc' => 'Does the student remember and apply previous lesson content?'],
                    ];
                @endphp

                @foreach($questions as $index => $q)
                    <div class="pb-6 {{ $index < count($questions) - 1 ? 'border-b border-gray-50' : '' }}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 text-lg">{{ $index + 1 }}. {{ $q['title'] }}</h3>
                                <p class="text-sm text-gray-500">{{ $q['desc'] }}</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-red-500 mr-2">Poor</span>
                            @for($i = 1; $i <= 10; $i++)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="{{ $q['id'] }}_score" value="{{ $i }}" {{ old($q['id'].'_score', 10) == $i ? 'checked' : '' }} class="peer hidden" required>
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-400 font-bold text-sm group-hover:border-green-200 transition peer-checked:bg-vibrant-green peer-checked:border-vibrant-green peer-checked:text-white peer-checked:shadow-md">
                                        {{ $i }}
                                    </div>
                                </label>
                            @endfor
                            <span class="text-xs font-bold text-green-500 ml-2">Excellent</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="p-6 border-t border-gray-100 bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Feedback</label>
                <textarea name="notes" rows="4" class="w-full p-4 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" placeholder="Enter any extra comments or specific feedback here...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4 mb-10">
            <a href="{{ route('teacher.student-evaluations.index') }}" class="px-8 py-3 bg-white border border-gray-300 text-gray-700 rounded-2xl font-bold hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-8 py-3 bg-vibrant-green text-white rounded-2xl font-bold hover:bg-deep-blue transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fa-solid fa-check-circle mr-2 text-lg"></i> Complete Evaluation
            </button>
        </div>
    </form>
</div>
</x-dashboard-layout>
