<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


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
                Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                // ->options(Category::all()->pluck('name', 'id'))
                // ->searchable()
                ->required()
                ,
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
                TextColumn::make('category.name'),
                TextColumn::make('slug'),
                ColorColumn::make('color'),
                ImageColumn::make('thumbnail')    
                ->disk('cloudinary')
                ,
                CheckboxColumn::make('published'),
                SelectColumn::make('category_id')
                ->label('Category')
                ->options(Category::all()->pluck('name', 'id'))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
