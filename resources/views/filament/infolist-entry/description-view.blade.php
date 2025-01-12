<div class="grid gap-y-2">
    <div class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Omschrijving</div>

    <div class="text-sm leading-5">
        {!! str($this->shift->description)->sanitizeHtml() !!}
    </div>
</div>
