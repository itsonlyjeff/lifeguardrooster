<?php

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Instellingen';
    protected static ?string $navigationLabel = 'Instellingen';
    protected static ?string $clusterBreadcrumb = 'Instellingen';

}
