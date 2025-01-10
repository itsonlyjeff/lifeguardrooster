<div>
    @if (count($media))
        <div class="flex flex-col flex-none space-y-6 max-h-96 overflow-y-auto">
            <ul>
                @foreach ($media as $mediafile)
                    <li>
                        {{ $mediafile->name }} ({{ $mediafile->mime_type }})
                        <a href="{{ route('download', ['mediaItem' => $mediafile->id]) }}"
                           class="text-blue-500 underline">Download</a>
                    </li>

                @endforeach
            </ul>
        </div>
    @else
        <p>Geen bijlagen beschikbaar.</p>
    @endif
</div>
