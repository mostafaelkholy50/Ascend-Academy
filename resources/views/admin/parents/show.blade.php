<x-dashboard-layout title="Parent Details">
    <div class="mb-6">
        <a href="{{ route('admin.parents.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Parents
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
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Parent Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Parent Information</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $parent->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $parent->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form action="{{ route('admin.parents.update', $parent->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ $parent->name }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ $parent->email }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Phone</label>
                            <input type="tel" name="phone" value="{{ $parent->phone }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                            <select name="active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="1" {{ $parent->active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$parent->active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Location / Country</label>
                            <select name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ $parent->country == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Daily Class Reminders</label>
                            <select name="class_reminders_enabled" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="1" {{ $parent->class_reminders_enabled ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ !$parent->class_reminders_enabled ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-save mr-2"></i>Update Parent Info
                    </button>
                </form>
            </div>

            <!-- Security -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Security</h2>
                <form action="{{ route('admin.parents.update-password', $parent->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">New Password</label>
                            <input type="password" name="password" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition">
                            <i class="fa-solid fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Children List -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Children ({{ $parent->children->count() }})</h2>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Attach Existing Students -->
                        @if($availableStudents->count() > 0)
                            <form action="{{ route('admin.parents.attach-students', $parent->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <div class="flex flex-col">
                                    <select name="student_ids[]" multiple
                                        class="min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green text-sm"
                                        size="1"
                                        onfocus="this.size=4;"
                                        onblur="this.size=1;"
                                        onchange="this.size=1; this.blur();">
                                        @foreach($availableStudents as $student)
                                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('student_ids')
                                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm whitespace-nowrap">
                                    <i class="fa-solid fa-link mr-1"></i>Attach Student(s)
                                </button>
                            </form>
                        @endif

                        <button onclick="document.getElementById('addChildModal').classList.remove('hidden')"
                            class="bg-vibrant-green text-white px-4 py-2 rounded-lg hover:bg-deep-blue transition text-sm">
                            <i class="fa-solid fa-plus mr-2"></i>Add Child
                        </button>
                    </div>
                </div>

                @forelse($parent->children as $child)
                    <div class="border border-gray-200 rounded-xl p-5 mb-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xl font-bold">
                                    {{ strtoupper(substr($child->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $child->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $child->email }}</p>
                                    @if($child->birth_date)
                                        <p class="text-xs text-gray-500">Age: {{ \Carbon\Carbon::parse($child->birth_date)->age }} years</p>
                                    @endif
                                    @if($child->gender)
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                            {{ ucfirst($child->gender) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('admin.parents.remove-child', [$parent->id, $child->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700"
                                    onclick="return confirm('Remove this child from parent?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Child's Courses -->
                        @if($child->enrollments->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Enrolled Courses:</h4>
                                <div class="space-y-2">
                                    @foreach($child->enrollments as $enrollment)
                                        <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                            <span class="text-sm text-gray-700">{{ $enrollment->course->title }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $enrollment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="fa-solid fa-users text-4xl mb-3 text-gray-300"></i>
                        <p>No children added yet</p>
                        <p class="text-sm">Click "Add Child" to get started</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Children:</span>
                        <span class="text-lg font-bold text-vibrant-green">{{ $parent->children->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Registered:</span>
                        <span class="text-sm text-gray-800">{{ $parent->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Update:</span>
                        <span class="text-sm text-gray-800">{{ $parent->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $parent->email }}"
                        class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-envelope mr-2"></i>Send Email
                    </a>

                    @if($parent->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $parent->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    @endif

                    <form action="{{ route('admin.parents.destroy', $parent->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure? This will delete the parent and all relationships.')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Parent
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Child Modal -->
    <div id="addChildModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Add New Child</h3>
                    <button onclick="document.getElementById('addChildModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('admin.parents.add-child', $parent->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Student Name *</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Email *</label>
                            <input type="email" name="email" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Password *</label>
                            <input type="password" name="password" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Birth Date</label>
                            <input type="date" name="birth_date" max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Level</label>
                            <select name="level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Level (Optional)</option>
                                <option value="Qaida Nooraniya">Qaida Nooraniya</option>
                                <option value="Nazira (Reading)">Nazira (Reading)</option>
                                <option value="Hifz (Memorization)">Hifz (Memorization)</option>
                                <option value="Tajweed Rules">Tajweed Rules</option>
                                <option value="Foundation">Foundation</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="Ijazah">Ijazah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Location / Country</label>
                            <select name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="submit"
                            class="flex-1 bg-vibrant-green text-white px-6 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                            <i class="fa-solid fa-plus mr-2"></i>Add Student
                        </button>
                        <button type="button"
                            onclick="document.getElementById('addChildModal').classList.add('hidden')"
                            class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>
