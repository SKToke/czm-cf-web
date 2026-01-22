document.addEventListener('DOMContentLoaded', function() {
    const tab1 = document.getElementById('tab1');
    const tab2 = document.getElementById('tab2');

    tab1.addEventListener('click', function(e) {
        document.getElementById('personalForm').style.display = 'block';
        document.getElementById('businessForm').style.display = 'none';
        tab1.classList.add('active');
        tab2.classList.remove('active');
    });

    tab2.addEventListener('click', function(e) {
        document.getElementById('personalForm').style.display = 'none';
        document.getElementById('businessForm').style.display = 'block';
        tab2.classList.add('active');
        tab1.classList.remove('active');
    });

    const personalForm = document.getElementById('personalZakatForm');
    const businessForm = document.getElementById('businessZakatForm');

    personalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitZakatForm('/zakat/personal', new FormData(this));
    });

    businessForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitZakatForm('/zakat/business', new FormData(this));
    });

    personalForm.addEventListener('reset', function(e) {
        e.preventDefault();
        resetForm();
    });

    businessForm.addEventListener('reset', function(e) {
        e.preventDefault();
        resetForm();
    });

    function resetForm() {
        $.ajax({
            url: '/reset-zakat-form',
            type: 'GET',
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr) {
                console.error('Error resetting Zakat form');
            }
        });
    }

    function submitZakatForm(url, formData) {
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
            .then(response => response.json())
            .then(data => {
                var requestData = JSON.stringify(data.requestedBusinessZakatData);
                if (data.requestedPersonalZakatData != null) {
                    requestData = JSON.stringify(data.requestedPersonalZakatData);
                }

                document.getElementById('totalAssets').textContent = data.totalAssets;
                document.getElementById('totalLiabilities').textContent = data.totalLiabilities;
                document.getElementById('netZakatableAssets').textContent = data.netZakatableAssets;
                document.getElementById('payableZakat').textContent = data.payableZakat;
                document.getElementById('resultPartial').style.display = 'block';

                document.getElementById('hiddenTotalAssets1').value = data.totalAssets.toString();
                document.getElementById('hiddenTotalLiabilities1').value = data.totalLiabilities.toString();
                document.getElementById('hiddenNetZakatableAssets1').value = data.netZakatableAssets.toString();
                document.getElementById('hiddenPayableZakat1').value = data.payableZakat.toString();
                document.getElementById('hiddenRequestedData1').value = requestData;

                document.getElementById('hiddenTotalAssets2').value = data.totalAssets.toString();
                document.getElementById('hiddenTotalLiabilities2').value = data.totalLiabilities.toString();
                document.getElementById('hiddenNetZakatableAssets2').value = data.netZakatableAssets.toString();
                document.getElementById('hiddenPayableZakat2').value = data.payableZakat.toString();
                document.getElementById('hiddenRequestedData2').value = requestData;

                document.getElementById('hiddenTotalAssets3').value = data.totalAssets.toString();
                document.getElementById('hiddenTotalLiabilities3').value = data.totalLiabilities.toString();
                document.getElementById('hiddenNetZakatableAssets3').value = data.netZakatableAssets.toString();
                document.getElementById('hiddenPayableZakat3').value = data.payableZakat.toString();
                document.getElementById('hiddenRequestedData3').value = requestData;

            })
            .catch(error => console.error('Error:', error));
    }
});
