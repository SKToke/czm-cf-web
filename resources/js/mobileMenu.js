import $ from 'jquery';

function toggleNavHeader(navHeader) {
    if (!navHeader) return;
    const $header = $(navHeader);
    if ($header.is(':visible')) {
        $header.stop(true, true).slideUp(250);
    } else {
        $header.stop(true, true).slideDown(250);
    }
}

// 1. Delegated click handler on document
$(document).on('click', '.navigation .nav-footer button, .navigation .nav-footer', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $navHeader = $(this).closest('.navigation').find('.nav-header');
    if ($navHeader.length) {
        toggleNavHeader($navHeader[0]);
    }
});

// 2. Submenu toggle on mobile
$(document).on('click', '.navigation .nav-header .dropdown > a', function (e) {
    if (window.innerWidth <= 1024) {
        e.preventDefault();
        e.stopPropagation();
        const $submenu = $(this).siblings('.submenu');
        if ($submenu.is(':visible')) {
            $submenu.stop(true, true).slideUp(200);
        } else {
            $(this).parent().siblings('.dropdown').find('.submenu').slideUp(200);
            $submenu.stop(true, true).slideDown(200);
        }
    }
});

// 3. Close when clicking outside
$(document).on('click', function (e) {
    if (window.innerWidth <= 1024) {
        if (!$(e.target).closest('.navigation').length) {
            $('.navigation .nav-header').slideUp(200);
        }
    }
});

// 4. Native DOM listener as immediate fallback
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.navigation .nav-footer button');
    buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const nav = this.closest('.navigation');
            if (nav) {
                const navHeader = nav.querySelector('.nav-header');
                if (navHeader && !$(navHeader).is(':animated')) {
                    toggleNavHeader(navHeader);
                }
            }
        });
    });
});


