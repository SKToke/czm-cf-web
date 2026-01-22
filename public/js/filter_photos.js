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
        document.getElementById('filter-photos-form').submit();
    });

    // Handling selection of "All Categories"
    const allCategoriesOption = document.querySelector('#category_id option[value=""]');
    allCategoriesOption.addEventListener('click', function() {
        // Submit the form
        document.getElementById('filter-photos-form').submit();
    });
});
