@foreach($jobPosts as $jobPost)
    <div class="job-card">
        <div class="row">
            <div class="col-md-2 logo-container">
                @if($jobPost->logo)
                    <div class="mt-1 ms-2">
                        <img src="{{ $jobPost->getLogoUrl() }}" alt="Job Logo" class="">
                    </div>
                @endif
            </div>
            <div class="col-md-10 h-25">
                <h2 class="job-title">{{ $jobPost->title }}</h2>
                <div class="job-details row">
                    <div class="col-12 mt-3">
                                        <span class="job-detail">
                                            <i class="fas fa-building"></i>
                                            {{ $jobPost->company_name }}
                                        </span>
                    </div>
                    <div class="col-10 d-flex flex-wrap">
                                        <span class="job-detail mt-2">
                                            <i class="fas fa-clock"></i>
                                            {{ $jobPost->job_nature }}
                                        </span>
                        <span class="job-detail mt-2">
                                            <i class="fa-solid fa-location-dot me-2"></i>
                                            {{ $jobPost->location }}
                                        </span>
                        <span class="job-detail mt-2">
                                            <i class="fa-regular fa-calendar-days me-2"></i>
                                            Posted {{ $jobPost->created_at->diffForHumans() }}
                                        </span>
                        <span class="job-detail mt-2">
                                            <i class="fa-regular fa-calendar-days me-2"></i>
                                            Deadline: {{ $jobPost->closing_date }}
                                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-10">
                <div class="job-description ms-2" style="overflow: hidden">
                    {!! Str::limit($jobPost->description, 100, '...') !!}
                </div>
            </div>
            <div class="col-md-2 text-center">
                <a href="{{ route('jobPost.show', $jobPost->slug) }}" class="btn apply-now">Apply Now</a>
            </div>
        </div>
    </div>
@endforeach
