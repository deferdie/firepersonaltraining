<script setup>
import { MessageSquare } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardTitle from '@/Components/molecules/CardTitle.vue';
import Avatar from '@/Components/atoms/Avatar.vue';

const props = defineProps({
    messages: {
        type: Array,
        default: () => [],
    },
    clientName: {
        type: String,
        required: true,
    },
    clientInitials: {
        type: String,
        required: true,
    },
    clientColor: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="text-base">Messages</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <div v-if="messages.length > 0" class="space-y-3 max-h-64 overflow-y-auto">
                <div
                    v-for="message in messages.slice(0, 5)"
                    :key="message.id"
                    class="flex gap-2"
                >
                    <Avatar
                        v-if="message.sender === 'client'"
                        :initials="clientInitials"
                        :color="clientColor"
                        size="sm"
                    />
                    <div
                        v-else
                        class="size-6 rounded-full bg-gray-800 flex items-center justify-center text-white text-xs font-medium"
                    >
                        T
                    </div>
                    <div class="flex-1 min-w-0">
                        <div
                            :class="[
                                'rounded-lg px-3 py-2 text-sm',
                                message.sender === 'client'
                                    ? 'bg-gray-100 text-gray-900'
                                    : 'bg-gray-800 text-white',
                            ]"
                        >
                            <p class="break-words">{{ message.message }}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ message.timestamp }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                <MessageSquare class="size-8 mx-auto mb-2 opacity-50" />
                <p class="text-sm">No messages yet</p>
            </div>
        </CardContent>
    </Card>
</template>
