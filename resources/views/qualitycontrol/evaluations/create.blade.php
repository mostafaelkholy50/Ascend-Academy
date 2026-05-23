<x-dashboard-layout title="Evaluate Teacher">
<div class="p-0 max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('qualitycontrol.dashboard') }}" class="mr-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-gray-500 hover:text-blue-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quality Evaluation</h1>
            <p class="text-gray-500 mt-1">Evaluating <span class="font-bold text-blue-600">{{ $teacher->name }}</span> for week of {{ $startOfWeek->format('d M, Y') }}</p>
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

    <form action="{{ route('qualitycontrol.evaluations.store', $teacher->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-transparent border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">Evaluation Criteria</h2>
                <p class="text-sm text-gray-500">Please rate the teacher on a scale of 1-10 for each question. Total score will be out of 100.</p>
            </div>

            <div class="p-6 space-y-8">
                @php
                    $questions = [
                        ['id' => 'q1', 'title' => 'Punctuality', 'desc' => 'Did the teacher start the session on time?'],
                        ['id' => 'q2', 'title' => 'Professional Appearance', 'desc' => 'Is the teacher wearing appropriate and professional attire?'],
                        ['id' => 'q3', 'title' => 'Environment & Lighting', 'desc' => 'Is the background organized and is the lighting clear?'],
                        ['id' => 'q4', 'title' => 'Internet Stability', 'desc' => 'Was the internet connection stable during the session?'],
                        ['id' => 'q5', 'title' => 'Focus & Noise Level', 'desc' => 'Is the environment quiet and free from any distractions?'],
                        ['id' => 'q6', 'title' => 'Student Engagement', 'desc' => 'Did the teacher interact effectively and encourage the student?'],
                        ['id' => 'q7', 'title' => 'Explanation Clarity', 'desc' => 'Was the lesson explained in a clear, simple, and accurate way?'],
                        ['id' => 'q8', 'title' => 'Curriculum Knowledge', 'desc' => 'Does the teacher show complete mastery of the subject matter?'],
                        ['id' => 'q9', 'title' => 'Time Management', 'desc' => 'Did the teacher manage the session time effectively?'],
                        ['id' => 'q10', 'title' => 'Use of Tools', 'desc' => 'Did the teacher use the whiteboard or available digital tools correctly?'],
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
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-400 font-bold text-sm group-hover:border-blue-200 transition peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white peer-checked:shadow-md">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Feedback / ملاحظات إضافية</label>
                <textarea name="notes" rows="4" class="w-full p-4 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter any extra comments or specific feedback here...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4 mb-10">
            <a href="{{ route('qualitycontrol.dashboard') }}" class="px-8 py-3 bg-white border border-gray-300 text-gray-700 rounded-2xl font-bold hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fa-solid fa-check-circle mr-2 text-lg"></i> Complete Evaluation
            </button>
        </div>
    </form>
</div>
</x-dashboard-layout>
