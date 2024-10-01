import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: Nova.config('env').appkey,
    wsHost: Nova.config('env').host,
    wsPort: Nova.config('env').port ?? 80,
    wssPort: Nova.config('env').port ?? 443,
    forceTLS: (Nova.config('env').scheme ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
