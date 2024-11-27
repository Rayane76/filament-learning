<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'Blog';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Kch hedja';


    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\AuthorsRelationManager::class,
            RelationManagers\CommentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}