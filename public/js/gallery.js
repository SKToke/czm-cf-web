document.addEventListener('DOMContentLoaded', function() {
    let currentIndex = -1; // To track the current image index
    let images = []; // To store the image data

    // Function to bind click events to image containers
    function bindGalleryEvents() {
        images = Array.from(document.querySelectorAll('.image-container')); // Collect all images
        images.forEach((container, index) => {
            container.addEventListener('click', function(event) {
                currentIndex = index;
                displayImage(currentIndex);
            });
        });

        // Bind click event for closing modal
        const imageViewModal = document.querySelector('#imageViewModal');
        if (imageViewModal) {
            imageViewModal.addEventListener('click', function() {
                closeImage();
            });
        }

        // Bind click event for navigation buttons
        const prevButton = document.querySelector('#prevImage');
        const nextButton = document.querySelector('#nextImage');
        if (prevButton && nextButton) {
            prevButton.addEventListener('click', showPrevImage);
            nextButton.addEventListener('click', showNextImage);
        }
    }

    // Function to display the image in a modal
    function displayImage(index) {
        if (index >= 0 && index < images.length) {
            const container = images[index];
            const imagePath = container.dataset.imagePath;
            const caseTitle = container.dataset.caseTitle;
            const modalImage = document.querySelector('#modalImage');
            const title = document.querySelector('#caseTitle');
            const imageViewModal = document.querySelector('#imageViewModal');

            if (modalImage && title && imageViewModal) {
                modalImage.src = imagePath;
                title.textContent = caseTitle;
                imageViewModal.style.display = 'block';
            }
        }
    }

    // Function to show the previous image
    function showPrevImage(event) {
        event.stopPropagation(); // Prevent modal close
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        displayImage(currentIndex);
    }

    // Function to show the next image
    function showNextImage(event) {
        event.stopPropagation(); // Prevent modal close
        currentIndex = (currentIndex + 1) % images.length;
        displayImage(currentIndex);
    }

    // Function to close the image modal
    function closeImage() {
        const imageViewModal = document.querySelector('#imageViewModal');
        if (imageViewModal) {
            imageViewModal.style.display = 'none';
        }
    }

    // Initial binding of events
    bindGalleryEvents();

    // Exposing the bindGalleryEvents function to the global scope
    window.bindGalleryEvents = bindGalleryEvents;
});
