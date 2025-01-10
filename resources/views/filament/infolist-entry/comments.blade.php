<div>
    @if (count($comments))
        <div class="flex flex-col flex-none space-y-6 h-96 overflow-y-scroll">
            @foreach ($comments as $comment)

                @if ($comment['sender']['id'] === auth()->user()->id)
                    <div class="flex items-start gap-2.5 justify-end">
                        <div class="flex flex-col gap-1 w-full max-w-[400px]">
                            <div class="flex justify-between items-center space-x-2 rtl:space-x-reverse">
                            <span
                                class="text-sm font-semibold text-gray-900 dark:text-white">{{ $comment['sender']['name'] }}</span>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($comment['created_at'])->format('d-m-Y H:i') }} uur</span>
                            </div>
                            <div
                                class="flex flex-col leading-1.5 p-4 border-blue-200 bg-blue-100 rounded-e-xl rounded-es-xl dark:bg-gray-700">
                                <p class="text-sm font-normal text-gray-900 dark:text-white"> {{ $comment['body'] }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-2.5">
                        <div class="flex flex-col gap-1 w-full max-w-[400px]">
                            <div class="flex justify-between items-center space-x-2 rtl:space-x-reverse">
                            <span
                                class="text-sm font-semibold text-gray-900 dark:text-white">{{ $comment['sender']['name'] }}</span>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($comment['created_at'])->format('d-m-Y H:i') }} uur</span>
                            </div>
                            <div
                                class="flex flex-col leading-1.5 p-4 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl dark:bg-gray-700">
                                <p class="text-sm font-normal text-gray-900 dark:text-white"> {{ $comment['body'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p>Geen opmerkingen beschikbaar.</p>
    @endif
</div>
