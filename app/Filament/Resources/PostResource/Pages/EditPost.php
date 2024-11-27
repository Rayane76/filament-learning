<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



class EditPost extends EditRecord
{

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Runs before the form fields are saved to the database.

       $record = $this->record->thumbnail;

       if($record !== $data['thumbnail']){
             $data['thumbnail'] = 'lara_test/' . $data['thumbnail'];
             Storage::delete($record);
       }

       return $data;
    }
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->after(function ($record) {
                Storage::delete($record->thumbnail);
            })
            ,
        ];
    }
}
