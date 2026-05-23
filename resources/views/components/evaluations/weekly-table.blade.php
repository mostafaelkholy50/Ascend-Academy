@props(['evaluations', 'teachers'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Filters Header -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <form action="{{ url()->current() }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="view" value="weekly">
            
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search teacher..." 
                    class="w-full pl-11 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm text-sm">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>

            <select name="teacher_id" class="px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm text-sm">
                <option value="">All Teachers</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm text-sm">
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-md text-sm">
                    Filter
                </button>
                <a href="{{ url()->current() }}?view=weekly" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition text-sm flex items-center">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="p-4 font-semibold text-gray-600 text-sm">Week Start</th>
                    <th class="p-4 font-semibold text-gray-600 text-sm">Teacher</th>
                    <th class="p-4 font-semibold text-gray-600 text-sm">Evaluator</th>
                    <th class="p-4 font-semibold text-gray-600 text-sm text-center">Score</th>
                    <th class="p-4 font-semibold text-gray-600 text-sm text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($evaluations as $eval)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm font-medium text-gray-700">
                            {{ $eval->week_start_date->format('M d, Y') }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $eval->teacher->name }}</div>
                            <div class="text-[10px] text-gray-400 italic">Evaluated on {{ $eval->evaluation_date->format('M d') }}</div>
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            {{ $eval->evaluator->name }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 rounded-lg text-xs font-black {{ $eval->total_score >= 85 ? 'bg-green-100 text-green-700' : ($eval->total_score >= 70 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $eval->total_score }}%
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                @can('edit evaluations')
                                <a href="{{ route('qualitycontrol.evaluations.create', $eval->teacher_id) }}" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-900 hover:text-white transition" title="Edit">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                @endcan
                                <a href="{{ route('qualitycontrol.reports.teacher', $eval->teacher_id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Teacher Profile">
                                    <i class="fa-solid fa-user"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400 italic">
                            No evaluations found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($evaluations->hasPages())
        <div class="p-6 border-t border-gray-100">
            {{ $evaluations->links() }}
        </div>
    @endif
</div>
