<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class QueryOptimizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->isLocal()) {

            // Active la détection N+1
            Model::preventLazyLoading();

            // Handler simple et compatible
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                Log::warning('N+1 détecté sur ' . get_class($model) . ' relation: ' . $relation);
            });
        }
    }
}