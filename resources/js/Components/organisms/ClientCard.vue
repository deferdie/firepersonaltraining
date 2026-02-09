<script setup>
import { Calendar, Mail, MoreVertical, Phone } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import Avatar from '@/Components/atoms/Avatar.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import DropdownMenu from '@/Components/molecules/DropdownMenu.vue';
import DropdownMenuItem from '@/Components/molecules/DropdownMenuItem.vue';

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
    clickable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['click', 'sendMessage', 'scheduleSession']);

const getStatusBadge = (status) => {
    switch (status) {
        case 'active':
            return 'active';
        case 'trial':
            return 'trial';
        case 'inactive':
            return 'inactive';
        default:
            return 'default';
    }
};

const formatJoinDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};

const handleCardClick = () => {
    if (props.clickable) {
        emit('click', props.client);
    }
};

const handleSendMessage = () => {
    emit('sendMessage', props.client);
};

const handleScheduleSession = () => {
    emit('scheduleSession', props.client);
};
</script>

<template>
    <Card
        :class="[
            'hover:shadow-md transition-all hover:border-gray-900',
            clickable && 'cursor-pointer'
        ]"
        @click="handleCardClick"
    >
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <Avatar
                        :initials="client.initials"
                        :color="client.color"
                        size="lg"
                    />
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold truncate">{{ client.name }}</h3>
                        <p class="text-sm text-gray-500 truncate">
                            {{ client.program || 'No program assigned' }}
                        </p>
                    </div>
                </div>
                <DropdownMenu @click.stop>
                    <template #trigger>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8 shrink-0"
                            @click.stop.prevent
                        >
                            <MoreVertical class="size-4" />
                        </Button>
                    </template>
                    <template #content>
                        <DropdownMenuItem @click.stop="handleSendMessage">
                            <Mail class="size-4 mr-2" />
                            Send Message
                        </DropdownMenuItem>
                        <DropdownMenuItem @click.stop="handleScheduleSession">
                            <Calendar class="size-4 mr-2" />
                            Schedule Session
                        </DropdownMenuItem>
                    </template>
                </DropdownMenu>
            </div>
        </CardHeader>
        <CardContent class="space-y-3">
            <div class="flex items-center justify-between">
                <Badge :variant="getStatusBadge(client.status)">
                    {{ client.status.charAt(0).toUpperCase() + client.status.slice(1) }}
                </Badge>
                <span class="text-xs text-gray-500">
                    Joined {{ formatJoinDate(client.joinDate) }}
                </span>
            </div>
            <div class="space-y-1 text-sm">
                <div class="flex items-center gap-2 text-gray-500">
                    <Mail class="size-3" />
                    <span class="truncate text-xs">{{ client.email }}</span>
                </div>
                <div v-if="client.phone" class="flex items-center gap-2 text-gray-500">
                    <Phone class="size-3" />
                    <span class="text-xs">{{ client.phone }}</span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
