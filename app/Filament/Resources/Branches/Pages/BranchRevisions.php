<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Mansoor\FilamentVersionable\RevisionsPage;
class BranchRevisions extends RevisionsPage
{
    protected static string $resource = BranchResource::class;

}
