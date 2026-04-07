<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Permission {
    id: number;
    nama: string;
    nama_report: string;
    judul_report: string;
    link_dashboard: string;
}

interface Divisi {
    id: number;
    kode: string;
    nama: string;
    logo: string | null;
    no_urut: number;
    permissions: Permission[];
}

defineProps<{
    divisis: Divisi[];
}>();

// ── State ────────────────────────────────────────────────────
const activeDivisi = ref<Divisi | null>(null);
const activePermission = ref<Permission | null>(null);

const selectDivisi = (divisi: Divisi) => {
    if (activeDivisi.value?.id === divisi.id) {
        // Klik divisi yang sama = tutup
        activeDivisi.value = null;
        activePermission.value = null;
        return;
    }
    activeDivisi.value = divisi;
    activePermission.value = null;

    // Jika hanya ada 1 permission, langsung tampilkan
    if (divisi.permissions.length === 1) {
        activePermission.value = divisi.permissions[0];
    }

    setTimeout(() => {
         window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 350);
};

const selectPermission = (permission: Permission) => {
    if (activePermission.value?.id === permission.id) {
        activePermission.value = null;
        return;
    }
    activePermission.value = permission;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-8 space-y-6">

            <!-- ── Section Dashboard (muncul setelah pilih divisi) ── -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 -translate-y-3"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-3"
            >
                <div v-if="activeDivisi" id="dashboard-section" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">

                        <!-- Header: Judul -->
                        <div class="px-6 pt-6 pb-2 flex items-start justify-between">
                            <div class="flex-1 text-center">
                                <!-- Judul = nama_report dari permission aktif, fallback nama divisi -->
                                <h2 class="text-2xl font-bold text-gray-800">
                                    {{ activePermission?.judul_report || activeDivisi.nama }}
                                </h2>
                                <div class="mt-2 mx-auto w-60 h-0.5 bg-red-500 rounded"></div>
                            </div>
                        </div>

                        <!-- Tombol-tombol Permission/Report -->
                        <div v-if="activeDivisi.permissions.length > 0" class="px-6 py-4 flex flex-wrap justify-center gap-2">
                            <button
                                v-for="perm in activeDivisi.permissions"
                                :key="perm.id"
                                @click="selectPermission(perm)"
                                :class="[
                                    'px-4 py-1.5 text-sm rounded border transition',
                                    activePermission?.id === perm.id
                                        ? 'bg-blue-900 text-white border-blue-900'
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400 hover:text-blue-900'
                                ]"
                            >
                                {{ perm.nama_report }}
                            </button>
                        </div>

                        <div v-else class="px-6 py-4 text-center text-sm text-gray-400 italic">
                            Divisi ini belum memiliki report.
                        </div>

                        <!-- iframe -->
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                        >
                            <div v-if="activePermission && activePermission.link_dashboard" class="border-t border-gray-100 mt-2">
                                <iframe
                                    :src="activePermission.link_dashboard"
                                    :title="activePermission.nama_report"
                                    class="w-full"
                                    style="height: 65vh; border: none;"
                                    allowfullscreen
                                />
                            </div>
                        </Transition>

                        <!-- Padding bawah -->
                        <div class="pb-4"></div>
                    </div>
                </div>
            </Transition>

            <!-- ── Grid Divisi ── -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-md px-8 py-8">

                    <!-- Judul -->
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800">Hutama Karya</h2>
                        <div class="mt-2 mx-auto w-60 h-0.5 bg-red-500 rounded"></div>
                    </div>

                    <!-- Grid: 6 per baris, rata tengah -->
                    <div v-if="divisis.length === 0" class="text-center py-10 text-gray-400 text-sm">
                        Belum ada divisi yang terdaftar.
                    </div>

                    <div
                        v-else
                        class="flex flex-wrap justify-center gap-4"
                    >
                        <button
                            v-for="divisi in divisis"
                            :key="divisi.id"
                            @click="selectDivisi(divisi)"
                            :class="[
                                'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all duration-200',
                                'w-[calc(100%/6-1rem)] min-w-[100px] max-w-[140px]',
                                activeDivisi?.id === divisi.id
                                    ? 'border-blue-900 bg-blue-50 shadow-md scale-105'
                                    : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50 hover:scale-105'
                            ]"
                        >
                            <!-- Logo -->
                            <div class="w-16 h-16 flex items-center justify-center rounded-lg overflow-hidden bg-white ">
                                <img
                                    v-if="divisi.logo"
                                    :src="`/storage/${divisi.logo}`"
                                    :alt="divisi.nama"
                                    class="max-w-full max-h-full object-contain p-1"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-800 font-bold text-2xl"
                                >
                                    {{ divisi.nama.charAt(0).toUpperCase() }}
                                </div>
                            </div>

                            <!-- Kode Divisi -->
                            <span class="text-xs font-medium text-gray-700 text-center leading-tight line-clamp-2">
                                {{ divisi.kode }}
                            </span>

                            <!-- Indikator aktif -->
                            <span
                                v-if="activeDivisi?.id === divisi.id"
                                class=""
                            />
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>