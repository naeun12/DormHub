// resources/js/bootstrap.js

// resources/js/bootstrap.js

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;


window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});


window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('Pusher state changed:', states);
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Pusher connection error:', err);
});




// import './echo';
