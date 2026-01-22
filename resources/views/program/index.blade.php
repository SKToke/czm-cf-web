<x-main>
    @include('home.sections.banner')
    <div class="my-4 w-75 mx-auto">
        <div class="row single-service-style">
            @foreach($programs as $program)
                @include('program.card', ['program' => $program])
            @endforeach
        </div>
    </div>
</x-main>
