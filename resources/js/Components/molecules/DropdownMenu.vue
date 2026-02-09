<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'end',
        validator: (value) => ['start', 'end'].includes(value),
    },
});

const open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

const handleClickOutside = (event) => {
    if (open.value && !event.target.closest('.dropdown-menu-container')) {
        open.value = false;
    }
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.removeEventListener('click', handleClickOutside);
});

const alignmentClasses = {
    start: 'left-0',
    end: 'right-0',
};
</script>

<template>
    <div class="relative dropdown-menu-container">
        <div @click.stop="open = !open">
            <slot name="trigger" />
        </div>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                :class="[
                    'absolute z-50 mt-2 min-w-[8rem] overflow-hidden rounded-md border border-gray-200 bg-white shadow-md',
                    alignmentClasses[align]
                ]"
            >
                <slot name="content" />
            </div>
        </Transition>
    </div>
</template>
