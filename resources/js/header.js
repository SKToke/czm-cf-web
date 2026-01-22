jQuery(document).ready(function($) {
    $(window).on('scroll', function() {
        stickyHeader();
        scrollToTop();
    });

    // Function to handle sticky header
    function stickyHeader() {
        if ($('.stricky')) {
            var strickyScrollPos = $('.stricky').next().offset().top;
            if ($(window).scrollTop() > strickyScrollPos) {
                $('.stricky').removeClass('fadeIn animated');
                $('.stricky').addClass('stricky-fixed fadeInDown animated');
            } else if ($(window).scrollTop() <= strickyScrollPos) {
                $('.stricky').removeClass('stricky-fixed fadeInDown animated');
                $('.stricky').addClass('slideIn animated');
            }
        }
    }

    // Function to handle scrolling to top
    function scrollToTop() {
        if ($('.page-wrapper')) {
            var topHeader = $('.top-bar').innerHeight();
            var windowpos = $(window).scrollTop();
            if (windowpos >= topHeader) {
                $('.page-wrapper').addClass('fixed-header');
                $('.scroll-to-top').fadeIn(300);
            } else {
                $('.page-wrapper').removeClass('fixed-header');
                $('.scroll-to-top').fadeOut(300);
            }
        }
    }

    // Scroll to top
    if ($('.scroll-to-top')) {
        $(".scroll-to-top").on('click', function() {
            // animate
            $('html, body').animate({
                scrollTop: 0 // Scroll to the top of the page
            }, 1000);
        });
    }
});
