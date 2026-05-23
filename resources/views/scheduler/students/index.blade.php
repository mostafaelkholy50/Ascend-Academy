<x-dashboard-layout title="Manage Students">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Students & Timezones</h2>
        <div class="w-full md:w-80">
            <form action="{{ route('scheduler.students.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search students..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-vibrant-green focus:border-vibrant-green text-sm transition">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-search text-sm"></i>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" id="students-table-container">
        @include('scheduler.students.partials.table', ['students' => $students])
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const tableContainer = document.getElementById('students-table-container');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                const query = this.value;
                clearTimeout(timeout);
                
                timeout = setTimeout(() => {
                    tableContainer.style.opacity = '0.5';
                    fetch(`{{ route('scheduler.students.search') }}?search=${encodeURIComponent(query)}`)
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
