<x-dashboard-layout title="Inquiry Details">
    <div class="mb-6">
        <a href="{{ route('admin.inquiries.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Inquiries
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Contact Information</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $inquiry->type === 'trial' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $inquiry->type === 'contact' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $inquiry->type === 'registration' ? 'bg-purple-100 text-purple-700' : '' }}">
                        {{ ucfirst($inquiry->type) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Full Name</label>
                        <p class="text-gray-800">{{ $inquiry->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="text-gray-800">{{ $inquiry->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Phone</label>
                        <p class="text-gray-800">{{ $inquiry->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Location</label>
                        <p class="text-gray-800">
                            {{ $inquiry->city_state ?? $inquiry->city ?? 'N/A' }}{{ $inquiry->country ? ', ' . $inquiry->country : '' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Registration Details -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Registration Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Age</label>
                        <p class="text-gray-800">{{ $inquiry->age ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Gender</label>
                        <p class="text-gray-800">{{ ucfirst($inquiry->gender ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Date of Joining</label>
                        <p class="text-gray-800">{{ $inquiry->join_date ? $inquiry->join_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Referrer</label>
                        <p class="text-gray-800">{{ $inquiry->referrer ?? 'N/A' }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mt-6 mb-3 border-t pt-4">Course Preferences</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Course Needed</label>
                        <p class="text-gray-800">{{ $inquiry->courses_needed ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Sessions Per Week</label>
                        <p class="text-gray-800">{{ $inquiry->sessions_per_week ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Study Hours</label>
                        <p class="text-gray-800">{{ $inquiry->study_hours ?? 'N/A' }}</p>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="text-sm font-semibold text-gray-600">Available Days</label>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @if(is_array($inquiry->available_days))
                                @foreach($inquiry->available_days as $day)
                                    <span class="px-2 py-1 bg-teal-50 text-teal-700 rounded text-xs font-medium border border-teal-100">{{ $day }}</span>
                                @endforeach
                            @elseif($inquiry->available_days)
                                <span class="text-gray-800">{{ $inquiry->available_days }}</span>
                            @else
                                <span class="text-gray-500">Not specified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message -->
            @if($inquiry->message)
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Message</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $inquiry->message }}</p>
                </div>
            @endif

            <!-- Admin Notes -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Admin Notes</h2>
                <form action="{{ route('admin.inquiries.update-status', $inquiry->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                            <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="converted" {{ $inquiry->status === 'converted' ? 'selected' : '' }}>Converted</option>
                            <option value="cancelled" {{ $inquiry->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Notes</label>
                        <textarea name="admin_notes" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green"
                            placeholder="Add notes about this inquiry...">{{ $inquiry->admin_notes }}</textarea>
                    </div>
                    <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-save mr-2"></i>Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Current Status:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $inquiry->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $inquiry->status === 'contacted' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $inquiry->status === 'converted' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $inquiry->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($inquiry->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Received:</span>
                        <span class="text-gray-800">{{ $inquiry->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($inquiry->status !== 'converted')
                        <form action="{{ route('admin.inquiries.convert', $inquiry->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition text-sm"
                                onclick="return confirm('Convert this inquiry to a parent account?\n\nA new parent account will be created with temporary password: password123')">
                                <i class="fa-solid fa-user-plus mr-2"></i>Convert to Parent Account
                            </button>
                        </form>
                    @endif

                    <a href="mailto:{{ $inquiry->email }}"
                        class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-envelope mr-2"></i>Send Email
                    </a>

                    @if($inquiry->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inquiry->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    @endif

                    <form action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure you want to delete this inquiry?')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Inquiry
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-info-circle text-yellow-600 mt-1"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Quick Tip</p>
                        <p>Converting to parent account will create a user with temporary password "password123". Remember to inform the parent to change it.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
