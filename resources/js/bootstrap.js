import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import $ from 'jquery';

window.$ = window.jQuery = $;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    encrypted: true,
});

window.Echo.channel('events')
    .listen('RealTimeMessage', (e) => {
        const notice = $('.notification-container')
        const red_badge = $('#red-badge-for-notification')
        let userId = notice.data('user');
        let noticeCount = notice.data('notification-count');
        if (userId != null && userId !== "" && e.message.includes(String(userId))) {
             noticeCount = parseInt(noticeCount) + 1;
             notice.data('notification-count', noticeCount);
             notice.find('.notification-badge').text(noticeCount);
             if (noticeCount > 0) {
                 red_badge.css('display', 'block');
             }
        }
    });
