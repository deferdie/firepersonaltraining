<script setup>
import { computed } from 'vue';
import { Plus, Users, MessageSquare, UserMinus } from 'lucide-vue-next';
import Avatar from '@/Components/atoms/Avatar.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardTitle from '@/Components/molecules/CardTitle.vue';
import Progress from '@/Components/atoms/Progress.vue';

const props = defineProps({
    members: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['add-members', 'member-click', 'remove-member']);

const activeMembers = computed(() =>
    (props.members || []).filter((m) => m.status === 'active')
);
const inactiveMembers = computed(() =>
    (props.members || []).filter((m) => m.status !== 'active')
);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Group Members</h2>
            <Button @click="emit('add-members')">
                <Plus class="size-4 mr-2" />
                Add Members
            </Button>
        </div>

        <!-- Active Members -->
        <Card>
            <CardHeader>
                <CardTitle>Active Members ({{ activeMembers.length }})</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="activeMembers.length > 0" class="grid gap-4">
                    <div
                        v-for="member in activeMembers"
                        :key="member.id"
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                        @click="emit('member-click', member)"
                    >
                        <div class="flex items-center gap-3">
                            <Avatar
                                :initials="member.initials"
                                :color="member.color"
                                size="md"
                            />
                            <div>
                                <p class="font-medium">{{ member.name }}</p>
                                <p class="text-sm text-gray-500">Adherence: {{ member.adherence }}%</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-32">
                                <Progress :value="member.adherence" />
                            </div>
                            <Badge variant="outline" class="text-green-600 border-green-600">
                                Active
                            </Badge>
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-red-600 hover:text-red-700 hover:border-red-600 shrink-0"
                                @click="emit('remove-member', member, $event)"
                            >
                                <UserMinus class="size-4 mr-1" />
                                Remove
                            </Button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    <Users class="size-12 mx-auto mb-3 opacity-50" />
                    <p>No active members</p>
                </div>
            </CardContent>
        </Card>

        <!-- Needs Attention -->
        <Card v-if="inactiveMembers.length > 0">
            <CardHeader>
                <CardTitle>Needs Attention ({{ inactiveMembers.length }})</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid gap-4">
                    <div
                        v-for="member in inactiveMembers"
                        :key="member.id"
                        class="flex items-center justify-between p-4 bg-orange-50 rounded-lg"
                    >
                        <div class="flex items-center gap-3">
                            <Avatar
                                :initials="member.initials"
                                :color="member.color"
                                size="md"
                            />
                            <div>
                                <p class="font-medium">{{ member.name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ member.status === 'paused' ? 'Paused' : member.status }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click.stop="emit('member-click', member)"
                            >
                                <MessageSquare class="size-4 mr-2" />
                                Message
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-red-600 hover:text-red-700 hover:border-red-600"
                                @click="emit('remove-member', member, $event)"
                            >
                                <UserMinus class="size-4 mr-1" />
                                Remove
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
