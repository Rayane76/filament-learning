<?php

namespace App\Filament\Resources\TestImagesResource\Pages;

use App\Filament\Resources\TestImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class EditTestImages extends EditRecord
{
    protected static string $resource = TestImagesResource::class;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Runs before the form fields are saved to the database.

       $record = $this->record->images;

       $modified = array_map(function ($str) {
        if (!str_starts_with($str, 'lara_test/')) {
            return 'lara_test/' . $str;
        }   
        // If the string already starts with 'lara_test/', return it unchanged
        return $str;
       }, $data['images']);

       $data['images'] = $modified;

       $array_of_images_to_delete = array_diff($record, $modified);

       Storage::delete($array_of_images_to_delete);

       return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->after(function ($record) {
                Storage::delete($record->images);
            })
            ,
        ];
    }
}
