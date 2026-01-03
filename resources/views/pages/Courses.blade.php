<x-app-layout>
    <section class="min-h-screen bg-gray-50 pt-24 pb-12 lg:pt-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12 text-center">
                <h1 class="text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    Explore Our <span class="text-[#1E90A0]">Courses</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover the perfect course for your learning journey in Quran, Tajweed, and Arabic.
                </p>
                
                <!-- Info Alert -->
                <div class="mt-8 max-w-3xl mx-auto bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-4 text-left shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-circle-info text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-blue-900">Flexible Monthly Subscription</h3>
                        <p class="text-sm text-blue-800 mt-1">
                            Choose your preferred schedule (days per week and session duration). 
                            Prices shown are reference only - your final monthly price will be customized based on your selected schedule.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Course Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                @forelse($courses as $course)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 transform hover:-translate-y-1 group">
                    
                    <div class="relative h-56 overflow-hidden">
                        <img src="{{ $course->getPhotoUrl() }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        @if($course->is_free)
                        <span class="absolute top-4 right-4 bg-green-500 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-lg">
                            FREE
                        </span>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#1E90A0] transition-colors">
                            {{ $course->title }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-6 leading-relaxed line-clamp-3">
                            {{ $course->description }}
                        </p>

                        @if($course->duration_weeks)
                        <div class="flex items-center text-sm text-gray-500 mb-6 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <i class="fa-solid fa-clock text-[#1E90A0] mr-2"></i>
                            <span class="font-medium">{{ $course->duration_weeks }} Weeks Duration</span>
                        </div>
                        @endif

                        <a href="{{ route('get-started') }}" class="block w-full text-center px-4 py-3 bg-[#1E90A0] text-white font-bold rounded-xl hover:bg-[#157a8a] transition shadow-lg shadow-teal-200/50 transform hover:-translate-y-0.5">
                            Join Course
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="fa-solid fa-book text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No courses available</h3>
                    <p class="text-gray-500">Check back soon for new courses!</p>
                </div>
                @endforelse
            </div>

            <!-- Pricing Tiers Section -->
            @if($pricingTiers->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 text-center bg-gradient-to-br from-[#1E90A0]/5 to-blue-50 border-b border-gray-200">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Transparent Pricing</h2>
                        <p class="text-gray-600">Monthly rates based on your preferred schedule</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-200">
                                    <th class="px-6 py-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Schedule</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">CAD</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">USD</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">GBP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pricingTiers as $tier)
                                    <tr class="hover:bg-teal-50/30 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center font-semibold text-gray-900">
                                                <div class="w-10 h-10 rounded-full bg-teal-50 text-[#1E90A0] flex items-center justify-center mr-3">
                                                    <i class="fa-solid fa-calendar"></i>
                                                </div>
                                                {{ $tier->getScheduleDescription() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="block text-xl font-bold text-gray-900">CA${{ number_format($tier->price_cad, 2) }}</span>
                                            <span class="text-xs text-gray-400">/month</span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="block text-xl font-bold text-gray-900">${{ number_format($tier->price_usd, 2) }}</span>
                                            <span class="text-xs text-gray-400">/month</span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="block text-xl font-bold text-gray-900">£{{ number_format($tier->price_gbp, 2) }}</span>
                                            <span class="text-xs text-gray-400">/month</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-600">
                            <i class="fa-solid fa-info-circle text-[#1E90A0] mr-1"></i>
                            All prices are per month. Choose the schedule that works best for you during enrollment.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
