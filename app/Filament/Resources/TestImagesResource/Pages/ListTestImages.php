<?php

namespace App\Filament\Resources\TestImagesResource\Pages;

use App\Filament\Resources\TestImagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestImages extends ListRecords
{
    protected static string $resource = TestImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
