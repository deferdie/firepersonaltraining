/**
 * Brand colors for feature types.
 * Use these consistently across the application for scheduling, badges, and UI.
 */
export const FEATURE_COLORS = {
    habit: {
        bg: 'bg-emerald-500',
        bgLight: 'bg-emerald-50',
        text: 'text-emerald-700',
        border: 'border-emerald-500',
        hex: '#10b981',
    },
    program: {
        bg: 'bg-rose-500',
        bgLight: 'bg-rose-50',
        text: 'text-rose-700',
        border: 'border-rose-500',
        hex: '#f43f5e',
    },
    assessment: {
        bg: 'bg-amber-500',
        bgLight: 'bg-amber-50',
        text: 'text-amber-700',
        border: 'border-amber-500',
        hex: '#f59e0b',
    },
    content: {
        bg: 'bg-sky-500',
        bgLight: 'bg-sky-50',
        text: 'text-sky-700',
        border: 'border-sky-500',
        hex: '#0ea5e9',
    },
    goal: {
        bg: 'bg-violet-500',
        bgLight: 'bg-violet-50',
        text: 'text-violet-700',
        border: 'border-violet-500',
        hex: '#8b5cf6',
    },
    nutrition: {
        bg: 'bg-teal-500',
        bgLight: 'bg-teal-50',
        text: 'text-teal-700',
        border: 'border-teal-500',
        hex: '#14b8a6',
    },
};

/**
 * Get the color config for a feature type (e.g. 'habit', 'program').
 * Falls back to gray if type is unknown.
 */
export function getFeatureColor(type) {
    return FEATURE_COLORS[type] ?? {
        bg: 'bg-gray-500',
        bgLight: 'bg-gray-50',
        text: 'text-gray-700',
        border: 'border-gray-500',
        hex: '#6b7280',
    };
}

/**
 * Get hex color for calendar event styling (FullCalendar etc.)
 */
export function getFeatureHex(type) {
    return getFeatureColor(type).hex;
}
