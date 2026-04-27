<script setup lang="ts">
defineProps<{
    to?: string;
    as?: string;
    method?: string;
}>();
</script>

<template>
    <button v-if="as === 'button'"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    >
        <slot />
    </button>
    <router-link v-else
        :to="to || ''"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    >
        <slot />
    </router-link>
</template>
