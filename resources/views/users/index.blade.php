<x-app-layout>
<x-slot name="title">Pengelolaan Pengguna</x-slot>

@vite(['resources/css/users.css'])

<div class="us-wrap">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="us-flash us-flash--ok">
            <i class="ti ti-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="us-page-header">
        <div>
            <h1 class="us-page-title">Pengelolaan Pengguna</h1>
            <p class="us-page-sub">Kelola akun pengguna dan akses laboratorium</p>
        </div>
        <button class="us-btn-primary" onclick="toggleAdd()">
            <i class="ti ti-user-plus"></i>
            Tambah Pengguna
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="us-stats">
        <div class="us-stat">
            <div class="us-stat-icon us-stat-icon--all">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <div class="us-stat-val">{{ $users->count() }}</div>
                <div class="us-stat-key">Total</div>
            </div>
        </div>
        <div class="us-stat">
            <div class="us-stat-icon us-stat-icon--admin">
                <i class="ti ti-shield"></i>
            </div>
            <div>
                <div class="us-stat-val">{{ $users->where('role', 'admin')->count() }}</div>
                <div class="us-stat-key">Admin</div>
            </div>
        </div>
        <div class="us-stat">
            <div class="us-stat-icon us-stat-icon--operator">
                <i class="ti ti-settings"></i>
            </div>
            <div>
                <div class="us-stat-val">{{ $users->where('role', 'operator')->count() }}</div>
                <div class="us-stat-key">Operator</div>
            </div>
        </div>
        <div class="us-stat">
            <div class="us-stat-icon us-stat-icon--teknisi">
                <i class="ti ti-wrench"></i>
            </div>
            <div>
                <div class="us-stat-val">{{ $users->where('role', 'teknisi')->count() }}</div>
                <div class="us-stat-key">Teknisi</div>
            </div>
        </div>
    </div>

    {{-- Add User Card --}}
    <div class="us-add-card" id="add-card">
        <button class="us-add-header" onclick="toggleAdd()" type="button" aria-expanded="false" id="add-toggle-btn">
            <div class="us-add-header-left">
                <span class="us-add-icon"><i class="ti ti-user-plus"></i></span>
                <div>
                    <div class="us-add-title">Tambah Pengguna Baru</div>
                    <div class="us-add-sub">Buat akun baru dengan akses sesuai kebutuhan</div>
                </div>
            </div>
            <i class="ti ti-chevron-down us-add-chevron" id="add-chevron"></i>
        </button>
        <div class="us-add-body" id="add-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="us-field-row">
                    <div class="us-field">
                        <label class="us-label">Nama Lengkap <span class="us-required">*</span></label>
                        <input type="text" name="full_name" class="us-input" required value="{{ old('full_name') }}">
                    </div>
                    <div class="us-field">
                        <label class="us-label">Username <span class="us-required">*</span></label>
                        <input type="text" name="username" class="us-input" required value="{{ old('username') }}">
                    </div>
                </div>
                <div class="us-field-row">
                    <div class="us-field">
                        <label class="us-label">Email</label>
                        <input type="email" name="email" class="us-input" value="{{ old('email') }}">
                    </div>
                    <div class="us-field">
                        <label class="us-label">Nomor HP</label>
                        <input type="text" name="phone" class="us-input" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="us-field-row">
                    <div class="us-field">
                        <label class="us-label">Role <span class="us-required">*</span></label>
                        <select name="role" class="us-select" required id="add-role" onchange="toggleLabAssignment('add')">
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="teknisi" {{ old('role') == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                            <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        </select>
                    </div>
                    <div class="us-field">
                        <label class="us-label">Password <span class="us-required">*</span></label>
                        <input type="password" name="password" class="us-input" required>
                    </div>
                </div>
                <div class="us-field" id="add-lab-assignment" style="display: none;">
                    <label class="us-label">Lab yang Ditugaskan</label>
                    <div class="us-lab-group">
                        @foreach($resources as $resource)
                        <label class="us-lab-checkbox">
                            <input type="checkbox" name="resource_ids[]" value="{{ $resource->id }}">
                            <span class="us-lab-label">{{ $resource->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="us-add-footer">
                    <button type="submit" class="us-btn-submit">
                        <i class="ti ti-check"></i>
                        Simpan Pengguna
                    </button>
                    <button type="button" class="us-btn-cancel" onclick="toggleAdd()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="us-table-card">
        <div class="us-table-header">
            <div>
                <div class="us-table-title">
                    <i class="ti ti-users"></i>
                    Daftar Pengguna
                </div>
                <div class="us-table-count">{{ $users->count() }} pengguna terdaftar</div>
            </div>
            <div class="us-table-search">
                <i class="ti ti-search"></i>
                <input type="text" id="search-input" placeholder="Cari nama, username..." oninput="filterTable(this.value)">
            </div>
        </div>

        <div class="us-table-wrap">
            <table class="us-tbl" id="users-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Lab yang Ditugaskan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr data-search="{{ strtolower($user->full_name . ' ' . $user->username . ' ' . $user->email) }}">
                        <td>
                            <div class="us-user-cell">
                                @php
                                    $initials = collect(explode(' ', $user->full_name))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                                @endphp
                                <div class="us-avatar us-avatar--{{ $user->role }}">{{ $initials }}</div>
                                <div>
                                    <div class="us-user-name">{{ $user->full_name }}</div>
                                    <div class="us-user-username">@ {{ $user->username }}</div>
                                    @if($user->email)
                                    <div class="us-user-email">{{ $user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="us-badge us-badge--{{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->role == 'admin' || $user->role == 'guru')
                                <span class="us-lab-tag us-lab-tag-all">Semua Lab</span>
                            @else
                                <div class="us-lab-tags">
                                    @if($user->assigned_labs->count() > 0)
                                        @foreach($user->assigned_labs as $resource)
                                        <span class="us-lab-tag">{{ $resource->name }}</span>
                                        @endforeach
                                    @else
                                        <span style="color: var(--muted); font-size: 12px;">Belum ada lab</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="us-badge" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                                Aktif
                            </span>
                        </td>
                        <td>
                            <div class="us-actions">
                                <button class="us-btn-edit" onclick="openEditModal({{ $user->id }})">
                                    <i class="ti ti-edit"></i>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="us-btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="us-empty">
                            <i class="ti ti-users-off"></i>
                            <div>Belum ada pengguna</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="us-modal-overlay" id="edit-modal">
    <div class="us-modal">
        <div class="us-modal-header">
            <div class="us-modal-title">Edit Pengguna</div>
            <button class="us-modal-close" onclick="closeEditModal()">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <form method="POST" id="edit-form">
            @csrf
            @method('PUT')
            <div class="us-modal-body">
                <div class="us-field-row">
                    <div class="us-field">
                        <label class="us-label">Nama Lengkap <span class="us-required">*</span></label>
                        <input type="text" name="full_name" id="edit-full-name" class="us-input" required>
                    </div>
                    <div class="us-field">
                        <label class="us-label">Username <span class="us-required">*</span></label>
                        <input type="text" name="username" id="edit-username" class="us-input" required>
                    </div>
                </div>
                <div class="us-field-row">
                    <div class="us-field">
                        <label class="us-label">Email</label>
                        <input type="email" name="email" id="edit-email" class="us-input">
                    </div>
                    <div class="us-field">
                        <label class="us-label">Nomor HP</label>
                        <input type="text" name="phone" id="edit-phone" class="us-input">
                    </div>
                </div>
                <div class="us-field-row">
                    <div class="us-field">
                        <label class="us-label">Role <span class="us-required">*</span></label>
                        <select name="role" id="edit-role" class="us-select" required onchange="toggleLabAssignment('edit')">
                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                            <option value="teknisi">Teknisi</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>
                    <div class="us-field">
                        <label class="us-label">Password (Opsional)</label>
                        <input type="password" name="password" class="us-input" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>
                <div class="us-field" id="edit-lab-assignment">
                    <label class="us-label">Lab yang Ditugaskan</label>
                    <div class="us-lab-group" id="edit-lab-checkboxes">
                    </div>
                </div>
            </div>
            <div class="us-modal-footer">
                <button type="button" class="us-btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="us-btn-submit">
                    <i class="ti ti-check"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/users.js'])

<script>
    // Use preloaded users data with assigned_labs
    const usersData = @json($users);
    const resources = @json($resources);

    function toggleAdd() {
        const body = document.getElementById('add-body');
        const chevron = document.getElementById('add-chevron');
        const btn = document.getElementById('add-toggle-btn');
        
        body.classList.toggle('open');
        chevron.classList.toggle('open');
        btn.setAttribute('aria-expanded', body.classList.contains('open'));
    }

    function toggleLabAssignment(type) {
        const roleSelect = document.getElementById(type === 'add' ? 'add-role' : 'edit-role');
        const labDiv = document.getElementById(type === 'add' ? 'add-lab-assignment' : 'edit-lab-assignment');
        const role = roleSelect.value;
        
        if (role === 'operator' || role === 'teknisi') {
            labDiv.style.display = 'block';
        } else {
            labDiv.style.display = 'none';
        }
    }

    function openEditModal(userId) {
        const user = usersData.find(u => u.id === userId);
        if (!user) return;

        const form = document.getElementById('edit-form');
        form.action = `/users/${userId}`;

        document.getElementById('edit-full-name').value = user.full_name;
        document.getElementById('edit-username').value = user.username;
        document.getElementById('edit-email').value = user.email || '';
        document.getElementById('edit-phone').value = user.phone || '';
        document.getElementById('edit-role').value = user.role;

        // Render lab checkboxes
        const labCheckboxes = document.getElementById('edit-lab-checkboxes');
        labCheckboxes.innerHTML = '';
        const assignedLabIds = user.assigned_labs ? user.assigned_labs.map(r => r.id) : [];

        resources.forEach(resource => {
            const checked = assignedLabIds.includes(resource.id) ? 'checked' : '';
            const label = document.createElement('label');
            label.className = 'us-lab-checkbox';
            label.innerHTML = `
                <input type="checkbox" name="resource_ids[]" value="${resource.id}" ${checked}>
                <span class="us-lab-label">${resource.name}</span>
            `;
            labCheckboxes.appendChild(label);
        });

        toggleLabAssignment('edit');

        document.getElementById('edit-modal').classList.add('open');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.remove('open');
    }

    function filterTable(query) {
        const rows = document.querySelectorAll('#users-table tbody tr');
        const lowerQuery = query.toLowerCase();

        rows.forEach(row => {
            if (row.querySelector('.us-empty')) return;
            
            const searchText = row.dataset.search || '';
            row.style.display = searchText.includes(lowerQuery) ? '' : 'none';
        });
    }

    // Close modal on outside click
    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });
</script>

</x-app-layout>
