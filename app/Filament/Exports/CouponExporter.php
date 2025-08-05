<?php

namespace App\Filament\Exports;

use App\Models\Coupon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class CouponExporter extends Exporter
{
    protected static ?string $model = Coupon::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('agent.name'),
            ExportColumn::make('branch_id.name'),
            ExportColumn::make('exhibition_id.name'),
            ExportColumn::make('employee.name'),
            ExportColumn::make('sungard_branch_id.name'),
            ExportColumn::make('customer_name'),
            ExportColumn::make('customer_email'),
            ExportColumn::make('customer_phone'),
            ExportColumn::make('coupon_link'),
            ExportColumn::make('car_model'),
            ExportColumn::make('car_brand'),
            ExportColumn::make('car_category'),
            ExportColumn::make('plate_number'),
            ExportColumn::make('is_confirmed'),
            ExportColumn::make('status')
                ->formatStateUsing(fn ($record) => $record->status?->label() ?? ''),

            ExportColumn::make('reserved_date'),
            ExportColumn::make('reached_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your coupon export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
