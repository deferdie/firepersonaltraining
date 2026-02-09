/**
 * Laravel Echo configuration for real-time messaging.
 * Set VITE_PUSHER_APP_KEY (or VITE_REVERB_APP_KEY) and related env vars to enable.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;
    const key = import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY;
    if (key) {
        const useReverb = !!import.meta.env.VITE_REVERB_APP_KEY;
        const wsHost = import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST || (useReverb ? 'localhost' : undefined);
        const wsPort = import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || (useReverb ? '6001' : 80);
        const scheme = import.meta.env.VITE_REVERB_SCHEME || import.meta.env.VITE_PUSHER_SCHEME;
        const forceTLS = useReverb ? scheme === 'https' : (scheme || 'https') === 'https';

        window.Echo = new Echo({
            broadcaster: useReverb ? 'reverb' : 'pusher',
            key,
            wsHost,
            wsPort: Number(wsPort) || (useReverb ? 6001 : 80),
            wssPort: Number(wsPort) || (useReverb ? 6001 : 443),
            forceTLS,
            enabledTransports: ['ws', 'wss'],
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-XSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        document.cookie.replace(/(?:(?:^|.*;\s*)XSRF-TOKEN\s*=\s*([^;]*).*$)|^.*$/, '$1'),
                },
            },
        });
    }
}
