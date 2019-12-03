
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    //window.$ = window.jQuery = require('jquery');

    //require('bootstrap-sass');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let showingErrorMessage = false;

window.axios.interceptors.response.use(
    (response) => {
        setTimeout(() => {
            $('.tooltips').tooltip();
            setTimeout(() => {
                $('.tooltips').tooltip();
            }, 4000);
        }, 1000);
        return response;
    },
    function(error){
        if ( !showingErrorMessage && (error.response.data == "Unauthorized" || error.response.status == 401)) {
            gerror('Acceso no autorizado o sesión caducada');
            showingErrorMessage = true;
            setTimeout(function(){
                location.reload();
            },1000);
        }
    },
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
