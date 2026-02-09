<script setup>
import { CheckCircle2, XCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

const props = defineProps({
    type: {
        type: String,
        default: 'success',
        validator: (v) => ['success', 'error', 'warning', 'info', 'status'].includes(v),
    },
    message: {
        type: String,
        required: true,
    },
    dismissible: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['dismiss']);

const config = {
    success: {
        icon: CheckCircle2,
        classes: 'bg-green-50 border-green-200 text-green-800',
        iconClasses: 'text-green-600',
    },
    error: {
        icon: XCircle,
        classes: 'bg-red-50 border-red-200 text-red-800',
        iconClasses: 'text-red-600',
    },
    warning: {
        icon: AlertTriangle,
        classes: 'bg-amber-50 border-amber-200 text-amber-800',
        iconClasses: 'text-amber-600',
    },
    info: {
        icon: Info,
        classes: 'bg-blue-50 border-blue-200 text-blue-800',
        iconClasses: 'text-blue-600',
    },
    status: {
        icon: Info,
        classes: 'bg-gray-50 border-gray-200 text-gray-800',
        iconClasses: 'text-gray-600',
    },
};

const cfg = config[props.type] ?? config.status;
const IconComponent = cfg.icon;
</script>

<template>
    <div
        :class="[
            'flex items-center gap-3 rounded-lg border px-4 py-3 text-sm',
            cfg.classes,
        ]"
    >
        <IconComponent :class="['size-5 shrink-0', cfg.iconClasses]" />
        <p class="flex-1 font-medium">{{ message }}</p>
        <button
            v-if="dismissible"
            type="button"
            class="shrink-0 rounded p-1 opacity-70 hover:opacity-100 transition-opacity"
            aria-label="Dismiss"
            @click="emit('dismiss')"
        >
            <X class="size-4" />
        </button>
    </div>
</template>
