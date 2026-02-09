<script setup>
import { ref } from 'vue';
import { Smile, Paperclip, Send, Mic } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';
import AudioRecorder from '@/Components/molecules/AudioRecorder.vue';

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['send']);

const messageInput = ref('');
const isRecordingAudio = ref(false);

const handleSendMessage = () => {
    const text = messageInput.value?.trim();
    if (text && !props.disabled) {
        emit('send', { body: text, payload_type: 'text' });
        messageInput.value = '';
    }
};

const handleSendAudio = (duration) => {
    if (!props.disabled) {
        emit('send', {
            payload_type: 'audio',
            payload: { duration },
        });
    }
    isRecordingAudio.value = false;
};

const handleKeyDown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        handleSendMessage();
    }
};
</script>

<template>
    <div class="bg-white border-t border-gray-200 p-4">
        <AudioRecorder
            v-if="isRecordingAudio"
            @cancel="isRecordingAudio = false"
            @send="handleSendAudio"
        />
        <div v-else class="flex items-end gap-3">
            <Button variant="ghost" size="icon" class="shrink-0" :disabled="disabled">
                <Smile class="size-5 text-gray-500" />
            </Button>
            <Button variant="ghost" size="icon" class="shrink-0" :disabled="disabled">
                <Paperclip class="size-5 text-gray-500" />
            </Button>
            <div class="flex-1 relative">
                <textarea
                    v-model="messageInput"
                    placeholder="Type a message..."
                    class="w-full resize-none rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
                    rows="1"
                    :disabled="disabled"
                    style="min-height: 42px; max-height: 120px"
                    @keydown="handleKeyDown"
                />
            </div>
            <Button
                v-if="messageInput.trim()"
                class="shrink-0 bg-gray-900 hover:bg-gray-800"
                size="icon"
                :disabled="disabled"
                @click="handleSendMessage"
            >
                <Send class="size-4" />
            </Button>
            <Button
                v-else
                variant="ghost"
                size="icon"
                class="shrink-0"
                :disabled="disabled"
                @click="isRecordingAudio = true"
            >
                <Mic class="size-5 text-gray-500" />
            </Button>
        </div>
    </div>
</template>
