<script setup>
import { watch, onMounted, onUnmounted } from 'vue';
import { X } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    maxWidth: {
        type: String,
        default: '2xl',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl'].includes(value),
    },
});

const emit = defineEmits(['close']);

const maxWidthClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
};

const closeOnEscape = (e) => {
    if (props.isOpen && e.key === 'Escape') {
        emit('close');
    }
};

watch(() => props.isOpen, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});

const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
        emit('close');
    }
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            @click="handleBackdropClick"
        >
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="isOpen"
                    :class="[
                        'bg-white rounded-xl shadow-xl w-full max-h-[90vh] overflow-y-auto',
                        maxWidthClasses[maxWidth]
                    ]"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <div>
                            <h2 class="text-xl font-semibold">{{ title }}</h2>
                            <p v-if="description" class="text-sm text-gray-500 mt-1">
                                {{ description }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="emit('close')"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <X class="size-5" />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <slot />
                    </div>

                    <!-- Footer (optional) -->
                    <div v-if="$slots.footer" class="p-6 border-t border-gray-200">
                        <slot name="footer" />
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
