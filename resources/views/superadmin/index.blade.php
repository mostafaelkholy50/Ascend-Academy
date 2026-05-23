<x-dashboard-layout title="SuperAdmin - Users & Roles">
    <div class="space-y-8 animate-fade-in">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-white py-6 px-8 rounded-2xl shadow-sm border border-gray-100">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Access Control Center</h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-vibrant-green rounded-full animate-pulse"></span>
                        Define the hierarchy of authority and individual user capabilities.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('superadmin.roles.index') }}" class="group px-5 py-2.5 bg-white text-deep-blue border border-deep-blue/10 rounded-xl hover:bg-deep-blue hover:text-white transition-all duration-300 flex items-center shadow-sm text-sm">
                        <i class="fa-solid fa-shield-halved mr-2 group-hover:rotate-12 transition-transform"></i> 
                        <span class="font-bold">Role Definitions</span>
                    </a>
                    <button type="button" onclick="openCreateUserModal()" class="group px-5 py-2.5 bg-vibrant-green text-white rounded-xl hover:bg-black transition-all duration-300 flex items-center shadow-lg shadow-vibrant-green/10 text-sm">
                        <i class="fa-solid fa-user-plus mr-2 group-hover:scale-110 transition-transform"></i> 
                        <span class="font-bold">Create New Account</span>
                    </button>
                </div>
            </div>
            <!-- Decorative background element -->
            <div class="absolute -right-5 -top-5 w-24 h-24 bg-vibrant-green/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Users Management Card -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div>
                    <h3 class="text-xl font-black text-gray-800">Academy Personnel</h3>
                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">Total Accounts: {{ $users->total() }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <!-- Role Filter -->
                    <div class="relative min-w-[180px]">
                        <select id="roleFilter" class="w-full pl-10 pr-8 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all appearance-none cursor-pointer font-bold text-gray-600">
                            <option value="all">All Roles</option>
                            <option value="none">No Role Assigned</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px] pointer-events-none"></i>
                    </div>
                    <!-- Name Search -->
                    <div class="relative w-full sm:w-80">
                        <input type="text" id="userSearch" placeholder="Filter by identity..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-medium">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-300"></i>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-widest font-black">
                            <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Identify</th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Authority Matrix</th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Region</th>
                            <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                            <tr class="user-row group hover:bg-gray-50/40 transition-colors" data-roles="{{ json_encode($user->roles->pluck('name')) }}">
                                <td class="px-10 py-6">
                                    <div class="flex items-center">
                                        <div class="relative">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-12 h-12 rounded-2xl object-cover shadow-sm ring-2 ring-white">
                                            @else
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 shadow-sm ring-2 ring-white">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                            @endif
                                            @if($user->hasRole('SuperAdmin'))
                                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full shadow-sm"></div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-bold text-gray-800 group-hover:text-vibrant-green transition-colors user-name">{{ $user->name }}</div>
                                            <div class="text-[11px] text-gray-400 font-medium user-email">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col gap-2">
                                        <!-- Roles -->
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($user->roles as $role)
                                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm
                                                    {{ $role->name === 'SuperAdmin' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-gray-300 text-[10px] italic">No defined roles</span>
                                            @endforelse
                                        </div>
                                        <!-- Extra Direct Permissions -->
                                        @if($user->permissions->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($user->permissions as $perm)
                                                    <span class="px-2 py-0.5 rounded-md bg-vibrant-green/5 text-vibrant-green text-[9px] font-bold border border-vibrant-green/10">
                                                        <i class="fa-solid fa-plus text-[7px] mr-1"></i>{{ $perm->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-1.5 text-vibrant-green">
                                        <i class="fa-solid fa-location-dot text-[9px]"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $user->country ?? 'Global' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex flex-col gap-1.5 w-full max-w-[140px] ml-auto">
                                        <!-- Edit Profile -->
                                        <button type="button" 
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone }}"
                                                data-country="{{ $user->country }}"
                                                onclick="initProfileModal(this)"
                                                class="flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-vibrant-green transition-all duration-300 text-[9px] font-black uppercase tracking-wider">
                                            <i class="fa-solid fa-user-pen"></i> Edit
                                        </button>

                                        <!-- Permissions / Access -->
                                        <button type="button" 
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone }}"
                                                data-country="{{ $user->country }}"
                                                data-roles='@json($user->roles->pluck("name"))'
                                                data-permissions='@json($user->permissions->pluck("name"))'
                                                data-allowed='@json($user->allowed_countries ?? [])'
                                                data-payroll="{{ $user->can_access_payroll ? '1' : '0' }}"
                                                onclick="initAuthorityModal(this)"
                                                class="flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-vibrant-green text-white hover:bg-deep-blue transition-all duration-300 text-[9px] font-black uppercase tracking-wider">
                                            <i class="fa-solid fa-shield-halved"></i> Access
                                        </button>

                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete user?');" class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 text-[9px] font-black uppercase tracking-wider">
                                                    <i class="fa-solid fa-trash-can"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-10 bg-gray-50/20 border-t border-gray-50">
                {{ $users->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- Profile Edit Modal -->
    <div id="profileEditModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-md transition-opacity" onclick="closeProfileModal()"></div>
            
            <div class="relative bg-white/95 rounded-[2.5rem] shadow-2xl max-w-xl w-full p-8 flex flex-col transform transition-all border border-white/20">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <div class="w-12 h-12 bg-gray-900 rounded-2xl flex items-center justify-center text-white mb-4 shadow-lg">
                            <i class="fa-solid fa-user-pen text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Edit Profile</h3>
                        <p class="text-gray-500 text-xs mt-1">Update personal and regional identity records.</p>
                    </div>
                    <button onclick="closeProfileModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>

                <form id="profileEditForm" method="POST" class="space-y-6">
                    @csrf
                    <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100 space-y-4">
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 ml-1">Full Name</label>
                            <input type="text" name="name" id="editProfileName" class="w-full px-5 py-3 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 focus:ring-2 focus:ring-vibrant-green/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 ml-1">Email Address</label>
                            <input type="email" name="email" id="editProfileEmail" class="w-full px-5 py-3 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 focus:ring-2 focus:ring-vibrant-green/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 ml-1">Phone Number</label>
                            <input type="text" name="phone" id="editProfilePhone" class="w-full px-5 py-3 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 focus:ring-2 focus:ring-vibrant-green/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 ml-1">Region / Country</label>
                            <div class="relative">
                                <select name="country" id="editProfileCountry" class="w-full px-5 py-3 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 appearance-none cursor-pointer focus:ring-2 focus:ring-vibrant-green/20 transition-all">
                                    <option value="">No Specific Country Assigned</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-location-dot absolute right-5 top-1/2 -translate-y-1/2 text-vibrant-green text-xs"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 ml-1">New Password (Optional)</label>
                            <div class="relative">
                                <input type="password" name="password" id="editProfilePassword" placeholder="Leave blank to keep current password" class="w-full px-5 py-3 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 focus:ring-2 focus:ring-vibrant-green/20 transition-all">
                                <i class="fa-solid fa-key absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full px-8 py-4 bg-gray-900 text-white rounded-2xl hover:bg-vibrant-green transition-all duration-300 font-black text-[12px] uppercase tracking-widest shadow-lg">Save Profile Updates</button>
                        <button type="button" onclick="closeProfileModal()" class="w-full px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 transition-all font-black text-[11px] uppercase tracking-widest">Discard Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- System Authority Modal -->
    <div id="authorityModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-md transition-opacity" onclick="closeAuthorityModal()"></div>
            
            <div class="relative bg-white/95 rounded-[2rem] shadow-2xl max-w-4xl w-full p-6 flex flex-col transform transition-all border border-white/20">
                <div class="mb-4 flex justify-between items-center border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-vibrant-green text-white rounded-xl flex items-center justify-center shadow-lg shadow-vibrant-green/20">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight" id="authorityModalTitle">System Authority</h3>
                    </div>
                    <button onclick="closeAuthorityModal()" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <form id="authorityForm" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="name" id="authModalName">
                    <input type="hidden" name="email" id="authModalEmail">
                    <input type="hidden" name="phone" id="authModalPhone">
                    <input type="hidden" name="country" id="authModalCountry">

                    <!-- Section 2: Authority & System Access -->
                    <div class="bg-gray-900 p-5 rounded-2xl shadow-xl">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Roles Section -->
                            <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Roles</label>
                                <div class="space-y-1.5">
                                    @foreach($roles as $role)
                                        <label class="group flex items-center p-2 rounded-lg bg-white/5 border border-transparent hover:border-vibrant-green/20 cursor-pointer transition-all">
                                            <div class="relative flex items-center justify-center">
                                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                                       onchange="syncInheritedPermissions()"
                                                       data-permissions="{{ json_encode($role->permissions->pluck('name')) }}"
                                                       class="role-checkbox peer appearance-none w-3.5 h-3.5 border border-white/10 rounded checked:bg-vibrant-green checked:border-vibrant-green transition-all">
                                                <i class="fa-solid fa-check absolute text-white text-[7px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </div>
                                            <div class="ml-2">
                                                <span class="block text-[10px] font-black text-white/90 group-hover:text-vibrant-green transition-colors">{{ $role->name }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Permissions Section -->
                            <div class="md:col-span-2 bg-white/5 p-4 rounded-xl border border-white/5">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Individual Capabilities</label>
                                    <div class="relative">
                                        <input type="text" id="modalPermissionFilter" placeholder="Filter..." class="w-20 pl-6 pr-1 py-1 bg-white/5 border-none rounded-lg text-[8px] text-white focus:ring-1 focus:ring-vibrant-green/20">
                                        <i class="fa-solid fa-filter absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[7px]"></i>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-1" id="modalPermissionsGrid">
                                    @foreach($permissions as $permission)
                                        <label class="modal-permission-item group flex items-center p-2 rounded-lg border border-white/5 hover:bg-white/5 cursor-pointer transition-all" data-name="{{ strtolower($permission->name) }}">
                                            <div class="relative flex items-center justify-center">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                       onchange="handlePermissionToggle(this)"
                                                       class="permission-checkbox peer appearance-none w-3.5 h-3.5 border border-white/10 rounded checked:bg-vibrant-green checked:border-vibrant-green transition-all disabled:opacity-50">
                                                <i class="fa-solid fa-check absolute text-white text-[6px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                <div class="inherited-badge absolute -left-1 -top-1 hidden">
                                                    <i class="fa-solid fa-link text-[6px] text-vibrant-green bg-white rounded-full p-0.5"></i>
                                                </div>
                                            </div>
                                            <div class="ml-2">
                                                <span class="block text-[9px] font-bold text-white/80 permission-label truncate">{{ $permission->name }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Regional Authority (For Accountants) -->
                    <div id="locationSection" class="bg-vibrant-green/5 p-5 rounded-2xl border border-vibrant-green/10 hidden space-y-4">
                        <div class="flex items-center justify-between border-b border-vibrant-green/10 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-vibrant-green shadow-sm">
                                    <i class="fa-solid fa-money-bill-transfer text-xs"></i>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest">Teacher Payroll Access</label>
                                    <p class="text-[8px] text-gray-400 font-bold">Grant permission to view and manage teacher financial records.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="can_access_payroll" value="1" id="payrollToggle" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-vibrant-green"></div>
                            </label>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-earth-americas text-vibrant-green text-xs"></i>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Accounting Access (Students)</label>
                                </div>
                                <button type="button" onclick="toggleAllCountries()" id="toggleCountriesBtn" class="text-[8px] font-black text-vibrant-green uppercase tracking-tighter bg-white px-2 py-1 rounded-md border border-vibrant-green/10 hover:bg-vibrant-green hover:text-white transition-all">Select All</button>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($countries as $country)
                                    <label class="flex items-center px-3 py-1 bg-white border border-gray-100 rounded-lg cursor-pointer hover:border-vibrant-green/30 transition-all shadow-sm">
                                        <input type="checkbox" name="allowed_countries[]" value="{{ $country }}" class="country-checkbox peer hidden">
                                        <span class="text-[9px] font-bold text-gray-500 peer-checked:text-vibrant-green transition-colors">{{ $country }}</span>
                                        <i class="fa-solid fa-check ml-1.5 text-[7px] text-vibrant-green opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 py-3 bg-vibrant-green text-white rounded-xl hover:bg-deep-blue transition-all font-black text-[10px] uppercase tracking-widest shadow-lg">Save Authority</button>
                        <button type="button" onclick="closeAuthorityModal()" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all font-black text-[10px] uppercase tracking-widest">Discard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Create User Modal -->
    <div id="createUserModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-md transition-opacity" onclick="closeCreateUserModal()"></div>
            
            <div class="relative bg-white/95 rounded-[2.5rem] shadow-2xl max-w-xl w-full p-8 overflow-hidden transform transition-all border border-white/20">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <div class="w-12 h-12 bg-vibrant-green/10 rounded-2xl flex items-center justify-center text-vibrant-green mb-4 shadow-inner">
                            <i class="fa-solid fa-user-plus text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Create Personnel Account</h3>
                        <p class="text-gray-500 text-xs mt-1">Register a new staff member and assign their initial authority.</p>
                    </div>
                    <button onclick="closeCreateUserModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('superadmin.users.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-8">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Full Identity Name</label>
                            <input type="text" name="name" required placeholder="e.g. John Doe" value="{{ old('name') }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-medium @error('name') ring-2 ring-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-[10px] mt-1 ml-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                            <input type="email" name="email" required placeholder="e.g. john@ascend.com" value="{{ old('email') }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-medium @error('email') ring-2 ring-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-[10px] mt-1 ml-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Phone Number</label>
                            <input type="text" name="phone" placeholder="e.g. +1 234 567 890" value="{{ old('phone') }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-medium">
                        </div>

                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Assigned Region (Country)</label>
                            <select name="country" class="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-bold text-gray-600 appearance-none cursor-pointer">
                                <option value="">Select Region (Optional)...</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Access Password</label>
                                <input type="password" name="password" required placeholder="••••••••" 
                                       class="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-medium @error('password') ring-2 ring-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-[10px] mt-1 ml-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Initial Role</label>
                                <select name="roles[]" required class="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-vibrant-green/20 transition-all font-bold text-gray-600 appearance-none cursor-pointer @error('roles') ring-2 ring-red-500 @enderror">
                                    <option value="" disabled selected>Select Authority...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ (is_array(old('roles')) && in_array($role->name, old('roles'))) ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('roles')
                                    <p class="text-red-500 text-[10px] mt-1 ml-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" onclick="closeCreateUserModal()" class="flex-1 px-6 py-4 bg-gray-50 text-gray-500 rounded-xl hover:bg-gray-100 transition font-black text-[10px] uppercase tracking-widest">Discard</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-vibrant-green text-white rounded-xl hover:bg-black hover:shadow-xl hover:shadow-vibrant-green/10 transition-all duration-300 font-black text-[10px] uppercase tracking-widest">Initialize Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .permission-inherited {
            border-color: rgba(34, 197, 94, 0.1) !important;
            background-color: rgba(34, 197, 94, 0.03) !important;
        }
        .permission-inherited .permission-label {
            color: #16a34a !important;
        }
        .permission-inherited .inherited-badge,
        .permission-inherited .inherited-text {
            display: block !important;
        }
    </style>

    <script>
        // Integrated Search & Role Filter Logic
        const userSearch = document.getElementById('userSearch');
        const roleFilter = document.getElementById('roleFilter');
        const userRows = document.querySelectorAll('.user-row');

        function filterUsers() {
            const searchTerm = userSearch.value.toLowerCase();
            const selectedRole = roleFilter.value;

            userRows.forEach(row => {
                const name = row.querySelector('.user-name').innerText.toLowerCase();
                const email = row.querySelector('.user-email').innerText.toLowerCase();
                const roles = JSON.parse(row.dataset.roles || '[]');
                
                // Text search match
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                
                // Role filter match
                let matchesRole = true;
                if (selectedRole === 'none') {
                    matchesRole = roles.length === 0;
                } else if (selectedRole !== 'all') {
                    matchesRole = roles.includes(selectedRole);
                }

                if (matchesSearch && matchesRole) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        userSearch.addEventListener('input', filterUsers);
        roleFilter.addEventListener('change', filterUsers);

        // Modal Permission Filter
        const modalPermissionFilter = document.getElementById('modalPermissionFilter');
        modalPermissionFilter.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.modal-permission-item');
            items.forEach(item => {
                if (item.dataset.name.includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function handlePermissionToggle(cb) {
            const item = cb.closest('.modal-permission-item');
            if (item) {
                item.dataset.direct = cb.checked ? "true" : "false";
            }
            syncInheritedPermissions();
        }

        function syncInheritedPermissions() {
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            const permCheckboxes = document.querySelectorAll('.permission-checkbox');
            const permItems = document.querySelectorAll('.modal-permission-item');
            
            let inheritedPerms = new Set();
            let isSuperAdmin = false;
            let isAccountant = false;

            roleCheckboxes.forEach(cb => {
                if (cb.checked) {
                    if (cb.value === 'SuperAdmin') isSuperAdmin = true;
                    if (cb.value === 'Accountant') isAccountant = true;
                    try {
                        const perms = JSON.parse(cb.dataset.permissions);
                        perms.forEach(p => inheritedPerms.add(p));
                    } catch (e) { }
                }
            });

            const locationSection = document.getElementById('locationSection');
            
            // Logic: Show location section if "Accountant" role is selected 
            // OR if any "accounting" related permission is selected (direct or inherited)
            let hasAccountingAccess = isAccountant;
            
            if (!hasAccountingAccess) {
                permCheckboxes.forEach(cb => {
                    if (cb.checked && cb.value.toLowerCase().includes('accounting')) {
                        hasAccountingAccess = true;
                    }
                });
            }

            if (locationSection) {
                if (hasAccountingAccess) locationSection.classList.remove('hidden');
                else locationSection.classList.add('hidden');
            }

            permCheckboxes.forEach((cb, index) => {
                const item = permItems[index];
                if (isSuperAdmin || inheritedPerms.has(cb.value)) {
                    cb.checked = true;
                    cb.disabled = true;
                    item.classList.add('permission-inherited');
                } else {
                    cb.checked = item.dataset.direct === "true";
                    cb.disabled = false;
                    item.classList.remove('permission-inherited');
                }
            });
        }

        function initProfileModal(btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const email = btn.dataset.email;
            const phone = btn.dataset.phone;
            const country = btn.dataset.country;
            openProfileModal(id, name, email, phone, country);
        }

        function openProfileModal(id, name, email, phone, country) {
            const modal = document.getElementById('profileEditModal');
            const form = document.getElementById('profileEditForm');
            form.action = '/admin/superadmin/assign-role/' + id;
            
            document.getElementById('editProfileName').value = name;
            document.getElementById('editProfileEmail').value = email;
            document.getElementById('editProfilePhone').value = phone === 'null' ? '' : (phone || '');
            document.getElementById('editProfileCountry').value = country === 'null' ? '' : (country || '');
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            document.getElementById('editProfilePassword').value = '';
            document.getElementById('profileEditModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function toggleAllCountries() {
            const checkboxes = document.querySelectorAll('.country-checkbox');
            const btn = document.getElementById('toggleCountriesBtn');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => cb.checked = !allChecked);
            btn.innerText = !allChecked ? 'Deselect All' : 'Select All';
        }

        function initAuthorityModal(btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const email = btn.dataset.email;
            const phone = btn.dataset.phone;
            const country = btn.dataset.country;
            const roles = JSON.parse(btn.dataset.roles || '[]');
            const permissions = JSON.parse(btn.dataset.permissions || '[]');
            const allowed = JSON.parse(btn.dataset.allowed || '[]');
            const payroll = btn.dataset.payroll === '1';
            
            // Populate hidden fields to avoid NULL errors on update
            document.getElementById('authModalName').value = name;
            document.getElementById('authModalEmail').value = email;
            document.getElementById('authModalPhone').value = phone === 'null' ? '' : (phone || '');
            document.getElementById('authModalCountry').value = country === 'null' ? '' : (country || '');
            
            openAuthorityModal(id, name, roles, permissions, allowed, payroll);
        }

        function openAuthorityModal(userId, userName, userRoles, userPermissions, allowedCountries, hasPayrollAccess) {
            const modal = document.getElementById('authorityModal');
            const form = document.getElementById('authorityForm');
            form.action = '/admin/superadmin/assign-role/' + userId;
            
            document.getElementById('authorityModalTitle').innerText = userName + ' Authority';

            // Reset checkboxes
            document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.country-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('payrollToggle').checked = false;
            
            // Set values
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            const permCheckboxes = document.querySelectorAll('.permission-checkbox');
            const permItems = document.querySelectorAll('.modal-permission-item');

            permItems.forEach(item => {
                item.removeAttribute('data-direct');
                item.classList.remove('permission-inherited');
            });

            permCheckboxes.forEach((cb, index) => {
                if (userPermissions.includes(cb.value)) {
                    cb.checked = true;
                    permItems[index].dataset.direct = "true";
                }
            });

            roleCheckboxes.forEach(cb => {
                cb.checked = userRoles.includes(cb.value);
            });

            const countryCheckboxes = document.querySelectorAll('.country-checkbox');
            countryCheckboxes.forEach(cb => {
                cb.checked = allowedCountries.includes(cb.value);
            });

            document.getElementById('payrollToggle').checked = hasPayrollAccess;

            syncInheritedPermissions();

            // Update Select All button text
            const countryCheckboxes2 = document.querySelectorAll('.country-checkbox');
            const allChecked = Array.from(countryCheckboxes2).every(cb => cb.checked);
            const btn = document.getElementById('toggleCountriesBtn');
            if (btn) btn.innerText = (allChecked && countryCheckboxes2.length > 0) ? 'Deselect All' : 'Select All';
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAuthorityModal() {
            document.getElementById('authorityModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openCreateUserModal() {
            document.getElementById('createUserModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateUserModal() {
            document.getElementById('createUserModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Auto-open modal if validation errors exist
        @if($errors->hasAny(['name', 'email', 'password', 'roles']))
            document.addEventListener('DOMContentLoaded', function() {
                openCreateUserModal();
            });
        @endif
    </script>
</x-dashboard-layout>
