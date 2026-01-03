<x-dashboard-layout title="Pricing Tiers">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pricing Tiers</h1>
                <p class="text-gray-600 text-sm">Manage reference pricing for different schedules</p>
            </div>
            <a href="{{ route('admin.pricing-tiers.create') }}" 
                class="bg-vibrant-green text-white px-6 py-3 rounded-lg hover:bg-deep-blue transition font-semibold">
                <i class="fa-solid fa-plus mr-2"></i>Add Pricing Tier
            </a>
        </div>
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

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Schedule
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CAD Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            USD Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            GBP Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pricingTiers as $tier)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-calendar-days text-vibrant-green mr-2"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $tier->getScheduleDescription() }}
                                        </div>
                                        @if($tier->notes)
                                            <div class="text-xs text-gray-500">{{ Str::limit($tier->notes, 30) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">
                                    CA${{ number_format($tier->price_cad, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($tier->price_usd, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">
                                    £{{ number_format($tier->price_gbp, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tier->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.pricing-tiers.edit', $tier->id) }}" 
                                    class="text-vibrant-green hover:text-deep-blue mr-3">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.pricing-tiers.destroy', $tier->id) }}" 
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this pricing tier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fa-solid fa-dollar-sign text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No pricing tiers found</p>
                                <a href="{{ route('admin.pricing-tiers.create') }}" 
                                    class="text-vibrant-green hover:text-deep-blue mt-2 inline-block">
                                    Create your first pricing tier
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pricingTiers->count() > 0)
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fa-solid fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                <div class="text-sm text-blue-700">
                    <p class="font-semibold mb-1">Reference Pricing</p>
                    <p>These prices serve as a reference when creating enrollments. Admins can use these suggested prices or set custom prices for each enrollment.</p>
                </div>
            </div>
        </div>
    @endif
</x-dashboard-layout>
