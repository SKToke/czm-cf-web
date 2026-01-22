@php
use Illuminate\Support\Str;
@endphp
<x-main>
    @include('home.sections.banner')
    <div class="section meet-Volunteer pb_30">
        <div class="container text-dark">
            @if ($governance && $governance->description)
                <p class="mt-2">{!! $governance->description !!}</p>
            @else
                <p class="mt-30"></p>
            @endif

            @foreach ($committees as $committee)
                @if ($committee->committeeMembers->isNotEmpty())
                    <div class="accordion governance-accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <div class="accordion-header text-dark" id="flush-heading-{{ $committee->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-{{ $committee->id }}" aria-expanded="false" aria-controls="flush-collapse-{{ $committee->id }}">
                                    {{ $committee->name }}
                                </button>
                                <div class="row">
                                    <div class="col-md-12 mt-2">
                                        <p>{!! $committee->description !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div id="flush-collapse-{{ $committee->id }}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach ($committee->committeeMembers as $committee_member)
                                            @if ($committee_member->member)
                                                <div class="col-md-3">
                                                    <div class="single-team-member">
                                                        <a href="{{ route('czm-member-details', $committee_member->member->id) }}">
                                                            <div class="img-container p-5">
                                                                <img src="{{ $committee_member->member->getImage() }}" alt="{{ $committee_member->member->name }}">
                                                            </div>
                                                            <div class="member-content pt-2">
                                                                <h3>{{ $committee_member->member->name }}</h3>
                                                                @if ($committee_member->member->self_designation)
                                                                    <p title="{{ $committee_member->member->self_designation }}">
                                                                        {{ Str::limit($committee_member->member->self_designation, 65, '...') }}
                                                                    </p>
                                                                @endif
                                                                <div class="committee-designation" title="{{ $committee_member->designation }}">
                                                                    {{ Str::limit($committee_member->designation, 65, '...') }}
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</x-main>
