<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestImagesResource\Pages;
use App\Filament\Resources\TestImagesResource\RelationManagers;
use App\Models\TestImages;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;





class TestImagesResource extends Resource
{
    protected static ?string $model = TestImages::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('images')
                ->disk('cloudinary')
                ->multiple()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg' , 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/tiff'])
                ->getUploadedFileNameForStorageUsing(
                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())->chopEnd(['.png','.jpeg','.jpg','.gif','.webp','.svg+xml','.bmp','.tiff'])->prepend(Str::random(8))
                )                 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->after(function ($records) {
                        $records->each(function ($record) {
                            Storage::delete($record->images);
                        });
                    })
                    ,
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestImages::route('/'),
            'create' => Pages\CreateTestImages::route('/create'),
            'edit' => Pages\EditTestImages::route('/{record}/edit'),
        ];
    }
}
