<script setup>
import { inject, onMounted, onUnmounted } from 'vue';

const select = inject('select');

const handleClickOutside = (event) => {
    if (select.open && !event.target.closest('.select-container')) {
        select.close();
    }
};

const closeOnEscape = (e) => {
    if (select.open && e.key === 'Escape') {
        select.close();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', closeOnEscape);
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-show="select.open"
            class="select-content-container absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-md"
        >
            <slot />
        </div>
    </Transition>
</template>
