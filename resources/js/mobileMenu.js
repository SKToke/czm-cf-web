import './library/jquery-1.11.1.min.js';
import 'owl.carousel';

jQuery(document).ready(function($) {
    if ($('.navigation .nav-footer button')) {
        $('.navigation .nav-footer button').on('click', function () {
            $('.navigation .nav-header').slideToggle();
            $('.navigation .nav-header').find('.dropdown').children('a').append(function () {
                return '<button><i class="fa fa-bars"></i></button>';
            });
            $('.navigation .nav-header .dropdown a button').on('click', function () {
                $(this).parent().parent().children('ul.submenu').slideToggle();
                return false;
            });
        });
    };
});
