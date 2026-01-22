document.addEventListener('DOMContentLoaded', (event) => {
    const disableButtons = () => {
        const nextButton = document.querySelector('a[aria-label="Next »"]');
        const prevButton = document.querySelector('a[aria-label="« Previous"]');
        if (nextButton) {
            nextButton.classList.add('disabled-button');
        }
        if (prevButton) {
            prevButton.classList.add('disabled-button');
        }
    };

    disableButtons();

    const observer = new MutationObserver(disableButtons);

    const config = { childList: true, subtree: true };

    observer.observe(document.body, config);
});
