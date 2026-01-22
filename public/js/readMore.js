document.addEventListener('DOMContentLoaded', function() {
    const description = document.querySelector('.read-more');

    if (!description) return;

    const originalText = description.innerHTML;
    const truncatedText = originalText.substring(0, 1000) + '...';

    let isExpanded = false;

    if (originalText.length > 1000) {
        description.innerHTML = `${truncatedText} <a href="#" id="toggleDescription">Read More</a>`;
    }

    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'toggleDescription') {
            event.preventDefault();
            if (isExpanded) {
                description.innerHTML = `${truncatedText} <a href="#" id="toggleDescription">Read More</a>`;
            } else {
                description.innerHTML = `${originalText} <a href="#" id="toggleDescription">Read Less</a>`;
            }
            isExpanded = !isExpanded;
        }
    });
});
