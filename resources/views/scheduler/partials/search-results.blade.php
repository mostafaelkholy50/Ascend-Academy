@if($search)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4 animate-in fade-in slide-in-from-top-1 duration-300">
        @forelse($searchResults as $result)
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl border border-gray-100 transition group bg-white shadow-sm hover:shadow-md">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full {{ $result->role === 'Teacher' ? 'bg-purple-100 text-purple-600' : 'bg-green-100 text-green-600' }} flex items-center justify-center font-bold shadow-inner">
                        {{ strtoupper(substr($result->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 flex items-center text-sm">
                            {{ Str::limit($result->name, 20) }}
                            <span class="ml-2 text-[10px] px-1.5 py-0.5 rounded-full {{ $result->role === 'Teacher' ? 'bg-purple-50 text-purple-700' : 'bg-green-50 text-green-700' }} border border-current opacity-70">
                                {{ $result->role }}
                            </span>
                        </div>
                        <div class="text-[10px] text-gray-500 truncate w-40">{{ $result->email }}</div>
                    </div>
                </div>
                <a href="{{ $result->role === 'Teacher' ? route('scheduler.teachers.show', $result->id) : route('scheduler.students.show', $result->id) }}" 
                   class="p-2 text-vibrant-green hover:bg-vibrant-green hover:text-white rounded-lg transition-all duration-300" title="View Profile">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-6 text-gray-500">
                <i class="fa-solid fa-user-slash text-2xl mb-2 text-gray-300"></i>
                <p class="text-sm">No users found matching "{{ $search }}"</p>
            </div>
        @endforelse
    </div>
@else
    <div class="bg-gray-50 rounded-xl p-6 text-center border-2 border-dashed border-gray-200">
        <i class="fa-solid fa-users text-3xl mb-2 text-gray-200"></i>
        <p class="text-xs text-gray-400 italic">Start typing a name, email or phone number above to search for students and teachers</p>
    </div>
@endif
