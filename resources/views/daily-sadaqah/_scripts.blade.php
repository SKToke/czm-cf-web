<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ===============================
        // CONFIG
        // ===============================
        const amountSets = {
            daily:   [10,20,30,50,100],
            monthly: [100,300,500,1000,5000]
        };

        let currentFrequency = 'daily';

        const amountContainer = document.getElementById('amount-buttons');
        const amountInput     = document.getElementById('amount');
        const freqButtons     = document.querySelectorAll('.freq-btn');
        const frequencyInput  = document.getElementById('frequency');

        // ===============================
        // RENDER AMOUNT BUTTONS
        // ===============================
        function renderAmounts(type){

            amountContainer.innerHTML = '';

            const amounts = amountSets[type];

            amounts.forEach((amt, index)=>{

                let col = document.createElement('div');
                col.className = 'col-4';

                let btn = document.createElement('button');
                btn.type = 'button';
                btn.className =
                    'btn w-100 amount-btn ' +
                    (index===0 ? 'active' : '');

                btn.dataset.amount = amt;
                btn.innerText = '৳ ' + amt;

                btn.onclick = function(){

                    document.querySelectorAll('.amount-btn')
                        .forEach(b=>b.classList.remove('active'));

                    this.classList.add('active');

                    amountInput.value = this.dataset.amount;
                };

                col.appendChild(btn);
                amountContainer.appendChild(col);
            });

            // === ANY AMOUNT BUTTON ===
            let anyCol = document.createElement('div');
            anyCol.className = 'col-4';

            let anyBtn = document.createElement('button');
            anyBtn.type = 'button';
            anyBtn.className = 'btn w-100 amount-btn';
            anyBtn.innerText = 'Any Amount';

            anyBtn.onclick = function(){

                document.querySelectorAll('.amount-btn')
                    .forEach(b=>b.classList.remove('active'));

                this.classList.add('active');

                // minimum value of current set
                amountInput.value = amounts[0];
                amountInput.focus();
            };

            anyCol.appendChild(anyBtn);
            amountContainer.appendChild(anyCol);

            // default first value
            amountInput.value = amounts[0];
        }
        function _renderAmounts(type){

            amountContainer.innerHTML = '';

            const amounts = amountSets[type];

            amounts.forEach((amt, index)=>{

                let col = document.createElement('div');
                col.className = 'col-4';

                let btn = document.createElement('button');
                btn.type = 'button';
                btn.className =
                    'btn w-100 amount-btn ' +
                    (index===0
                        ? 'btn-success text-white active'
                        : 'btn-outline-secondary');

                btn.dataset.amount = amt;
                btn.innerText = '৳ ' + amt;

                // click select
                btn.onclick = function(){

                    document.querySelectorAll('.amount-btn')
                        .forEach(b=>{
                            b.classList.remove('btn-success','text-white','active');
                            b.classList.add('btn-outline-secondary');
                        });

                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success','text-white','active');

                    amountInput.value = this.dataset.amount;
                };

                col.appendChild(btn);
                amountContainer.appendChild(col);
            });

            // default first value
            amountInput.value = amounts[0];
        }

        // ===============================
        // FREQUENCY SWITCH
        // ===============================
        freqButtons.forEach(btn => {

            btn.onclick = function(){

                // toggle active style
                freqButtons.forEach(b=>{
                    b.classList.remove('btn-success','text-white','active');
                    b.classList.add('btn-outline-secondary');
                });

                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-success','text-white','active');

                currentFrequency = this.dataset.value;
                frequencyInput.value = currentFrequency;

                // regenerate amount buttons
                renderAmounts(currentFrequency);
            }
        });

        // ===============================
        // INIT LOAD
        // ===============================
        renderAmounts('daily');

    });
</script>
