<x-dashboard-layout title="Manage Teachers">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Teachers</h2>
        <div class="w-full md:w-80">
            <form action="{{ route('scheduler.teachers.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search teachers..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-vibrant-green focus:border-vibrant-green text-sm transition">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-search text-sm"></i>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" id="teachers-table-container">
        @include('scheduler.teachers.partials.table', ['teachers' => $teachers])
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const tableContainer = document.getElementById('teachers-table-container');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                const query = this.value;
                clearTimeout(timeout);
                
                timeout = setTimeout(() => {
                    tableContainer.style.opacity = '0.5';
                    fetch(`{{ route('scheduler.teachers.search') }}?search=${encodeURIComponent(query)}`)
                        .then(response => response.text())
                        .then(html => {
                            tableContainer.innerHTML = html;
                            tableContainer.style.opacity = '1';
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            tableContainer.style.opacity = '1';
                        });
                }, 300);
            });

            // Prevent form submission
            searchInput.closest('form').addEventListener('submit', (e) => e.preventDefault());
        });
    </script>
    @endpush
</x-dashboard-layout>
