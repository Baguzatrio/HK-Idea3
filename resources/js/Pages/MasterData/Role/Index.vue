<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

interface Permission {
    id: number;
    nama: string;
}

interface Role {
    id: number;
    nama: string;
    permissions: Permission[];
}

const roles = ref<Role[]>([]);
const permissions = ref<Permission[]>([]);
const isLoading = ref(true);

const fetchRoles = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/roles');
        roles.value = response.data.roles;
        permissions.value = response.data.permissions;
    } catch (error) {
        console.error('Error fetching data:', error);
        Swal.fire('Error', 'Gagal mengambil data role', 'error');
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchRoles();
});

// ── Search & Pagination ──────────────────────────────────────
const search = ref('');
const perPage = ref(10);
const currentPage = ref(1);

const filtered = computed(() => {
    const q = search.value.toLowerCase();
    return roles.value.filter(r => r.nama.toLowerCase().includes(q));
});

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage.value));

const paginated = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filtered.value.slice(start, start + perPage.value);
});

const resetPage = () => { currentPage.value = 1; };

watch(currentPage, () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ── Modal Tambah ─────────────────────────────────────────────
const showAddModal = ref(false);
const processingAdd = ref(false);

const addForm = ref({
    nama: '',
    permissions: [] as number[],
    errors: {} as Record<string, string>,
});

const submitAdd = async () => {
    processingAdd.value = true;
    addForm.value.errors = {};
    try {
        const payload = { ...addForm.value };
        delete (payload as any).errors;

        await axios.post('/api/roles', payload);
        
        showAddModal.value = false;
        addForm.value.nama = '';
        addForm.value.permissions = [];
        
        Swal.fire('Berhasil!', 'Data role berhasil ditambahkan.', 'success');
        fetchRoles();
    } catch (error: any) {
        if (error.response?.data?.errors) {
            for (const key in error.response.data.errors) {
                addForm.value.errors[key] = error.response.data.errors[key][0];
            }
        }
    } finally {
        processingAdd.value = false;
    }
};

const toggleAddPermission = (id: number) => {
    const idx = addForm.value.permissions.indexOf(id);
    if (idx === -1) addForm.value.permissions.push(id);
    else addForm.value.permissions.splice(idx, 1);
};

// ── Modal Edit ───────────────────────────────────────────────
const showEditModal = ref(false);
const processingEdit = ref(false);
const editingRole = ref<Role | null>(null);

const editForm = ref({
    nama: '',
    permissions: [] as number[],
    errors: {} as Record<string, string>,
});

const openEdit = (role: Role) => {
    editingRole.value = role;
    editForm.value.nama = role.nama;
    editForm.value.permissions = role.permissions.map(p => p.id);
    editForm.value.errors = {};
    showEditModal.value = true;
};

const toggleEditPermission = (id: number) => {
    const idx = editForm.value.permissions.indexOf(id);
    if (idx === -1) editForm.value.permissions.push(id);
    else editForm.value.permissions.splice(idx, 1);
};

const submitEdit = async () => {
    if (!editingRole.value) return;
    processingEdit.value = true;
    editForm.value.errors = {};
    
    try {
        const payload = { ...editForm.value };
        delete (payload as any).errors;

        await axios.put(`/api/roles/${editingRole.value.id}`, payload);
        showEditModal.value = false;
        Swal.fire('Berhasil!', 'Data role berhasil diperbarui.', 'success');
        fetchRoles();
    } catch (error: any) {
        if (error.response?.data?.errors) {
            for (const key in error.response.data.errors) {
                editForm.value.errors[key] = error.response.data.errors[key][0];
            }
        }
    } finally {
        processingEdit.value = false;
    }
};

// ── Delete ───────────────────────────────────────────────────
const confirmDelete = (id: number) => {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data role yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/roles/${id}`);
                Swal.fire('Terhapus!', 'Data role berhasil dihapus.', 'success');
                fetchRoles();
            } catch (error) {
                Swal.fire('Error', 'Gagal menghapus data', 'error');
            }
        }
    });
};
</script>

<template>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Master Data — Role
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">

                    <!-- ── Toolbar ── -->
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <span>Tampilkan</span>
                                <select
                                    v-model="perPage"
                                    @change="resetPage"
                                    class="border border-gray-300 rounded-md px-2 py-1 text-sm w-14 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option :value="10">10</option>
                                    <option :value="20">20</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                                <span>entri</span>
                            </div>

                            <input
                                v-model="search"
                                @input="resetPage"
                                type="text"
                                placeholder="Cari nama role..."
                                class="border border-gray-300 rounded-md px-3 py-1.5 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <button
                            @click="showAddModal = true"
                            class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded-md transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Role
                        </button>
                    </div>

                    <!-- ── Tabel ── -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider w-20">Aksi</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Nama Role</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Permission</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider w-28">Jumlah Permission</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-if="paginated.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                        Tidak ada data yang ditemukan.
                                    </td>
                                </tr>

                                <tr
                                    v-for="(role, index) in paginated"
                                    :key="role.id"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td class="px-4 py-4 text-gray-400">
                                        {{ (currentPage - 1) * perPage + index + 1 }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button
                                                @click="openEdit(role)"
                                                class="inline-flex items-center justify-center w-7 h-7 bg-blue-400 hover:bg-blue-500 text-white rounded transition"
                                                title="Edit"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="confirmDelete(role.id)"
                                                class="inline-flex items-center justify-center w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded transition"
                                                title="Hapus"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 font-medium text-gray-800">{{ role.nama }}</td>

                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="perm in role.permissions.slice(0, 3)"
                                                :key="perm.id"
                                                class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full"
                                            >
                                                {{ perm.nama }}
                                            </span>
                                            <span
                                                v-if="role.permissions.length > 3"
                                                class="inline-block bg-gray-100 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full"
                                            >
                                                +{{ role.permissions.length - 3 }} lainnya
                                            </span>
                                            <span v-if="role.permissions.length === 0" class="text-gray-400 text-xs">-</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            {{ role.permissions.length }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ── Footer tabel ── -->
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-t border-gray-200 text-sm text-gray-600">
                        <span>
                            Menampilkan
                            {{ filtered.length === 0 ? 0 : (currentPage - 1) * perPage + 1 }}–{{ Math.min(currentPage * perPage, filtered.length) }}
                            dari {{ filtered.length }} entri
                        </span>

                        <div class="flex items-center gap-1">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 rounded border border-gray-300 disabled:opacity-40 hover:bg-gray-100 transition"
                            >&lsaquo;</button>

                            <button
                                v-for="p in totalPages"
                                :key="p"
                                @click="currentPage = p"
                                :class="[
                                    'px-3 py-1 rounded border transition',
                                    currentPage === p
                                        ? 'bg-blue-900 text-white border-blue-900'
                                        : 'border-gray-300 hover:bg-gray-100'
                                ]"
                            >{{ p }}</button>

                            <button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages || totalPages === 0"
                                class="px-3 py-1 rounded border border-gray-300 disabled:opacity-40 hover:bg-gray-100 transition"
                            >&rsaquo;</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Modal Tambah Role ── -->
        <Teleport to="body">
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showAddModal = false">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Tambah Role</h3>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitAdd">
                            <div class="px-6 py-5 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="addForm.nama"
                                        type="text"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Cth: Admin Akuntansi"
                                    />
                                    <p v-if="addForm.errors.nama" class="text-red-500 text-xs mt-1">{{ addForm.errors.nama }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Permission</label>
                                    <div v-if="permissions.length === 0" class="text-sm text-gray-400 italic">
                                        Belum ada permission tersedia.
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                                        <label
                                            v-for="perm in permissions"
                                            :key="perm.id"
                                            class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-blue-900"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="perm.id"
                                                :checked="addForm.permissions.includes(perm.id)"
                                                @change="toggleAddPermission(perm.id)"
                                                class="rounded border-gray-300 text-blue-900 focus:ring-blue-500"
                                            />
                                            {{ perm.nama }}
                                        </label>
                                    </div>
                                    <p v-if="addForm.errors.permissions" class="text-red-500 text-xs mt-1">{{ addForm.errors.permissions }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
                                <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-100 transition">Batal</button>
                                <button type="submit" :disabled="processingAdd" class="px-4 py-2 text-sm rounded-md bg-blue-900 hover:bg-blue-800 text-white font-medium transition disabled:opacity-50">
                                    {{ processingAdd ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- ── Modal Edit Role ── -->
        <Teleport to="body">
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showEditModal = false">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Edit Role</h3>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitEdit">
                            <div class="px-6 py-5 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="editForm.nama"
                                        type="text"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    <p v-if="editForm.errors.nama" class="text-red-500 text-xs mt-1">{{ editForm.errors.nama }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Permission</label>
                                    <div v-if="permissions.length === 0" class="text-sm text-gray-400 italic">
                                        Belum ada permission tersedia.
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                                        <label
                                            v-for="perm in permissions"
                                            :key="perm.id"
                                            class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-blue-900"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="perm.id"
                                                :checked="editForm.permissions.includes(perm.id)"
                                                @change="toggleEditPermission(perm.id)"
                                                class="rounded border-gray-300 text-blue-900 focus:ring-blue-500"
                                            />
                                            {{ perm.nama }}
                                        </label>
                                    </div>
                                    <p v-if="editForm.errors.permissions" class="text-red-500 text-xs mt-1">{{ editForm.errors.permissions }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-100 transition">Batal</button>
                                <button type="submit" :disabled="processingEdit" class="px-4 py-2 text-sm rounded-md bg-blue-900 hover:bg-blue-800 text-white font-medium transition disabled:opacity-50">
                                    {{ processingEdit ? 'Menyimpan...' : 'Update' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AuthenticatedLayout>
</template>