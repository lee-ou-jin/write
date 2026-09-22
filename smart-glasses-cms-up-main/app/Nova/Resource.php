<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;


abstract class Resource extends NovaResource
{
    public static function indexQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return $query;
    }

    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::detailQuery($request, $query);
    }

    public static function relatableQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::relatableQuery($request, $query);
    }
}
