<x-app-layout>
    <section class="py-16 bg-gradient-to-br from-[#1E90A0] to-[#156d7a]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl font-extrabold mb-4">Join Our Teaching Team</h1>
            <p class="text-lg max-w-2xl mx-auto">
                Share your knowledge and inspire students worldwide. Apply now to become part of our certified teaching team.
            </p>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 rounded-lg text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 rounded-lg text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-8 rounded-xl shadow-lg">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Teacher Application Form</h2>

                <form action="{{ route('teacher-application.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Personal Information -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="full_name" required value="{{ old('full_name') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="email" required value="{{ old('email') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="tel" name="phone" required value="{{ old('phone') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                                <select name="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                <input type="text" name="country" required value="{{ old('country') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}" max="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                        </div>
                    </div>

                    <!-- Qualifications -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Qualifications & Experience</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Education Level *</label>
                                <select name="education_level" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                                    <option value="">Select Level</option>
                                    <option value="High School">High School</option>
                                    <option value="Bachelor's">Bachelor's Degree</option>
                                    <option value="Master's">Master's Degree</option>
                                    <option value="PhD">PhD</option>
                                    <option value="Islamic Studies Degree">Islamic Studies Degree</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience *</label>
                                <input type="number" name="years_of_experience" required min="0" value="{{ old('years_of_experience') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Certifications (Ijazah, Teaching Certificates, etc.)</label>
                            <textarea name="certifications" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">{{ old('certifications') }}</textarea>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teaching Experience (Describe your experience) *</label>
                            <textarea name="teaching_experience" rows="4" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">{{ old('teaching_experience') }}</textarea>
                        </div>
                    </div>

                    <!-- Teaching Details -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Teaching Preferences</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subjects You Can Teach * (Select all that apply)</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="subjects[]" value="Quran Memorization" class="rounded">
                                    <span class="text-sm">Quran Memorization</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="subjects[]" value="Tajweed" class="rounded">
                                    <span class="text-sm">Tajweed</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="subjects[]" value="Arabic Language" class="rounded">
                                    <span class="text-sm">Arabic Language</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="subjects[]" value="Islamic Studies" class="rounded">
                                    <span class="text-sm">Islamic Studies</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Age Groups * (Select all that apply)</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="age_groups[]" value="kids" class="rounded">
                                    <span class="text-sm">Kids (3-12)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="age_groups[]" value="teens" class="rounded">
                                    <span class="text-sm">Teens (13-17)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="age_groups[]" value="adults" class="rounded">
                                    <span class="text-sm">Adults (18+)</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teaching Methodology</label>
                            <textarea name="teaching_methodology" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">{{ old('teaching_methodology') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Availability * (Select days)</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach(['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="availability[]" value="{{ $day }}" class="rounded">
                                    <span class="text-sm">{{ $day }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Technical Requirements -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Technical Requirements</h3>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="has_stable_internet" value="1" class="rounded" checked required>
                                <span class="text-sm">I have stable internet connection *</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="has_quiet_space" value="1" class="rounded" checked required>
                                <span class="text-sm">I have a quiet space for teaching *</span>
                            </label>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Information</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Why do you want to join our team? *</label>
                            <textarea name="why_join" rows="4" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">{{ old('why_join') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload CV/Resume (PDF, DOC, DOCX - Max 5MB)</label>
                            <input type="file" name="cv" accept=".pdf,.doc,.docx"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E90A0]">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            * Required fields
                        </p>
                        <button type="submit" class="bg-[#1E90A0] text-white px-8 py-3 rounded-lg font-bold hover:bg-teal-700 transition">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
