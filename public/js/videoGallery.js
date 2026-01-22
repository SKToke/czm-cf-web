function playVideo(button) {
    var videoUrl = button.getAttribute('data-video-url');
    var videoId = extractYouTubeID(videoUrl);
    if (!videoId) {
        console.error("Could not extract video ID from URL.");
        return;
    }

    let embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;

    const iframe = document.createElement('iframe');
    iframe.setAttribute('src', embedUrl);
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
    iframe.setAttribute('allowfullscreen', 'true');
    iframe.classList.add('embed-responsive-item');

    const box = document.getElementById('box');
    box.innerHTML = '';
    box.appendChild(iframe);

    const closeButton = document.createElement('button');
    closeButton.textContent = '×';
    closeButton.classList.add('modal-close-button');
    closeButton.onclick = function() {
        toggleBox(false);
    };

    box.appendChild(closeButton);

    toggleBox(true);
}

    function extractYouTubeID(url) {
        const regExp = /^.*(youtu.be\/|v\/|e\/|u\/\w+\/|embed\/|v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        if (match && match[2].length === 11) {
            return match[2];
        } else {
            console.error("The YouTube URL is invalid.");
            return null;
        }
    }

    function toggleBox(show) {
        const box = document.getElementById('box');
        const iframe = box.querySelector('iframe');

        if (show) {
            box.style.display = 'flex';
        } else {
            box.style.display = 'none';
            if (iframe) {
                iframe.setAttribute('src', '');
            }
        }
    }
