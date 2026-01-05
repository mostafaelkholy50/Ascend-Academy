<x-dashboard-layout title="Registrations">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Registrations</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and track student registrations</p>
        </div>
    </div>

    <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm mb-6 mx-2 md:mx-0">
        <form method="GET" action="{{ route('admin.inquiries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="col-span-1 sm:col-span-2 lg:col-span-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search name, email..."
                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
            </div>
            <div>
                <select name="type" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-vibrant-green">
                    <option value="">All Types</option>
                    <option value="trial" {{ request('type') == 'trial' ? 'selected' : '' }}>Trial Request</option>
                    <option value="contact" {{ request('type') == 'contact' ? 'selected' : '' }}>Contact Message</option>
                    <option value="registration" {{ request('type') == 'registration' ? 'selected' : '' }}>Registration</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-vibrant-green">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-vibrant-green text-white px-4 py-2 rounded-xl hover:bg-deep-blue transition text-sm font-semibold">
                    <i class="fa-solid fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.inquiries.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 flex items-center justify-center">
                    <i class="fa-solid fa-redo text-gray-500"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:hidden px-2 mb-6">
        @forelse($inquiries as $inquiry)
            <div class="bg-white p-4 rounded-2xl shadow-sm border-l-4 
                {{ $inquiry->status === 'pending' ? 'border-yellow-400' : '' }}
                {{ $inquiry->status === 'contacted' ? 'border-blue-400' : '' }}
                {{ $inquiry->status === 'converted' ? 'border-green-400' : '' }}">
                
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $inquiry->full_name }}</h3>
                        <p class="text-xs text-gray-500">{{ $inquiry->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                        {{ $inquiry->type === 'trial' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $inquiry->type }}
                    </span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fa-solid fa-envelope w-5 text-gray-400"></i> {{ $inquiry->email }}
                    </div>
                    @if($inquiry->child_name)
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fa-solid fa-child w-5 text-gray-400"></i> {{ $inquiry->child_name }} ({{ $inquiry->child_age }})
                    </div>
                    @endif
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" 
                       class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold">
                       View Details
                    </a>
                    @if($inquiry->status === 'pending' || $inquiry->status === 'contacted')
                        <form action="{{ route('admin.inquiries.convert', $inquiry->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-vibrant-green text-white rounded-lg text-xs font-bold shadow-sm">
                                Convert
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-10">No inquiries found</p>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden mx-4 md:mx-0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Name & Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Student Details</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Course Preference</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Type & Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($inquiries as $inquiry)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $inquiry->full_name }}</div>
                                <div class="text-xs text-gray-500 mb-1">{{ $inquiry->email }}</div>
                                @if($inquiry->phone)
                                    <div class="text-xs text-gray-500"><i class="fa-solid fa-phone text-[10px] mr-1"></i>{{ $inquiry->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-800">
                                    {{ $inquiry->age ?? $inquiry->child_age ?? 'N/A' }} yrs 
                                    @if($inquiry->gender || $inquiry->child_gender)
                                        • <span class="capitalize">{{ $inquiry->gender ?? $inquiry->child_gender }}</span>
                                    @endif
                                </div>
                                @if($inquiry->city_state || $inquiry->country || $inquiry->city)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fa-solid fa-location-dot text-[10px] mr-1"></i>
                                        {{ $inquiry->city_state ?? $inquiry->city }}
                                        @if($inquiry->country), {{ $inquiry->country }}@endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-800">{{ $inquiry->courses_needed ?? $inquiry->preferred_course ?? 'N/A' }}</div>
                                @if($inquiry->sessions_per_week)
                                    <div class="text-xs text-gray-500">{{ $inquiry->sessions_per_week }}</div>
                                @endif
                                @if($inquiry->join_date)
                                    <div class="text-xs text-teal-600 mt-1 font-semibold">
                                        Join: {{ $inquiry->join_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <span class="w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $inquiry->type === 'trial' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $inquiry->type }}
                                    </span>
                                    <span class="w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase 
                                        {{ $inquiry->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $inquiry->status === 'contacted' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $inquiry->status === 'converted' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $inquiry->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $inquiry->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($inquiry->status === 'pending')
                                        <form action="{{ route('admin.inquiries.convert', $inquiry->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="Convert to Parent">
                                                <i class="fa-solid fa-user-plus"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 px-4">
        {{ $inquiries->links() }}
    </div>
</x-dashboard-layout>