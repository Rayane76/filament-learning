<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')->relationship('user', 'name')->searchable()->preload(),
                TextInput::make('comment'),
                MorphToSelect::make('commentable')
                ->types([
                    MorphToSelect\Type::make(Post::class)->titleAttribute('title'),
                    MorphToSelect\Type::make(User::class)->titleAttribute('name'),
                    MorphToSelect\Type::make(Comment::class)->titleAttribute('comment'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('commentable_type')
                ->label('Type')
                ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('commentable_id')
                ->label('Title')
                ->formatStateUsing(function ($record) {
                    // Load the related model based on commentable_type
                    $relatedModel = match ($record->commentable_type) {
                        'App\Models\Post' => Post::find($record->commentable_id),
                        'App\Models\User' => User::find($record->commentable_id),
                        'App\Models\Comment' => Comment::find($record->commentable_id),
                        default => null,
                    };
            
                    // Return the specific attribute based on the type of the related model
                    return match ($record->commentable_type) {
                        'App\Models\Post' => $relatedModel?->title ?? 'N/A',
                        'App\Models\User' => $relatedModel?->name ?? 'N/A',
                        'App\Models\Comment' => $relatedModel?->comment ?? 'N/A',
                        default => 'N/A',
                    };
                }),
                TextColumn::make('comment')->limit(15),
                TextColumn::make('user.name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
