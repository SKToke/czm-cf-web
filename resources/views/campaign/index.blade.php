<x-main>
    @include('home.sections.banner')
    <section class="recent-causes sec-padding case-list">
        <div class="container">
            <div class="row">
                <form id="filter-campaigns-form" action="{{ route('filter-campaigns') }}" data-campaign-route="{{ route('campaigns') }}" method="GET">
                    <label for="campaign_title" class="d-inline-block">Search <i class="fa-solid ml-2 search-campaign-icon fa-magnifying-glass"></i></label>
                    <input type="text" name="campaign_title" id="campaign_title" placeholder="By Title" class="d-inline-block custom-search-field mb-4">
                    <br>
                    <label for="category_id" class="d-inline-block">Showing Campaigns for</label>
                    <select name="category_id" id="category_id" class="custom-select d-inline-block" data-target="case-filter.selectCategory">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>

                    <label for="program_id" class="d-inline-block">under</label>
                    <select name="program_id" id="program_id" class="custom-select d-inline-block" data-target="case-filter.selectProgram">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->title }}</option>
                        @endforeach
                    </select>

                    <label for="last_date" class="d-inline-block">for Donation End Date</label>
                    <input type="date" name="last_date" id="last_date" class="d-inline-block custom-date-field" data-target="case-filter.selectDate" placeholder="mm/dd/yyyy" value="mm/dd/yyyy">

                    <div class="mt-3">
                        <label for="min_amount" class="d-inline-block">Allocated Amount From</label>
                        <input type="number" name="min_amount" id="min_amount" class="d-inline-block custom-number-field" placeholder="Min Amount" data-target="case-filter.selectMinAmount">

                        <label for="max_amount" class="d-inline-block">To</label>
                        <input type="number" name="max_amount" id="max_amount" class="d-inline-block custom-number-field" placeholder="Max Amount" data-target="case-filter.selectMaxAmount">
                    </div>

                    <div class="text-end">
                        <button type="button" id="reset-filter-btn" class="btn btn-sm reset-btn btn-outline-secondary" data-target="case-filter.clearButton">Reset Filter</button>
                    </div>
                </form>
            </div>
            <div id="case_list">
                @include('campaign.filtered_campaigns', ['campaigns' => $campaigns])
            </div>
        </div>
    </section>
    <script src="{{ asset('js/filter_campaigns.js') }}"></script>
    <script src="{{ asset('js/campaign_copy_link.js') }}"></script>
</x-main>
