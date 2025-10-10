import axios from 'axios';
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // ✅ Dapat ibutang before Echo init
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '22553162724d965c544b', // hardcoded
    cluster: 'us2',
    forceTLS: true
});
console.log(import.meta.env.VITE_PUSHER_APP_KEY);
console.log(import.meta.env.VITE_PUSHER_APP_CLUSTER);
console.log(import.meta.env.VITE_PUSHER_APP_SECRET);


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
