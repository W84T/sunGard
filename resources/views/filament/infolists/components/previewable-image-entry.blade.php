@php
    $images = \Illuminate\Support\Arr::wrap($getState());
    $imageUrl = fn($path) => $path ? asset($path) : $getDefaultImageUrl();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="pswp-gallery" id="my-gallery">
        @foreach ($images as $image)
            @php $url = $imageUrl($image); @endphp
            <a href="{{ $url }}"
               data-pswp-width="2500"
               data-pswp-height="1667"
               target="">
                <img src="{{ $url }}" alt="" />
            </a>
        @endforeach
    </div>
</x-dynamic-component>
