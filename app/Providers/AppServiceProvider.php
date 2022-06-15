<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $parseRelation = function ($relation) {
        $methodName = 'get' . Str::studly($relation) . 'Relation';

        if ($this instanceof Collection) {
            if ($this->count() === 0) {
                return [];
            }
        } else if ($this->resource === null) {
            return [];
        }

        if ($this instanceof Collection) {
            $related = $this->map->$methodName();

            if ($related->count() > 0 && $related[0] instanceof AnonymousResourceCollection) {
                $related = $related->flatMap;
            }
            
            if ($related->count() === 0) {
                return [];
            }

            return $related->filter(function ($item) {
                return $item->resource;
            })->unique('id')->keyBy('id');
        } else {
            $related = $this->$methodName();

            if ($related instanceof AnonymousResourceCollection || $related instanceof Collection) {
                return $related->keyBy('id');
            }

            if ($related->resource === null) {
                return [];
            }

            return [$related->id => $related];
        }

      };

      Collection::macro('parseRelation', $parseRelation);
      BaseResource::macro('parseRelation', $parseRelation);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultstringLength(191);
    }
}
