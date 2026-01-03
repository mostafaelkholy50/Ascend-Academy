<x-dashboard-layout title="Create Pricing Tier">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.pricing-tiers.index') }}" class="hover:text-vibrant-green">Pricing Tiers</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-semibold">Create New</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Create Pricing Tier</h1>
        <p class="text-gray-600 text-sm">Add a new reference pricing tier</p>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pricing-tiers.store') }}" class="max-w-4xl">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-calendar mr-2 text-vibrant-green"></i>
                Schedule Configuration
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Days Per Week -->
                <div>
                    <label for="days_per_week" class="block text-sm font-semibold text-gray-700 mb-2">
                        Days Per Week <span class="text-red-500">*</span>
                    </label>
                    <select name="days_per_week" id="days_per_week" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select days per week</option>
                        @for($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ old('days_per_week') == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 1 ? 'day' : 'days' }} per week
                            </option>
                        @endfor
                    </select>
                    @error('days_per_week')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Session Duration -->
                <div>
                    <label for="session_duration" class="block text-sm font-semibold text-gray-700 mb-2">
                        Session Duration <span class="text-red-500">*</span>
                    </label>
                    <select name="session_duration" id="session_duration" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                        <option value="">Select duration</option>
                        <option value="30" {{ old('session_duration') == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="60" {{ old('session_duration') == '60' ? 'selected' : '' }}>60 minutes (1 hour)</option>
                    </select>
                    @error('session_duration')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-dollar-sign mr-2 text-vibrant-green"></i>
                Pricing (Monthly)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- CAD Price -->
                <div>
                    <label for="price_cad" class="block text-sm font-semibold text-gray-700 mb-2">
                        CAD Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">CA$</span>
                        <input type="number" name="price_cad" id="price_cad" step="0.01" min="0" 
                            value="{{ old('price_cad') }}" required
                            placeholder="0.00"
                            class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    </div>
                    @error('price_cad')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- USD Price -->
                <div>
                    <label for="price_usd" class="block text-sm font-semibold text-gray-700 mb-2">
                        USD Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="price_usd" id="price_usd" step="0.01" min="0" 
                            value="{{ old('price_usd') }}" required
                            placeholder="0.00"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    </div>
                    @error('price_usd')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- GBP Price -->
                <div>
                    <label for="price_gbp" class="block text-sm font-semibold text-gray-700 mb-2">
                        GBP Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">£</span>
                        <input type="number" name="price_gbp" id="price_gbp" step="0.01" min="0" 
                            value="{{ old('price_gbp') }}" required
                            placeholder="0.00"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">
                    </div>
                    @error('price_gbp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-cog mr-2 text-vibrant-green"></i>
                Additional Settings
            </h2>

            <div class="space-y-4">
                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        placeholder="Add any notes about this pricing tier..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-vibrant-green focus:border-transparent">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="bg-vibrant-green text-white px-8 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-check mr-2"></i>Create Pricing Tier
            </button>
            <a href="{{ route('admin.pricing-tiers.index') }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fa-solid fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</x-dashboard-layout>
