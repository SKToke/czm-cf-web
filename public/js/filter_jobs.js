document.addEventListener('DOMContentLoaded', function() {
    const titleSelect = document.getElementById('title');
    const deadlineSelect = document.getElementById('deadline');
    const jobListContainer = document.getElementById('job_list');
    const filterForm = document.getElementById('filter-jobs-form');


    document.querySelector('#reset-filter-btn').addEventListener('click', function() {
        document.querySelector('#filter-jobs-form').reset();
        fetchFilteredJobs();
    });


    filterForm.addEventListener('submit', function(event) {
        event.preventDefault();
        fetchFilteredJobs();
    });


    function fetchFilteredJobs() {
        event.preventDefault();
        const titleValue = titleSelect.value;
        const deadlineValue = deadlineSelect.value;

        const url = `/filter-job-posts`;
        const data = {
            title: titleValue,
            deadline: deadlineValue
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Include CSRF token for Laravel
            },
            body: JSON.stringify(data)
        })
            .then(response => response.text())
            .then(html => {
                jobListContainer.innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    }

    titleSelect.addEventListener('input', fetchFilteredJobs);
    deadlineSelect.addEventListener('change', fetchFilteredJobs);
});
