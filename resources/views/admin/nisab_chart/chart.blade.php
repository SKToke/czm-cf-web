<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<form id="chartDateRangeForm" class="d-flex justify-content-center align-items-center w-100 mb-4 flex-wrap flex-md-nowrap">
    <div class="form-group d-flex align-items-center me-2">
        <label for="startDate" class="me-2">From:</label>
        <input type="date" id="startDate" class="form-control">
    </div>

    <div class="form-group d-flex align-items-center me-2">
        <label for="endDate" class="me-2">To:</label>
        <input type="date" id="endDate" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
    <button type="button" id="resetButton" class="ms-2 btn btn-secondary">Reset</button>
</form>

<div class="chart-container">
    <canvas id="lineChart"></canvas>
</div>

<script>
    var config = {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Gold Value',
                backgroundColor: 'rgb(54, 162, 235)',
                borderColor: 'rgb(54, 162, 235)',
                data: {!! json_encode($goldValues) !!}
            }, {
                label: 'Silver Value',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: {!! json_encode($silverValues) !!}
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: true,
        }
    };

    var ctx = document.getElementById('lineChart').getContext('2d');
    new Chart(ctx, config);
</script>

<script>
    document.getElementById('chartDateRangeForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;

        window.location.href = '?startDate=' + startDate + '&endDate=' + endDate;
    });

    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';

        window.location.href = window.location.pathname;
    });

    window.addEventListener('load', function() {
        var urlParams = new URLSearchParams(window.location.search);
        var startDate = urlParams.get('startDate');
        var endDate = urlParams.get('endDate');

        if (startDate) {
            document.getElementById('startDate').value = startDate;
        }

        if (endDate) {
            document.getElementById('endDate').value = endDate;
        }
    });
</script>
