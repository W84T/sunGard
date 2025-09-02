<?php

namespace App\Filament\Exports;

use App\Models\Coupon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

// OpenSpout (XLSX styling/writer)
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class CouponExporter extends Exporter
{
    protected static ?string $model = Coupon::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label(__('coupon.export.id')),

            ExportColumn::make('agent.name')->label(__('coupon.export.agent_name')),
            ExportColumn::make('branchRelation.name')->label(__('coupon.export.branch_name')),
            ExportColumn::make('exhibitionRelation.name')->label(__('coupon.export.exhibition_name')),
            ExportColumn::make('employee.name')->label(__('coupon.export.employee_name')),
            ExportColumn::make('sungardBranch.name')->label(__('coupon.export.sungard_branch_name')),

            ExportColumn::make('customer_name')->label(__('coupon.export.customer_name')),
            ExportColumn::make('customer_email')->label(__('coupon.export.customer_email')),
            ExportColumn::make('customer_phone')->label(__('coupon.export.customer_phone')),

            ExportColumn::make('coupon_link')->label(__('coupon.export.coupon_link')),
            ExportColumn::make('car_brand')->label(__('coupon.export.car_brand')),
            ExportColumn::make('car_type')->label(__('coupon.export.car_type')),
            ExportColumn::make('car_model')->label(__('coupon.export.car_model')),
            ExportColumn::make('car_category')->label(__('coupon.export.car_category')),
            ExportColumn::make('plate_number')->label(__('coupon.export.plate_number')),

            ExportColumn::make('is_confirmed')
                ->label(__('coupon.export.is_confirmed'))
                ->formatStateUsing(fn ($state) => $state ? __('Yes') : __('No')),

            ExportColumn::make('status')
                ->label(__('coupon.export.status'))
                ->formatStateUsing(fn ($state, $record) => $record->status?->label() ?? ''),

            ExportColumn::make('reserved_date')->label(__('coupon.export.reserved_date')),
            ExportColumn::make('reached_at')->label(__('coupon.export.reached_at')),
            ExportColumn::make('deleted_at')->label(__('coupon.export.deleted_at')),
            ExportColumn::make('created_at')->label(__('coupon.export.created_at')),
            ExportColumn::make('updated_at')->label(__('coupon.export.updated_at')),
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontName('Calibri')
            ->setFontColor('#ffffff')
            ->setBackgroundColor('#356854')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldShrinkToFit();
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(12)
            ->setFontName('Consolas');
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('coupon.export.notification.success_body', [
            'successful_rows' => Number::format($export->successful_rows),
            'row_plural' => str('row')->plural($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('coupon.export.notification.failed_body', [
                    'failed_rows' => Number::format($failedRowsCount),
                    'row_plural' => str('row')->plural($failedRowsCount),
                ]);
        }

        return $body;
    }
}
