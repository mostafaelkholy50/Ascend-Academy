<x-app-layout>
    <section class="py-16 bg-gradient-to-br from-[#1E90A0] to-[#156d7a] min-h-screen flex items-center justify-center">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
                <!-- Success Icon -->
                <div class="mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <!-- Success Message -->
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4">
                    Application Submitted Successfully!
                </h1>
                
                <p class="text-lg text-gray-600 mb-8">
                    Thank you for your interest in joining our teaching team at Ascend Academy.
                </p>

                <!-- What's Next Section -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8 text-left">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">What Happens Next?</h2>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">
                                1
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Application Review</h3>
                                <p class="text-sm text-gray-600">Our team will carefully review your application and qualifications.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Initial Contact</h3>
                                <p class="text-sm text-gray-600">We'll contact you within 3-5 business days via email or phone.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">
                                3
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Interview Process</h3>
                                <p class="text-sm text-gray-600">If selected, we'll schedule an interview to discuss your experience and teaching approach.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#1E90A0] text-white rounded-full flex items-center justify-center font-bold text-sm">
                                4
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Welcome Aboard!</h3>
                                <p class="text-sm text-gray-600">Upon approval, you'll receive your teacher account credentials and onboarding materials.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-left">
                            <h3 class="font-semibold text-blue-800 mb-1">Please Check Your Email</h3>
                            <p class="text-sm text-blue-700">
                                We've sent a confirmation email to the address you provided. 
                                Make sure to check your spam folder if you don't see it in your inbox.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-t border-gray-200 pt-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Have questions about your application?
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="mailto:info@ascendacademy.com" 
                            class="inline-flex items-center justify-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email Us
                        </a>
                        <a href="{{ route('contact') }}" 
                            class="inline-flex items-center justify-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Contact Page
                        </a>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="mt-8">
                    <a href="{{ route('home') }}" 
                        class="inline-block bg-[#1E90A0] text-white px-8 py-3 rounded-lg font-bold hover:bg-teal-700 transition">
                        Return to Home
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
