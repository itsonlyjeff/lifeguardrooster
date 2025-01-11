<div>
    <div class="flex flex-col space-y-5 w-1/4">
        @forelse ($users as $user)
            <div class="flex flex-row justify-between">
                <div class="font-bold">{{ $user->role->name  }}</div>
                <div>{{ $user->user->name ?? '-'  }}</div>
            </div>
        @empty
            <div class="flex flex-row space-x-10">
                <div>Nog niemand gepland.</div>
                <div>&nbsp;</div>
            </div>
        @endforelse
    </div>
</div>
