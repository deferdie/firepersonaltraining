<script setup>
import { ref } from 'vue';
import { Plus, Dumbbell, FileText, CheckCircle2, Clipboard } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import Progress from '@/Components/atoms/Progress.vue';
import AssignContentModal from '@/Components/organisms/AssignContentModal.vue';
import ContentDetailsModal from '@/Components/organisms/ContentDetailsModal.vue';

const props = defineProps({
    assignedContent: {
        type: Array,
        default: () => [],
    },
    assignableType: {
        type: String,
        default: 'group',
        validator: (v) => ['client', 'group'].includes(v),
    },
    assignableId: {
        type: Number,
        default: null,
    },
});

const isAssignModalOpen = ref(false);
const selectedItem = ref(null);

function openItemDetail(item) {
    selectedItem.value = item;
}

function closeItemDetail() {
    selectedItem.value = null;
}

function normalizedItem(item) {
    if (!item) return null;
    return {
        ...item,
        progress: item.completionRate ?? item.progress,
    };
}
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Assigned Content</h2>
            <Button
                v-if="props.assignableId != null"
                @click="isAssignModalOpen = true"
            >
                <Plus class="size-4 mr-2" />
                Assign Content
            </Button>
        </div>

        <Card v-if="assignedContent && assignedContent.length > 0">
            <CardContent class="pt-6">
                <div
                    v-for="(item, index) in assignedContent"
                    :key="item.id ?? index"
                    class="flex items-center justify-between p-4 rounded-lg border border-gray-200 mb-4 last:mb-0 hover:border-gray-300 hover:shadow-sm transition-all cursor-pointer"
                    @click="openItemDetail(item)"
                >
                    <div class="flex items-center gap-3">
                        <div
                            :class="[
                                'size-10 rounded-lg flex items-center justify-center',
                                item.type === 'program' ? 'bg-blue-100 text-blue-600' :
                                item.type === 'assessment' ? 'bg-green-100 text-green-600' :
                                'bg-orange-100 text-orange-600',
                            ]"
                        >
                            <Dumbbell v-if="item.type === 'program'" class="size-5" />
                            <CheckCircle2 v-else-if="item.type === 'assessment'" class="size-5" />
                            <FileText v-else class="size-5" />
                        </div>
                        <div>
                            <p class="font-semibold">{{ item.name }}</p>
                            <p class="text-sm text-gray-500">Assigned to {{ item.assignedTo }} members</p>
                        </div>
                    </div>
                    <Badge variant="outline">{{ item.type }}</Badge>
                    <div class="w-24">
                        <Progress :value="item.completionRate ?? 0" />
                    </div>
                </div>
            </CardContent>
        </Card>
        <Card v-else>
            <CardContent class="py-12 text-center">
                <Clipboard class="size-12 mx-auto mb-3 text-gray-400" />
                <p class="font-semibold text-gray-900 mb-1">No assigned content yet</p>
                <p class="text-sm text-gray-500 mb-4">Assign programs, documents, or videos to this group</p>
                <Button
                v-if="props.assignableId != null"
                @click="isAssignModalOpen = true"
            >
                <Plus class="size-4 mr-2" />
                Assign Content
            </Button>
            </CardContent>
        </Card>

        <AssignContentModal
            v-if="props.assignableId != null"
            :is-open="isAssignModalOpen"
            :assignable-type="props.assignableType"
            :assignable-id="props.assignableId"
            @close="isAssignModalOpen = false"
            @assigned="isAssignModalOpen = false"
        />

        <ContentDetailsModal
            :is-open="selectedItem != null"
            :item="selectedItem ? normalizedItem(selectedItem) : null"
            :client-id="null"
            @close="closeItemDetail"
        />
    </div>
</template>
