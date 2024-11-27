<?php

namespace App\Filament\Resources\TestImagesResource\Pages;

use App\Filament\Resources\TestImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;


class CreateTestImages extends CreateRecord
{

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //save public_id
        $modified = array_map(function ($str) {
           return 'lara_test/' . $str;
        }, $data['images']);

        $data['images'] = $modified;
    
        return $data;
    }

    protected static string $resource = TestImagesResource::class;
}
