<script setup>
import { computed } from 'vue';
import { CheckCheck, Check, Dumbbell, Mic } from 'lucide-vue-next';
import AudioMessagePlayer from '@/Components/molecules/AudioMessagePlayer.vue';

const props = defineProps({
    message: {
        type: Object,
        required: true,
    },
    viewerRole: {
        type: String,
        default: 'trainer',
        validator: (v) => ['trainer', 'client'].includes(v),
    },
});

const isRight = computed(() => {
    if (props.viewerRole === 'client') {
        return props.message.sender === 'client';
    }
    return props.message.sender === 'trainer';
});
</script>

<template>
    <div
        :class="[
            'flex',
            isRight ? 'justify-end' : 'justify-start'
        ]"
    >
        <div
            :class="[
                'max-w-md rounded-xl',
                isRight
                    ? 'bg-gray-800 text-white'
                    : 'bg-white border border-gray-200'
            ]"
        >
            <!-- Text Message -->
            <template v-if="message.type === 'text' && message.message">
                <div class="px-4 py-2.5">
                    <p class="text-sm">{{ message.message }}</p>
                    <div class="flex items-center justify-end gap-1 mt-1">
                        <span
                            :class="[
                                'text-xs',
                                isRight ? 'text-gray-300' : 'text-gray-500'
                            ]"
                        >
                            {{ message.timestamp }}
                        </span>
                        <div v-if="isRight" class="text-gray-300">
                            <CheckCheck v-if="message.read" class="size-3" />
                            <Check v-else class="size-3" />
                        </div>
                    </div>
                </div>
            </template>

            <!-- Workout Card -->
            <template v-else-if="message.type === 'workout' && message.content">
                <div class="p-3">
                    <div
                        :class="[
                            'rounded-lg p-3',
                            isRight ? 'bg-gray-700' : 'bg-gray-50'
                        ]"
                    >
                        <div class="flex items-center gap-2 mb-2">
                            <Dumbbell class="size-4" />
                            <span class="text-xs font-semibold">Workout Update</span>
                        </div>
                        <p class="text-sm font-semibold mb-1">{{ message.content.title }}</p>
                        <p class="text-sm opacity-90">{{ message.content.details }}</p>
                        <p v-if="message.content.note" class="text-xs opacity-75 mt-2">
                            {{ message.content.note }}
                        </p>
                    </div>
                    <div class="flex items-center justify-end gap-1 mt-2">
                        <span
                            :class="[
                                'text-xs',
                                isRight ? 'text-gray-300' : 'text-gray-500'
                            ]"
                        >
                            {{ message.timestamp }}
                        </span>
                        <div v-if="isRight" class="text-gray-300">
                            <CheckCheck v-if="message.read" class="size-3" />
                            <Check v-else class="size-3" />
                        </div>
                    </div>
                </div>
            </template>

            <!-- Audio Message -->
            <template v-else-if="message.type === 'audio' && message.content">
                <div class="p-3">
                    <div
                        :class="[
                            'rounded-lg p-3',
                            isRight ? 'bg-gray-700' : 'bg-gray-50'
                        ]"
                    >
                        <div class="flex items-center gap-2 mb-2">
                            <Mic class="size-4" />
                            <span class="text-xs font-semibold">Audio Message</span>
                        </div>
                        <AudioMessagePlayer
                            :duration="message.content.duration || '0:00'"
                            :is-trainer="isRight"
                        />
                    </div>
                    <div class="flex items-center justify-end gap-1 mt-2">
                        <span
                            :class="[
                                'text-xs',
                                isRight ? 'text-gray-300' : 'text-gray-500'
                            ]"
                        >
                            {{ message.timestamp }}
                        </span>
                        <div v-if="isRight" class="text-gray-300">
                            <CheckCheck v-if="message.read" class="size-3" />
                            <Check v-else class="size-3" />
                        </div>
                    </div>
                </div>
            </template>

            <!-- Fallback for other types -->
            <template v-else>
                <div class="px-4 py-2.5">
                    <p class="text-sm opacity-75">{{ message.message || 'Message' }}</p>
                    <div class="flex items-center justify-end gap-1 mt-1">
                        <span
                            :class="[
                                'text-xs',
                                isRight ? 'text-gray-300' : 'text-gray-500'
                            ]"
                        >
                            {{ message.timestamp }}
                        </span>
                        <div v-if="isRight" class="text-gray-300">
                            <CheckCheck v-if="message.read" class="size-3" />
                            <Check v-else class="size-3" />
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
