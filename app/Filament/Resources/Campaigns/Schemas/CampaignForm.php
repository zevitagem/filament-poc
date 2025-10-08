<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Services\SpunManagerService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Campaign Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('template_html')
                    ->label('HTML Template')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),

                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled Date')
                    ->required()
                    ->default(now()),

                Repeater::make('recipients')
                    ->label('Recipients')
                    ->schema([
                        Select::make('application_id')
                            ->label('Application')
                            ->options(fn() => app(SpunManagerService::class)->getApplicationOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $set('list_id', null);
                                $set('segment_id', null);
                            }),

                        Select::make('list_id')
                            ->label('List')
                            ->options(function (Get $get) {
                                $applicationId = $get('application_id');
                                if (!$applicationId) {
                                    return [];
                                }
                                return app(SpunManagerService::class)->getListOptions($applicationId);
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $set('segment_id', null);
                            }),

                        Select::make('segment_id')
                            ->label('Segment')
                            ->options(function (Get $get) {
                                $listId = $get('list_id');
                                if (!$listId) {
                                    return [];
                                }
                                return app(SpunManagerService::class)->getSegmentOptions($listId);
                            })
                            ->required(),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Recipient')
                    ->defaultItems(1)
                    ->minItems(1)
                    ->columnSpanFull(),
            ]);
    }
}
