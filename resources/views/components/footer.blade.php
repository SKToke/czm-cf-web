<footer class="footer mt-30 sec-padding pb-2 d-flex justify-content-center">
    <div class="w-75">
        <div class="row">
            <div class="col-md-4 col-sm-6 pe-5">
                <div class="footer-widget about-widget">

                    <img src="{{ asset('images/czm_logo_bg_white.png') }}" alt="CZM" class="main-logo w-50 h-25">

                    <h3 class="title">About Us</h3>
                    <p class="text-align-justify mb-15">
                        Center for Zakat Management (CZM) is a social enterprise, aiming to promote the obligation of zakat set by Allah as an economic tool for bringing prosperity to the needy.
                    </p>
                    <a href="#" class="app-link" target="_blank" rel="noopener noreferrer">
                        Click here to Download CZM App
                    </a>
                </div>
            </div>

            <div class="col-md-2 latest-post col-sm-6">
                <div class="footer-widget latest-post">
                    <h3 class="title">Contact Us</h3>
                    <ul class="contact">
                        <li class="d-flex align-items-center">
                            <i class="fa fa-map-marker me-2"></i>
                            <span>1st Floor, 113/B, Tejgaon Industrial Area,<br> Dhaka-1208</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fa fa-phone me-2"></i>
                            <span>01729 296 296</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fa fa-envelope me-2"></i>
                            <span>info@czm-bd.org</span>
                        </li>
                    </ul>
                    <ul class="social">
                        <li>
                            <a href="https://www.facebook.com/czm.org/" aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        </li>
                        <li class="ms-2">
                            <a href="https://bd.linkedin.com/company/center-for-zakat-management-czm" aria-label="Linkedin">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </li>
                        <li class="ms-2">
                            <a href="https://www.youtube.com/c/CenterforZakatManagement" aria-label="YouTube">
                                <i class="fa-brands fa-youtube"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="footer-widget quick-links">
                    <h3 class="title">Pages</h3>
                    <ul>
                        <li>
                            <a href="{{ route('aboutUs') }}">About us</a>
                        </li>
                        <li>
                            <a href="{{ route('jobPost.index') }}">Career</a>
                        </li>
                        <li>
                            <a href="{{ route('success_stories') }}">Success Stories</a>
                        </li>
                        <li>
                            <a href="{{ route('notices') }}">Notice Board</a>
                        </li>
                        <li>
                            <a href="{{ route('terms-and-conditions') }}">Terms and Condition</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy-policy') }}">Privacy policy</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 footer-subscription-form-container">
                <div class="footer-widget contact-widget">
                    <h3 class="title">Subscribe</h3>
                    <form method="POST" action="{{ route('subscribe') }}" class="subscription-form ajax-form" id="footer-cf" data-action-url="{{ route('subscribe') }}">
                        @csrf
                        <input type="text" name="name" placeholder="Name*" required>
                        <input type="email" name="email" placeholder="Email Address*" required>
                        <input type="text" name="phone" placeholder="Phone Number">
                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <img src="{{ asset('images/payment-logo/SSLCommerz-Pay-With-logo-All-Size-03.png') }}" alt="CZM" class="">
        </div>
    </div>
</footer>

<section class="footer-bottom">
    <div class="container text-center">
        <p>Copyright © 2024 by czm-bd.org. All rights reserved.</p>
    </div>
</section>
<script src="{{ asset('js/mapLoader.js') }}"></script>
