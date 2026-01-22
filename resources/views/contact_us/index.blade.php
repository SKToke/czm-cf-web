<x-main>
    @include('home.sections.banner')
    <div class="contact-content sec-padding contact-us">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h2>Contact Form</h2>
                    <form method="POST" data-action-url="{{ route('contact-us.store') }}" class="contact-form row ajax-form" id="contact-page-contact-form">
                        @csrf
                        <div class="col-md-12 d-flex justify-content-between tab w-100 mb-2">
                            <button type="button" class="w-50 tablinks" id="contactUsTab">Contact Us</button>
                            <button type="button" class="w-50 tablinks" id="zakatConsultancyTab">Zakat/Sadaqah/Waqf Consultancy</button>
                        </div>

                        <div class="tabcontent" id="contactUsContent">
                            <input type="radio" name="contact_type" value="{{ $general }}" id="general" checked class="radio_btn mb-0" hidden>
                        </div>
                        <div class="tabcontent" id="zakatConsultancyContent">
                            <div id="zakatOptions" class="form-group mt-2 ms-0">
                                <div class="f-check w-50 d-flex align-items-center">
                                    <input type="radio" name="contact_type" value="{{ $personalZakat }}" id="personalZakat" class="radio_btn mb-0">
                                    <label for="personalZakat" class="ms-3">Personal</label>
                                </div>
                                <div class="f-check w-50 d-flex align-items-center ml-20">
                                    <input type="radio" name="contact_type" value="{{ $businessZakat }}" id="businessZakat" class="radio_btn mb-0">
                                    <label for="businessZakat" class="ms-3">Business</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if(isset($campaignTitle))
                                <input type="text" name="campaign_title" value="{{'Campaign: ' . $campaignTitle }}" readonly class="form-control campaign-field mt-2">
                                <input type="hidden" name="campaign_id" value="{{ $campaignId }}">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="name" placeholder="Name*" required class="form-control">
                            <input type="email" name="email" placeholder="Email*" required class="form-control">
                            <input type="text" name="mobile_no" placeholder="Mobile number" id="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <textarea name="message" placeholder="Message" cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_V3_SITE_KEY') }}"></div>

                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                @endif
                            </div>
                        </div>


                        <div class="col-md-3" style="margin-top: 25px">
                            <button type="submit" class="send-btn p-3" style="width: 150px">SEND</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 mobile-margin-top">
                    <h2>Address</h2>
                    <ul class="contact-info">
                        <li>
                            <div class="icon-box d-flex align-content-center">
                                <div class="inner">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <div class="content-box">
                                    <h4>Address</h4>
                                    <p>
                                        1st Floor, 113/B,<br>
                                        Tejgaon Industrial Area, Dhaka-1208
                                    </p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="icon-box d-flex align-content-center">
                                <div class="inner">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div class="content-box">
                                    <h4>Phone</h4>
                                    <p>+8801729296296, +8802 2222 98255</p>
                                </div>
                            </div>
                        </li>
                        <li class="mt-40">
                            <div class="icon-box d-flex align-content-center">
                                <div class="inner">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="content-box">
                                    <h4>Email</h4>
                                    <p>info@czm-bd.org, www.czm-bd.org</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container w-100 mb-2 mt-4">
        <div id="map" class="w-100" style="height: 300px;"></div>
    </div>
</x-main>
<script src="{{ asset('js/contact_us.js') }}"></script>
