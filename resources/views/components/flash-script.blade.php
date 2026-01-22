<script>
    const flashMessageData = {!! json_encode(session('flash')) !!};
    @php session()->forget('flash'); @endphp
</script>
