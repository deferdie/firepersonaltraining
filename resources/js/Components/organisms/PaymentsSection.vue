<script setup>
import { DollarSign, CreditCard, Plus } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Button from '@/Components/atoms/Button.vue';
import Badge from '@/Components/atoms/Badge.vue';

const props = defineProps({
    payments: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Payments Overview -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <CreditCard class="size-5 text-gray-900" />
                        <h2 class="text-lg font-semibold">Payments Overview</h2>
                    </div>
                    <Button variant="ghost" size="sm">
                        <Plus class="size-4 mr-1" />
                        Add
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="payments.length > 0" class="space-y-5">
                    <div
                        v-for="(payment, index) in payments"
                        :key="index"
                        class="space-y-3 p-4 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition-shadow"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="font-semibold mb-1">{{ payment.description }}</p>
                                <p class="text-sm text-gray-500 mb-2">{{ formatDate(payment.date) }}</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <DollarSign class="size-3" />
                                        Amount: ${{ payment.amount?.toFixed(2) || '0.00' }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <CreditCard
                                            :class="[
                                                'size-3',
                                                payment.status === 'paid' ? 'text-gray-700' : 'text-gray-400',
                                            ]"
                                        />
                                        {{ payment.status === 'paid' ? 'Paid' : 'Pending' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <Badge
                                    :variant="payment.status === 'paid' ? 'default' : 'secondary'"
                                    :class="[
                                        payment.status === 'paid'
                                            ? 'bg-gray-800 text-white'
                                            : 'bg-gray-100 text-gray-600',
                                    ]"
                                >
                                    {{ payment.status }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <CreditCard class="size-12 mx-auto mb-3 opacity-50" />
                    <p>No payment history</p>
                    <Button size="sm" class="mt-4">
                        <Plus class="size-4 mr-1" />
                        Add Payment
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
