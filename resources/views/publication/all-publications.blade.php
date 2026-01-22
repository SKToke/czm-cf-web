<x-main>
    @include('home.sections.banner')
    <div class="container">
        <h2 class="publication-title">Our Publications</h2>
        <ul class="publication-links">
            <li><a href="{{ route('books') }}">Books</a></li>
            <li><a href="{{ route('auditReports') }}">Audit Reports</a></li>
            <li><a href="{{ route('newsletters') }}">Newsletters</a></li>
            <li><a href="{{ route('reports') }}">Reports</a></li>
        </ul>
    </div>
</x-main>
