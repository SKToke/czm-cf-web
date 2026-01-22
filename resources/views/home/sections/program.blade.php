<div class="my-4 w-75 mx-auto">
    <h3 class="text-center fw-bold czm-primary-text">
        CZM Zakat Distribution & Utilization Programs
    </h3>
    <div class="sec-title d-flex justify-content-center mb-3">
        <span class="decor">
            <span class="inner"></span>
        </span>
    </div>
    <div class="row single-service-style">
        @foreach($programs as $program)
            @include('program.card', ['program' => $program])
        @endforeach
    </div>
</div>
