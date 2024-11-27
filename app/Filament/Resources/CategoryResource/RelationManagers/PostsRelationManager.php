<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required(),
                TextInput::make('slug')->required(),
                ColorPicker::make('color')->required(),
                MarkdownEditor::make('content')->required(),
                FileUpload::make('thumbnail')
                ->disk('cloudinary')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg' , 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/tiff'])
                ->getUploadedFileNameForStorageUsing(
                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())->chopEnd(['.png','.jpeg','.jpg','.gif','.webp','.svg+xml','.bmp','.tiff'])->prepend(Str::random(8))
                )                 
                ,
                Checkbox::make('published')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug'),
                ColorColumn::make('color'),
                ImageColumn::make('thumbnail')    
                ->disk('cloudinary')
                ,
                CheckboxColumn::make('published'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function(array $data): array {

                    $data['thumbnail'] = 'lara_test/' . $data['thumbnail'];

                    return $data;

                })
                ,
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateFormDataUsing(function(array $data): array{
                    $infos = $this->getMountedTableActionRecord();

                    $record = $infos->thumbnail;

                    if($record !== $data['thumbnail']){
                          $data['thumbnail'] = 'lara_test/' . $data['thumbnail'];
                          Storage::delete($record);
                    }
                          
                    return $data;
                })
                ,
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->after(function ($records) {
                        $records->each(function ($record) {
                            Storage::delete($record->thumbnail);
                        });
                    })
                    ,
                ]),
            ]);
    }
}
