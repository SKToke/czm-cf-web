<x-main>
    @include('home.sections.banner')
    <div class="w-100 container">
        <div class="row w-100">
            <div class="col-md-8 p-3">
                <div class="tabs-container w-100">
                    <div class="tabs d-flex">
                        <li class="tab-item">
                            <a href="#" class="nav-link active" id="tab1">Personal Zakat Calculator</a>
                        </li>
                        <li class="tab-item">
                            <a href="#" class="nav-link" id="tab2">Business Zakat Calculator</a>
                        </li>
                    </div>
                    <div id="personalForm" class="tab-content-item">
                        @include('zakat.personal_zakat_form')
                    </div>
                    <div id="businessForm" class="tab-content-item" style="display: none;">
                        @include('zakat.business_zakat_form')
                    </div>
                </div>
                <div id="resultPartial" style="display: none;">
                    @include('zakat.result')
                </div>
            </div>
            <div class="col-md-4 p-3 overflow-none">
                @include('zakat.nisab_card')
            </div>
        </div>
    </div>
</x-main>
