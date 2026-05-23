<x-dashboard-layout title="Manage Roles - SuperAdmin">
    <div class="space-y-8 animate-fade-in">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-white py-6 px-8 rounded-2xl shadow-sm border border-gray-100">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <a href="{{ route('superadmin.index') }}" class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 hover:bg-vibrant-green hover:text-white rounded-lg transition-all duration-300">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </a>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Role Definitions</h2>
                    </div>
                    <p class="text-sm text-gray-500 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-vibrant-green rounded-full"></span>
                        Configure permissions for each administrative level.
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative hidden md:block">
                        <input type="text" id="roleSearch" placeholder="Search roles..." class="w-48 pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-xs focus:ring-2 focus:ring-vibrant-green/20 transition">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
                    </div>
                    <button onclick="document.getElementById('addRoleModal').classList.remove('hidden')" class="px-5 py-2.5 bg-vibrant-green text-white rounded-xl hover:bg-deep-blue transition-all duration-300 flex items-center shadow-lg shadow-vibrant-green/10 font-bold text-sm">
                        <i class="fa-solid fa-plus mr-2"></i> Create Role
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6" id="rolesGrid">
            @foreach($roles as $role)
                <div class="role-card group bg-white rounded-[1.5rem] shadow-sm p-6 border border-gray-100 hover:shadow-lg hover:shadow-gray-200/40 transition-all duration-500 relative overflow-hidden" data-role-name="{{ strtolower($role->name) }}">
                    <!-- Accent bar -->
                    <div class="absolute top-0 left-0 w-full h-1 {{ $role->name === 'SuperAdmin' ? 'bg-red-500' : 'bg-vibrant-green' }}"></div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-black text-gray-800 role-title">{{ $role->name }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold">Access Matrix</p>
                            </div>
                        </div>
                        @if($role->name === 'SuperAdmin')
                            <span class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">Immortal</span>
                        @else
                            <span class="bg-vibrant-green/5 text-vibrant-green px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest">Active</span>
                        @endif
                    </div>

                    <form action="{{ route('superadmin.roles.update-permissions', $role->id) }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Capabilities</label>
                                <div class="relative w-32">
                                    <input type="text" placeholder="Filter..." onkeyup="filterPermissions(this, '{{ $role->id }}')" class="w-full pl-7 pr-2 py-1 bg-gray-50/50 border-none rounded-lg text-[9px] focus:ring-1 focus:ring-vibrant-green/20 transition">
                                    <i class="fa-solid fa-filter absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-300 text-[7px]"></i>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar mb-4" id="permissions-grid-{{ $role->id }}">
                                @foreach($permissions as $permission)
                                    <label class="permission-item group/item flex items-center p-2.5 rounded-xl border border-gray-50 hover:border-vibrant-green/20 hover:bg-vibrant-green/[0.01] cursor-pointer transition-all duration-200" data-permission-name="{{ strtolower($permission->name) }}">
                                        <div class="relative flex items-center justify-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                {{ $role->name === 'SuperAdmin' ? 'disabled checked' : '' }}
                                                onchange="syncRoleAccountingAccess('{{ $role->id }}')"
                                                class="role-permission-checkbox peer appearance-none w-4 h-4 border-2 border-gray-200 rounded-lg checked:bg-vibrant-green checked:border-vibrant-green transition-all">
                                            <i class="fa-solid fa-check absolute text-white text-[7px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                        <span class="ml-2.5 text-xs font-medium text-gray-600 group-hover/item:text-gray-900 transition-colors">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Accounting Access Section (Dynamic) -->
                            <div id="accounting-access-{{ $role->id }}" class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100 mb-6 hidden space-y-5">
                                <!-- Payroll Access -->
                                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-vibrant-green/10 text-vibrant-green rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-money-bill-transfer text-xs"></i>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black text-gray-900 uppercase tracking-widest">Teacher Payroll Access</label>
                                            <p class="text-[7px] text-gray-400 font-bold">Access teacher financial records.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="can_access_payroll" value="1" {{ $role->can_access_payroll ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-vibrant-green"></div>
                                    </label>
                                </div>

                                <!-- Regional Access -->
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center mb-1">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-earth-americas text-vibrant-green text-[10px]"></i>
                                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Accounting Access (Students)</label>
                                        </div>
                                        <button type="button" onclick="toggleAllRoleCountries('{{ $role->id }}')" id="toggleCountriesBtn-{{ $role->id }}" class="text-[7px] font-black text-vibrant-green uppercase tracking-tighter bg-white px-2 py-1 rounded-md border border-gray-100 hover:bg-vibrant-green hover:text-white transition-all">Select All</button>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @php $currentAllowed = is_array($role->allowed_countries) ? $role->allowed_countries : json_decode($role->allowed_countries ?? '[]', true); @endphp
                                        @foreach($countries as $country)
                                            <label class="flex items-center px-3 py-1 bg-white border border-gray-100 rounded-lg cursor-pointer hover:border-vibrant-green/30 transition-all shadow-sm">
                                                <input type="checkbox" name="allowed_countries[]" value="{{ $country }}" 
                                                       {{ in_array($country, $currentAllowed ?? []) ? 'checked' : '' }}
                                                       class="country-checkbox-{{ $role->id }} peer hidden">
                                                <span class="text-[9px] font-bold text-gray-500 peer-checked:text-vibrant-green transition-colors">{{ $country }}</span>
                                                <i class="fa-solid fa-check ml-1.5 text-[7px] text-vibrant-green opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($role->name !== 'SuperAdmin')
                            <button type="submit" class="w-full py-3 bg-gray-900 text-white rounded-xl hover:bg-black transition-all duration-300 text-xs font-black shadow-lg shadow-gray-200">
                                Sync Role
                            </button>
                        @else
                            <div class="flex items-center justify-center gap-2 bg-gray-50 py-3 rounded-xl border border-dashed border-gray-200">
                                <i class="fa-solid fa-lock text-gray-300 text-xs"></i>
                                <p class="text-[10px] text-gray-400 font-medium italic">Full Platform Access</p>
                            </div>
                        @endif
                    </form>
                </div>
            @endforeach

            <!-- Add Permission Card -->
            <div class="bg-gradient-to-br from-deep-blue to-black rounded-[1.5rem] shadow-xl p-6 text-white relative overflow-hidden flex flex-col justify-center">
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mb-4">
                        <i class="fa-solid fa-key text-vibrant-green text-lg"></i>
                    </div>
                    <h3 class="text-xl font-black mb-1 tracking-tight">Expand Authority</h3>
                    <p class="text-white/50 text-[11px] mb-6">Define a new system capability.</p>
                    
                    <form action="{{ route('superadmin.permissions.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="relative">
                            <input type="text" name="name" placeholder="Capability name..." required 
                                   class="w-full bg-white/10 border-white/10 rounded-xl focus:ring-2 focus:ring-vibrant-green px-4 py-3 text-xs placeholder:text-white/30 text-white transition-all">
                        </div>
                        <button type="submit" class="w-full py-3 bg-vibrant-green text-white rounded-xl hover:bg-white hover:text-deep-blue transition-all duration-300 font-black text-xs shadow-lg shadow-vibrant-green/10">
                            Register Permission
                        </button>
                    </form>
                </div>
                <!-- Decorative element -->
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-vibrant-green/10 rounded-full blur-3xl"></div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('addRoleModal').classList.add('hidden')"></div>
            
            <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 overflow-hidden transform transition-all border border-gray-100">
                <div class="mb-6 text-center">
                    <div class="w-14 h-14 bg-vibrant-green/10 rounded-2xl flex items-center justify-center text-vibrant-green mx-auto mb-4">
                        <i class="fa-solid fa-plus text-xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900">New Role Profile</h3>
                    <p class="text-gray-500 text-xs mt-1">Create a new organizational tier.</p>
                </div>

                <form action="{{ route('superadmin.roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-8">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Role Title</label>
                        <input type="text" name="name" required placeholder="e.g. Accountant" 
                               class="w-full bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-vibrant-green px-4 py-3 text-xs font-medium">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('addRoleModal').classList.add('hidden')" 
                                class="flex-1 px-5 py-3.5 bg-gray-50 text-gray-500 rounded-xl hover:bg-gray-100 transition font-black text-xs">Discard</button>
                        <button type="submit" 
                                class="flex-1 px-5 py-3.5 bg-deep-blue text-white rounded-xl hover:bg-black transition-all duration-300 shadow-xl shadow-deep-blue/10 font-black text-xs">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Role Search Logic
        const roleSearch = document.getElementById('roleSearch');
        const roleCards = document.querySelectorAll('.role-card');

        roleSearch.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            roleCards.forEach(card => {
                const roleName = card.dataset.roleName;
                if (roleName.includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Role-based Accounting Access Visibility Logic
        function syncRoleAccountingAccess(roleId) {
            const grid = document.getElementById('permissions-grid-' + roleId);
            const checkboxes = grid.querySelectorAll('.role-permission-checkbox');
            const section = document.getElementById('accounting-access-' + roleId);
            
            let hasAccounting = false;
            checkboxes.forEach(cb => {
                if (cb.checked && cb.value.toLowerCase().includes('accounting')) {
                    hasAccounting = true;
                }
            });

            if (section) {
                if (hasAccounting) section.classList.remove('hidden');
                else section.classList.add('hidden');
            }
        }

        function toggleAllRoleCountries(roleId) {
            const checkboxes = document.querySelectorAll('.country-checkbox-' + roleId);
            const btn = document.getElementById('toggleCountriesBtn-' + roleId);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => cb.checked = !allChecked);
            btn.innerText = !allChecked ? 'Deselect All' : 'Select All';
        }

        // Initialize visibility on load
        document.addEventListener('DOMContentLoaded', () => {
            @foreach($roles as $role)
                syncRoleAccountingAccess('{{ $role->id }}');
            @endforeach
        });
    </script>
</x-dashboard-layout>
