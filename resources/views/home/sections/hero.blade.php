@if($heroes)
    <section class="home-slider sec-padding">
        <div class="container">
            <section class="rev_slider_wrapper splide" data-controller="splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        @foreach($heroes as $hero)
                            <li class="splide__slide hero-slider">
                                @if($hero->link)
                                    <a href="{{ $hero->link }}" target="_blank">
                                        @endif
                                        <img src="{{ $hero->getPhoto() }}" alt="{{ $hero->description }}" class="mw-100" @if($hero->link) style="cursor: pointer;" @endif>
                                        @if($hero->link)
                                    </a>
                                @endif
                                @if($hero->description)
                                    <h5 class="hero-text-box fw-bold text-white hero-overlay-text mt-80 d-flex justify-content-center align-items-center">{{ $hero->description }}</h5>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>
    </section>
@endif
