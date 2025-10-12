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


console.log('PUSHER_KEY:', import.meta.env.VITE_PUSHER_APP_KEY);
console.log('PUSHER_CLUSTER:', import.meta.env.VITE_PUSHER_APP_CLUSTER);
console.log(import.meta.env);



// import './echo';
