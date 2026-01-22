import '@splidejs/splide/dist/js/splide.js';
import Splide from '@splidejs/splide';

if ($('.splide').length > 0) {
    const splide = new Splide('.splide', {
        type: 'loop',
        autoplay: true,
        perPage: 1,
        pauseOnHover: false,
        interval: 4000,
    });

    splide.mount();
}
