<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube'; // Ganti dengan ikon yang valid

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->label('Product Name'),

            Forms\Components\Textarea::make('description')
                ->required()
                ->label('Description'),

            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->label('Price')
                ->minValue(0) // Menambahkan batas minimum untuk harga
                ->default(0), // Menetapkan nilai default

            Forms\Components\FileUpload::make('image')
                ->image()
                ->disk('public')
                ->directory('product-images')
                ->required()
                ->label('Product Image'), // Menambahkan label untuk gambar produk
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('name')->label('Product Name'),
            Tables\Columns\TextColumn::make('price')->label('Price')->money('idr'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->getStateUsing(fn ($record) => Carbon::parse($record->created_at)->setTimezone('Asia/Jakarta')->toDateTimeString()),
                Tables\Columns\ImageColumn::make('image') // Pastikan menggunakan field 'image' dari model
    ->label('Product Image')
    ->size(50)
    ->default('storage/default.png') // Mengatur gambar default jika tidak ada
    ->getStateUsing(fn ($record) => $record->image ?? 'storage/default.png'), // Menggunakan gambar dari model
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
