<x-dashboard-layout title="Teacher Application Details">
    <div class="mb-6">
        <a href="{{ route('admin.teacher-applications.index') }}" class="text-vibrant-green hover:underline text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Applications
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
            <!-- Personal Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Personal Information</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $application->status === 'reviewed' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $application->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $application->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $application->status === 'converted' ? 'bg-purple-100 text-purple-700' : '' }}">
                        {{ $application->getStatusLabel() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Full Name</label>
                        <p class="text-gray-800">{{ $application->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                        <p class="text-gray-800">{{ $application->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Phone</label>
                        <p class="text-gray-800">{{ $application->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Gender</label>
                        <p class="text-gray-800">{{ ucfirst($application->gender) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Country</label>
                        <p class="text-gray-800">{{ $application->country }}</p>
                    </div>
                    @if($application->city)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">City</label>
                            <p class="text-gray-800">{{ $application->city }}</p>
                        </div>
                    @endif
                    @if($application->birth_date)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Date of Birth</label>
                            <p class="text-gray-800">{{ $application->birth_date->format('M d, Y') }} ({{ $application->birth_date->age }} years old)</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Qualifications & Experience -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Qualifications & Experience</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Education Level</label>
                        <p class="text-gray-800">{{ $application->education_level }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Years of Experience</label>
                        <p class="text-gray-800">{{ $application->years_of_experience }} years</p>
                    </div>
                    @if($application->certifications)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Certifications</label>
                            <p class="text-gray-800 whitespace-pre-line">{{ $application->certifications }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Teaching Experience</label>
                        <p class="text-gray-800 whitespace-pre-line">{{ $application->teaching_experience }}</p>
                    </div>
                </div>
            </div>

            <!-- Teaching Preferences -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Teaching Preferences</h2>
                
                <div class="space-y-4">
                    @if($application->subjects && count($application->subjects) > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Subjects</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($application->subjects as $subject)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                        {{ $subject }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($application->age_groups && count($application->age_groups) > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Preferred Age Groups</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($application->age_groups as $age_group)
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                                        {{ ucfirst($age_group) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($application->teaching_methodology)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Teaching Methodology</label>
                            <p class="text-gray-800 whitespace-pre-line">{{ $application->teaching_methodology }}</p>
                        </div>
                    @endif

                    @if($application->availability && count($application->availability) > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Availability</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($application->availability as $day)
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                                        {{ $day }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Technical Requirements -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Technical Requirements</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid {{ $application->has_stable_internet ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' }}"></i>
                        <span class="text-gray-800">Stable Internet Connection</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid {{ $application->has_quiet_space ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' }}"></i>
                        <span class="text-gray-800">Quiet Teaching Space</span>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Additional Information</h2>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Why Join Our Team?</label>
                    <p class="text-gray-800 whitespace-pre-line">{{ $application->why_join }}</p>
                </div>

                @if($application->cv_path)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">CV/Resume</label>
                        <a href="{{ asset('storage/' . $application->cv_path) }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fa-solid fa-file-pdf mr-2"></i>
                            Download CV
                        </a>
                    </div>
                @endif
            </div>

            <!-- Admin Notes -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Admin Notes & Status</h2>
                
                <form action="{{ route('admin.teacher-applications.update-status', $application->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">
                            <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Under Review</option>
                            <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="converted" {{ $application->status === 'converted' ? 'selected' : '' }}>Converted to Teacher</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Admin Notes</label>
                        <textarea name="admin_notes" rows="4" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green">{{ $application->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="bg-vibrant-green text-white px-6 py-2 rounded-lg hover:bg-deep-blue transition">
                        <i class="fa-solid fa-save mr-2"></i>Update Status & Notes
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Quick Info</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Applied:</span>
                        <span class="text-sm text-gray-800">{{ $application->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Update:</span>
                        <span class="text-sm text-gray-800">{{ $application->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if(!$application->isConverted())
                        <form action="{{ route('admin.teacher-applications.convert', $application->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm"
                                onclick="return confirm('Convert this application to a teacher account? This will create a new teacher user.')">
                                <i class="fa-solid fa-user-check mr-2"></i>Convert to Teacher
                            </button>
                        </form>
                    @else
                        <div class="w-full bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-sm text-center">
                            <i class="fa-solid fa-check-circle mr-2"></i>Already Converted
                        </div>
                    @endif

                    <a href="mailto:{{ $application->email }}"
                        class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-center text-sm">
                        <i class="fa-solid fa-envelope mr-2"></i>Send Email
                    </a>

                    @if($application->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->phone) }}" target="_blank"
                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm">
                            <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    @endif

                    <form action="{{ route('admin.teacher-applications.destroy', $application->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                            onclick="return confirm('Are you sure? This will permanently delete this application.')">
                            <i class="fa-solid fa-trash mr-2"></i>Delete Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
