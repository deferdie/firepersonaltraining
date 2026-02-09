<script setup>
import { ref, onUnmounted } from 'vue';
import { X, Send } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

const emit = defineEmits(['cancel', 'send']);

const recordingDuration = ref(0);
const isRecording = ref(false);
const permissionError = ref(false);
const mediaRecorderRef = ref(null);
const chunksRef = ref([]);
let streamRef = null;
let timerId = null;

const startRecording = async () => {
    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            permissionError.value = true;
            return;
        }
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        streamRef = stream;
        const mediaRecorder = new MediaRecorder(stream);
        mediaRecorderRef.value = mediaRecorder;
        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) chunksRef.value.push(event.data);
        };
        mediaRecorder.onstop = () => {
            stream.getTracks().forEach((track) => track.stop());
        };
        mediaRecorder.start();
        isRecording.value = true;
        permissionError.value = false;
    } catch {
        permissionError.value = true;
        isRecording.value = false;
    }
};

startRecording();

timerId = setInterval(() => {
    if (isRecording.value) recordingDuration.value += 1;
}, 1000);

onUnmounted(() => {
    if (timerId) clearInterval(timerId);
    if (mediaRecorderRef.value?.state === 'recording') {
        mediaRecorderRef.value.stop();
    }
    streamRef?.getTracks().forEach((track) => track.stop());
});

const handleSend = () => {
    if (mediaRecorderRef.value?.state === 'recording') {
        mediaRecorderRef.value.stop();
    }
    const minutes = Math.floor(recordingDuration.value / 60);
    const seconds = recordingDuration.value % 60;
    const duration = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    emit('send', duration);
};

const handleCancel = () => {
    if (mediaRecorderRef.value?.state === 'recording') {
        mediaRecorderRef.value.stop();
        streamRef?.getTracks().forEach((track) => track.stop());
    }
    emit('cancel');
};

const formatDuration = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};
</script>

<template>
    <div
        v-if="permissionError"
        class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3"
    >
        <Button
            variant="ghost"
            size="icon"
            class="shrink-0 text-red-600 hover:bg-red-100"
            @click="handleCancel"
        >
            <X class="size-5" />
        </Button>
        <div class="flex-1">
            <p class="text-sm font-medium text-red-900">Microphone access denied</p>
            <p class="text-xs text-red-700 mt-0.5">
                Please allow microphone access in your browser settings to record audio messages.
            </p>
        </div>
    </div>

    <div
        v-else
        class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3"
    >
        <Button
            variant="ghost"
            size="icon"
            class="shrink-0 text-red-600 hover:bg-red-100"
            @click="handleCancel"
        >
            <X class="size-5" />
        </Button>
        <div class="flex-1 flex items-center gap-3">
            <div class="size-3 bg-red-500 rounded-full animate-pulse" />
            <span class="text-sm font-medium">Recording...</span>
            <span class="text-sm font-mono text-gray-600">{{ formatDuration(recordingDuration) }}</span>
        </div>
        <div class="flex items-center gap-0.5 h-6">
            <div
                v-for="i in 20"
                :key="i"
                class="w-0.5 bg-red-500 rounded-full animate-pulse"
                :style="{
                    height: `${30 + Math.random() * 70}%`,
                    animationDelay: `${Math.random() * 0.5}s`,
                }"
            />
        </div>
        <Button
            class="shrink-0 bg-gray-900 hover:bg-gray-800"
            size="icon"
            :disabled="recordingDuration === 0"
            @click="handleSend"
        >
            <Send class="size-4" />
        </Button>
    </div>
</template>
