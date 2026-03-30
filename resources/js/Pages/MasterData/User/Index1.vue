<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    divisi: string;
    role: string;
}

defineProps<{
    users: User[];
}>();

const confirmDelete = (id: number) => {
    if (confirm('Yakin ingin menghapus user ini?')) {
        router.delete(route('users.destroy', id));
    }
};
</script>

<template>
    <Head title="Master Data - User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Master Data - User
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">

                    <!-- Header tabel -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700">Daftar User</h3>
                        <a
                            :href="route('users.create')"
                            class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded-md transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah User
                        </a>
                    </div>

                    <!-- Tabel -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                    <th class="px-6 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Divisi</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <!-- Jika data kosong -->
                                <tr v-if="users.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                        Belum ada data user.
                                    </td>
                                </tr>

                                <!-- Data rows -->
                                <tr
                                    v-for="(user, index) in users"
                                    :key="user.id"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td class="px-6 py-4 text-gray-400">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ user.name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ user.email }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ user.divisi }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Edit -->
                                            <a
                                                :href="route('users.edit', user.id)"
                                                class="inline-flex items-center gap-1 bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-medium px-3 py-1.5 rounded transition"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>

                                            <!-- Tombol Hapus -->
                                            <button
                                                @click="confirmDelete(user.id)"
                                                class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-3 py-1.5 rounded transition"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>