<?php

namespace App\Filament\Pages;

use App\Models\Shift;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ViewShift extends Page implements HasForms, HasInfolists
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.view-shift';

    protected static ?string $slug = 'shift/{id}';
    protected static bool $shouldRegisterNavigation = false;

    public Shift $shift;
    public array $comments = [];


    public function mount($id)
    {
        $this->shift = Shift::findOrFail($id)->loadMissing(['comments.sender']);
        $this->comments = $this->shift->comments->toArray();
    }

    public function shiftInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->shift)
            ->schema([
                Section::make('Dienst')
                    ->schema([
                        TextEntry::make('name')->label('Naam')->weight(FontWeight::Bold),
                        TextEntry::make('department.name')->label('Afdeling')->badge(),
                        TextEntry::make('shiftType.name')->label('Type dienst')->badge(),
                        TextEntry::make('start')->date('d-m-Y H:i')->label('Start dienst')->icon('heroicon-o-calendar-days')->iconColor('primary'),
                        TextEntry::make('end')->date('d-m-Y H:i')->label('Einde dienst')->icon('heroicon-o-calendar-days')->iconColor('primary'),
                        TextEntry::make('description')->label('Omschrijving')->columnSpanFull()->hidden(!$this->shift->description),
                    ])
                    ->columns([
                        'sm' => 3,
                        'xl' => 4,
                        '2xl' => 6,
                    ]),

                Section::make('Bijlagen')
                    ->schema([
                        ViewEntry::make('attachments')
                            ->view('filament.infolist-entry.media', ['media' => $this->shift->getMedia('attachments')])
                    ]),

                Section::make('Reageren')
                    ->schema([
                        ViewEntry::make('comments')
                            ->view('filament.infolist-entry.comments', ['comments' => $this->comments])
                    ])
                    ->headerActions([
                        Action::make('Reageer')
                            ->form([
                                TextArea::make('comment')->label('Comment')->required(),
                            ])
                            ->action(function (array $data) {
                                $comment = $this->shift->comments()->create([
                                    'sender_id' => auth()->id(),
                                    'body' => $data['comment'],
                                ]);

                                array_unshift($this->comments, $comment->load('sender')->toArray());

                                Notification::make()
                                    ->title('Reactie is toegevoegd')
                                    ->success()
                                    ->send();
                            })
                    ])
            ]);
    }
}
