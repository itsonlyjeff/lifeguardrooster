<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        <ul>
            @forelse($getRecord()->getMedia('*') as $media)
                <li><a href="{{ route('expenses.download', [$media->uuid]) }}" target="_blank">{{ $media->name }} ({{ $media->mime_type }})</a></li>
            @empty
                <li>Geen bijlagen</li>
            @endforelse
        </ul>
    </div>
</x-dynamic-component>
