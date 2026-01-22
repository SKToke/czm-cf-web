<x-main>
    <div class="bt-titlebar bt-titlebar-v1">
        <div class="bt-titlebar-inner">
            <div class="bt-overlay"></div>
            <div class="bt-subheader">
                <div class="bt-subheader-inner container">
                    <div class="bt-subheader-cell bt-center">
                        <div class="bt-content text-center">
                            <div class="bt-page-title">
                                <h2>CZM Member's Profile</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bt-main-content">
        <div class="container">
                    <article class="post-281 team type-team status-publish has-post-thumbnail hentry">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="bt-thumb">
                                    <img height="952" class="img-fluid" width="952" src="{{ $member->getImage() }}" alt="{{ $member->name }}" decoding="async" fetchpriority="high" sizes="(max-width: 952px) 100vw, 952px">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="bt-title">{{ $member->name }}</h3>
                                <div class="bt-position">{{ $member->self_designation }}</div>
                                <div class="bt-content">

                                        @if($member->description)
                                        <p class="read-more">{{ $member->description }}</p>
                                        @endif

                                </div>
                                <div class="bt-info">
                                    <h4>Information</h4>
                                    <ul>
                                        @if($member->contact_number)
                                        @php
                                        $formatted_contact_number = str_replace('_', '', $member->contact_number);
                                        @endphp
                                        <li><strong>Phone: </strong><a class="bt-phone" href="tel:{{ $formatted_contact_number }}">{{ $formatted_contact_number }}</a></li>
                                        @endif
                                        @if($member->email_address)
                                        <li><strong>Email: </strong><a class="bt-email" href="mailto:{{ $member->email_address }}">{{ $member->email_address }}</a></li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="bt-social">
                                    <h4>Social</h4>
                                    <ul class="bt-socials">
                                        @if($member->facebook_link)
                                        <li><a href="{{ $member->facebook_link }}" target="_blank" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a></li>
                                        @endif
                                        @if($member->linkedin_link)
                                        <li><a href="{{ $member->linkedin_link }}" target="_blank" class="social-icon"><i class="fa-brands fa-linkedin"></i></a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </article>

        </div>
    </div>
    <script src="{{ asset('js/readMore.js') }}"></script>
</x-main>
