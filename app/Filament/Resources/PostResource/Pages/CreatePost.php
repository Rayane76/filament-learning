<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreatePost extends CreateRecord
{
    
    protected function mutateFormDataBeforeCreate(array $data): array
{
    //save public_id
    $data['thumbnail'] = 'lara_test/' . $data['thumbnail'];

    return $data;
}

    protected static string $resource = PostResource::class;
    
}
