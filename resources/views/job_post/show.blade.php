<x-main>
    <div class="individual-job container mt-5">
        <div class="row mt-4">
            <div class="col-md-8">
                <h2 class="job-title">{{ $jobPost->title }}</h2>
                <p class="description">{!! $jobPost->description !!}</p>
                <div class="details-content">
                    <form action="{{ route('jobPost.submit') }}" method="POST" enctype="multipart/form-data" class="ajax-form" data-action-url="{{ route('jobPost.submit') }}">
                        @csrf {{-- CSRF token for Laravel --}}
                        <h2>Apply online</h2>
                        <input type="hidden" name="job_post_id" value="{{ $jobPost->id }}">

                        <div class="form-group">
                            <label for="applicant_name">Applicant Name<span class="red-asterisk">*</span></label>
                            <input type="text" id="applicant_name" name="applicant_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="mobile_no">Applicant Mobile Number (with country code)<span class="red-asterisk">*</span></label>
                            <input type="tel" id="mobile_no" name="mobile_no" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email<span class="red-asterisk">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <textarea id="comment" name="comment" class="form-control"></textarea>
                        </div>

                        <div class="form-group file-upload">
                            <label for="cv">Choose a file or drag & drop it here<span class="red-asterisk">*</span></label>
                            <span class="formats">JPEG, PNG and PDF formats, up to 50MB</span>
                            <input type="file" id="cv" name="cv" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-submit">Submit</button>
                    </form>
                </div>

            </div>

            <div class="col-md-4">
                <div class="sidebar">
                    <div class="company-info">
                        <i class="fa fa-building"></i>
                        <span class="ms-2">{{ $jobPost->company_name }}</span>
                    </div>
                    <div class="detail">
                        <i class="fa fa-clock"></i>
                        <span class="ms-2">{{ $jobPost->job_nature }}</span>
                    </div>
                    <div class="detail">
                        <i class="fa fa-map-marker-alt"></i>
                        <span class="ms-2">{{ $jobPost->location }}</span>
                    </div>
                    <div class="detail">
                        <i class="fa fa-calendar-alt"></i>
                        <span class="ms-2">Posted {{ $jobPost->created_at->diffForHumans() }}</span>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main>
