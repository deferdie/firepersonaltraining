<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { CalendarDays, Clock, Plus, Calendar } from 'lucide-vue-next';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import ScheduleFormModal from '@/Components/organisms/ScheduleFormModal.vue';
import { getFeatureColor } from '@/constants/featureColors';
import axios from 'axios';

const props = defineProps({
    clientId: {
        type: Number,
        required: true,
    },
    upcomingSessions: {
        type: Array,
        default: () => [],
    },
    calendarEvents: {
        type: Array,
        default: () => [],
    },
});

const calendarRef = ref(null);
const currentView = ref('dayGridMonth');
const calendarApi = ref(null);
function toFcEvent(e) {
    return {
        ...e,
        backgroundColor: e.color,
        extendedProps: {
            type: e.type,
            scheduleId: e.scheduleId,
            notes: e.notes,
        },
    };
}

const events = ref((props.calendarEvents || []).map(toFcEvent));
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const selectedEvent = ref(null);
const initialDateForCreate = ref(null);

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
    initialView: currentView.value,
    headerToolbar: false,
    events: events.value,
    eventClick: handleEventClick,
    datesSet: handleDatesSet,
    height: 'auto',
    slotMinTime: '06:00:00',
    slotMaxTime: '22:00:00',
}));

function handleEventClick(info) {
    selectedEvent.value = {
        ...info.event.toPlainObject(),
        scheduleId: info.event.extendedProps?.scheduleId ?? info.event.id?.split('_')[0],
    };
    isEditModalOpen.value = true;
}

function handleDatesSet(info) {
    calendarApi.value = info.view.calendar;
    fetchEvents(info.startStr, info.endStr);
}

watch(currentView, (view) => {
    calendarApi.value?.changeView(view);
});

async function fetchEvents(start, end) {
    try {
        const { data } = await axios.get(
            route('trainer.clients.schedules.index', { client: props.clientId }),
            { params: { start, end } }
        );
        events.value = (data || []).map(toFcEvent);
    } catch {
        events.value = [];
    }
}

function openCreateModal(dateStr) {
    initialDateForCreate.value = dateStr ? dateStr.slice(0, 16) : null;
    isCreateModalOpen.value = true;
}

function onSaved() {
    if (calendarApi.value) {
        const view = calendarApi.value.view;
        fetchEvents(view.activeStart.toISOString(), view.activeEnd.toISOString());
    }
    router.reload({ only: ['upcomingSessions', 'calendarEvents'] });
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

</script>

<template>
    <div class="space-y-6">
        <!-- Calendar -->
        <Card>
            <CardHeader>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <Calendar class="size-5 text-gray-900" />
                        <h2 class="text-lg font-semibold">Schedule</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex rounded-lg border border-gray-200 p-1 bg-gray-50">
                            <button
                                :class="[
                                    'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                                    currentView === 'dayGridMonth'
                                        ? 'bg-white shadow-sm text-gray-900'
                                        : 'text-gray-600 hover:text-gray-900',
                                ]"
                                @click="currentView = 'dayGridMonth'"
                            >
                                Month
                            </button>
                            <button
                                :class="[
                                    'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                                    currentView === 'timeGridWeek'
                                        ? 'bg-white shadow-sm text-gray-900'
                                        : 'text-gray-600 hover:text-gray-900',
                                ]"
                                @click="currentView = 'timeGridWeek'"
                            >
                                Week
                            </button>
                            <button
                                :class="[
                                    'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                                    currentView === 'timeGridDay'
                                        ? 'bg-white shadow-sm text-gray-900'
                                        : 'text-gray-600 hover:text-gray-900',
                                ]"
                                @click="currentView = 'timeGridDay'"
                            >
                                Day
                            </button>
                            <button
                                :class="[
                                    'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                                    currentView === 'listWeek'
                                        ? 'bg-white shadow-sm text-gray-900'
                                        : 'text-gray-600 hover:text-gray-900',
                                ]"
                                @click="currentView = 'listWeek'"
                            >
                                List
                            </button>
                        </div>
                        <Button
                            size="sm"
                            @click="openCreateModal()"
                        >
                            <Plus class="size-4 mr-1" />
                            Schedule
                        </Button>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <FullCalendar
                    ref="calendarRef"
                    :options="calendarOptions"
                />
            </CardContent>
        </Card>

        <!-- Upcoming Sessions -->
        <Card>
            <CardHeader>
                <div class="flex items-center gap-2">
                    <CalendarDays class="size-5 text-gray-900" />
                    <h2 class="text-lg font-semibold">Upcoming Sessions</h2>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="upcomingSessions.length > 0" class="space-y-3">
                    <div
                        v-for="(session, index) in upcomingSessions"
                        :key="session.id ?? index"
                        class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:shadow-sm transition-shadow"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="size-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: getFeatureColor('habit').hex + '20' }"
                            >
                                <Clock
                                    class="size-5"
                                    :style="{ color: getFeatureColor('habit').hex }"
                                />
                            </div>
                            <div>
                                <p class="font-semibold">{{ session.type }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(session.date) }} at {{ session.time }}
                                </p>
                            </div>
                        </div>
                        <Badge
                            :variant="session.status === 'confirmed' ? 'default' : 'secondary'"
                            :class="[
                                session.status === 'confirmed'
                                    ? 'bg-gray-800 text-white'
                                    : 'bg-gray-100 text-gray-600',
                            ]"
                        >
                            {{ session.status }}
                        </Badge>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <CalendarDays class="size-12 mx-auto mb-3 opacity-50" />
                    <p>No upcoming sessions scheduled</p>
                </div>
            </CardContent>
        </Card>

        <ScheduleFormModal
            :is-open="isCreateModalOpen"
            :client-id="clientId"
            :initial-date="initialDateForCreate"
            @close="isCreateModalOpen = false"
            @saved="onSaved"
        />
        <ScheduleFormModal
            :is-open="isEditModalOpen"
            :client-id="clientId"
            :editing-event="selectedEvent"
            @close="isEditModalOpen = false; selectedEvent = null"
            @saved="onSaved"
        />
    </div>
</template>
