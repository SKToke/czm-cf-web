// // filter_photos.js
// document.addEventListener('DOMContentLoaded', function() {
//     const categorySelect = document.getElementById('category_id');
//     const contentTypeInput = document.getElementById('content_type'); // Access the hidden input
//     const contentListContainer = document.getElementById('content_list');
//
//     categorySelect.addEventListener('change', function() {
//         const categoryId = this.value;
//         const contentType = contentTypeInput.value; // Retrieve the content type value
//
//         // Append both categoryId and contentType to the URL
//         const url = `/czm-filtered-contents?category_id=${categoryId}&content_type=${contentType}`;
//
//         fetch(url)
//             .then(response => response.text())
//             .then(html => {
//                 contentListContainer.innerHTML = html;
//                 if (window.bindGalleryEvents) {
//                     window.bindGalleryEvents();
//                 }
//             })
//             .catch(error => console.error('Error:', error));
//     });
// });
//
//


document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');

    // Set the selected category based on the category ID from the URL
    if (categoryId !== null) {
        categorySelect.value = categoryId;
    }

    // Event listener for change in category select
    categorySelect.addEventListener('change', function() {
        // Submit the form
        document.getElementById('filter-contents-form').submit();
    });

    // Handling selection of "All Categories"
    const allCategoriesOption = document.querySelector('#category_id option[value=""]');
    allCategoriesOption.addEventListener('click', function() {
        // Submit the form
        document.getElementById('filter-contents-form').submit();
    });
});
