<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->label('Select Product'),

                Forms\Components\Select::make('payment_method')
                    ->options([
                        'bank_bri' => 'Bank BRI',
                        'bank_mandiri' => 'Bank Mandiri',
                        'cod' => 'Cash on Delivery',
                    ])
                    ->required()
                    ->label('Payment Method')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (strpos($state, 'bank_') !== false) {
                            // Set nomor rekening otomatis
                            $set('bank_account_number', $state === 'bank_bri' ? '123456789' : '987654321');
                        } else {
                            // Reset jika COD
                            $set('bank_account_number', null);
                        }
                    }),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('Amount')
                    ->minValue(0)
                    ->default(0),

                Forms\Components\TextInput::make('bank_account_number')
                    ->label('Bank Account Number')
                    ->required(fn ($get) => strpos($get('payment_method'), 'bank_') !== false) // Wajib jika metode adalah bank
                    ->placeholder('Enter your bank account number')
                    ->hidden(fn ($get) => $get('payment_method') === 'cod'), // Sembunyikan jika COD
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('product.name')->label('Product'),
                Tables\Columns\TextColumn::make('payment_method')->label('Payment Method'),
                Tables\Columns\TextColumn::make('amount')->label('Amount')->money('idr'),
                Tables\Columns\TextColumn::make('bank_account_number')
    ->label('Bank Account Number')
    ->getStateUsing(fn ($record) => $record->payment_method === 'cod' 
        ? 'Bayar Di tempat' 
        : ($record->payment_method === 'bank_bri' ? '123456789' : '987654321')
    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->getStateUsing(fn ($record) => Carbon::parse($record->created_at)->setTimezone('Asia/Jakarta')->toDateTimeString()),
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
            // Tambahkan relasi jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}