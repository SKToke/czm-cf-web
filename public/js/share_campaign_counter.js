document.addEventListener('DOMContentLoaded', function() {
    const socialButtons = document.querySelectorAll('.social-button');
    const campaignSlug = document.querySelector('.case-social-share').getAttribute('data-campaign-slug');
    socialButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            handleButtonClick(event);
        });
    });

    async function handleButtonClick(event) {
        await updateShareCount();
    }

    async function updateShareCount() {
        try {
            const response = await fetch(`/czm-campaign/${campaignSlug}/update-share-count`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
            });
            if (!response.ok) {
                console.error('Failed to update share count.');
            }
        } catch (error) {
            console.error('Error during update share count:', error);
        }
    }
});
