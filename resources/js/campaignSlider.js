import 'owl.carousel';

if ($('.team-carousel').length) {
    $('.team-carousel').owlCarousel({
        loop: false,
        margin: 30,
        nav: true,
        dots: false,
        navText: [
            '<i class="fa fa-angle-left"></i>',
            '<i class="fa fa-angle-right"></i>'
        ],
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive: {
            0:{
                items:1
            },
            480:{
                items:1
            },
            600:{
                items:2
            },
            1000:{
                items:3
            },
            1200:{
                items:3
            }
        }
    });
}
