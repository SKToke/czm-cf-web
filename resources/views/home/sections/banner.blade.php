@if(isset($banner))
    <section class="inner-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12 sec-title colored text-center">
                    <h2>{{ $banner->key }}</h2>
                    <ul class="breadcumb ">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li ><i class="fa fa-angle-right mt-10"></i></li>
                        <li><span>{{ $banner->key }}</span></li>
                    </ul>
                    <span class="decor"><span class="inner"></span></span>
                </div>
            </div>
        </div>
        @if($banner->hasImage())
            <div class="banner-image position-absolute top-0 left-0 w-100 h-100">
                <img src="{{ $banner->getImageUrl() }}" alt="{{ $banner->key }}" class="w-100 h-100">
            </div>
        @endif
    </section>
@endif
