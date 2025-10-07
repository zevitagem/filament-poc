<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('state')
                    ->required(),
                TextInput::make('zip_code')
                    ->required(),
                TextInput::make('country')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
