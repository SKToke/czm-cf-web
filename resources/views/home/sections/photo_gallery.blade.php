<div>
    <div class="container">
        @if(count($imagesWithTitle)!=0)
            <div class="gallery">
                    @foreach ($imagesWithTitle as $content)
                        <div class="image-container" data-action="click->gallery#displayImage"  data-image-path="{{ $content['image'] }}" data-case-title="{{ $content['title'] }}">
                            <img src="{{ $content['image'] }}" alt="image" >
                            <div class="overlay">
                                <span class="icon fa fa-search"></span>
                            </div>
                        </div>
                    @endforeach
            </div>
        @else
            <p class="text-center">No record found</p>
        @endif

            <div class="row pagination text-center mb-20">
                @if(method_exists($imagesWithTitle, 'links'))
                    {{ $imagesWithTitle->links() }}
                @endif
            </div>

    </div>
    <div id="imageViewModal" class="image-viewer-modal" data-action="click->gallery#closeImage">
        <div class="modal-dialog">
            <div class="modal-content">
                <button id="prevImage" class="nav-button">❮</button>
                <div class="modal-body">
                    <img id="modalImage">
                    <h3 id="caseTitle" class="text-white"></h3>
                </div>
                <button id="nextImage" class="nav-button">❯</button>

            </div>
        </div>
    </div>

</div>
<script src="{{ asset('js/gallery.js') }}"></script>
