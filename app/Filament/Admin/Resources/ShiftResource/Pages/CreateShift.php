<?php

namespace App\Filament\Admin\Resources\ShiftResource\Pages;

use App\Filament\Admin\Resources\ShiftResource;
use App\Models\Department;
use App\Models\ShiftSchedule;
use App\Models\ShiftTemplate;
use App\Models\ShiftType;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateShift extends CreateRecord
{
    protected static string $resource = ShiftResource::class;

    public function form(Form $form): Form
    {
        $components = [
            TextInput::make('name')
                ->label('Naam')
                ->required(),
            DateTimePicker::make('start')
                ->label('Aanvang dienst')
                ->seconds(false)
                ->weekStartsOnMonday()
                ->displayFormat('d-m-Y H:i:s')
                ->required(),
            DateTimePicker::make('end')
                ->label('Einde dienst')
                ->seconds(false)
                ->weekStartsOnMonday()
                ->displayFormat('d-m-Y H:i:s')
                ->after('start')
                ->required(),
            Select::make('department_id')
                ->label('Afdeling')
                ->required()
                ->options(Department::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id')),
            Select::make('shift_type_id')
                ->label('Soort dienst')
                ->required()
                ->options(ShiftType::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id')),
            Select::make('shift_template')
                ->label('Template')
                ->dehydrated(false)
                ->nullable()
                ->options(ShiftTemplate::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id')),

        ];

        return $form
            ->schema($components);
    }

    public function afterCreate(): void
    {
        $shift = $this->record;
        $formData = $this->data;

        $shiftSchedules = [];

        if ($formData['shift_template']) {
            $template = ShiftTemplate::findOrFail($formData['shift_template']);

            foreach ($template->shiftTemplateSchedules as $shiftTemplateSchedule) {
                ShiftSchedule::create([
                    'shift_id' => $shift->id,
                    'role_id' => $shiftTemplateSchedule->role_id,
                    'amount' => $shiftTemplateSchedule->amount,
                ]);
            }
        }
    }
}
