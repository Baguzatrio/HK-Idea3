<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-blue-100/50 via-white to-blue-200/50 pt-6 sm:justify-center sm:pt-0">
        <div>
            <router-link to="/">
                <img src="\images\logo\logoHK.png" alt="" class="block h-20 w-auto fill-current">
            </router-link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>