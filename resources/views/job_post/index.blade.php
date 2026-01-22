<x-main>
    @include('home.sections.banner')
    <div class="container mb-0 mt-4">
        @if($jobPosts->isEmpty())
            <h5 class="text-center fst-italic mt-50 mb-50">No jobs available at this moment</h5>
        @else
            <div class="job-listing">
                <form id="filter-jobs-form" action="{{ route('filter-jobs') }}" method="POST" class="row mb-20">
                    <div class="col-md-4 mt-10">
                        <label for="deadline" class="d-inline-block">Deadline</label>
                        @php
                            $lastDayOfYear = now()->format('Y-m-d');
                        @endphp
                        <input type="date" name="deadline" id="deadline" class="d-inline-block custom-date-field" data-target="job-filter.selectDate" value="{{ $lastDayOfYear }}">
                    </div>

                    <div class="col-md-4 d-flex align-content-center justify-content-center mt-10">
                        <label for="title" class="d-inline-block d-flex align-items-center">Title</label>
                        <input type="text" name="title" id="title" class="d-inline-block custom-text-field" placeholder="Search by Title">
                    </div>
                    <div class="text-center text-md-end col-md-4">
                        <button type="button" id="reset-filter-btn" class="btn btn-sm reset-btn btn-outline-secondary mt-10" data-target="case-filter.clearButton">Reset Filter</button>
                    </div>

                </form>

                <div id="job_list">
                    @include('job_post.filtered_jobs', ['jobPosts' => $jobPosts])
                </div>

            </div>
        @endif
    </div>
</x-main>
<script src="{{ asset('js/filter_jobs.js') }}"></script>
