@php
    use Illuminate\Support\Facades\Storage;
    $images = \Illuminate\Support\Arr::wrap($getState());
    $imageUrl = fn($path) => $path
        ? Storage::disk('public')->url($path) . '?v=' . (optional($entry->getRecord()->updated_at)->timestamp ?? time())
        : $getDefaultImageUrl();
@endphp


<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @foreach ($images as $image)
            @php $url = $imageUrl($image); @endphp
            <a href="{{ $url }}"
               target="_blank">
                <img class="rounded-2xl overflow-hidden" src="{{ $url }}" alt="" />
            </a>
        @endforeach
    </div>
</x-dynamic-component>
