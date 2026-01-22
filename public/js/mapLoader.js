document.addEventListener('DOMContentLoaded', function() {
    var czmLocation = { lat: 23.7630653, lng: 90.4016932 };
    var mapElement = document.getElementById('map');
    const scrollToTopButton = document.getElementById('scrollToTop');

    if (mapElement) { // Check if the map element exists on the page
        var map = new google.maps.Map(mapElement, {
            zoom: 17,
            center: czmLocation
        });

        var marker = new google.maps.Marker({
            position: czmLocation,
            map: map
        });
    }

    window.onscroll = function() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollToTopButton.style.display = "block";
        } else {
            scrollToTopButton.style.display = "none";
        }
    };

    scrollToTopButton.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
});
