<?php

namespace App\Nova\Resources\Media;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Media extends Resource
{
    public static string $model = \Spatie\MediaLibrary\MediaCollections\Models\Media::class;

    public function title(): string
    {
        return sprintf("(%s)%s",$this->resource->collection,$this->resource->file_name);
    }

    public static function label(): string
    {
        return __('Media Library');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','collection_name','name','file_name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Thumbnail'),'original_url')
                ->displayUsing(function(){
                    return sprintf("<img src='%s' class='h-20 w-auto mx-auto max-w-xs' />",$this->resource->getUrl());
                })->asHtml()
            ->hideWhenCreating()
            ->hideWhenUpdating(),

            Text::make(__('Disk'),'disk')->filterable(),
            Text::make(__('Collection'),'collection_name')->filterable(),
            Text::make(__('FileName'),'file_name')->sortable(),
            Text::make(__('Size'),'size')->sortable(),

            MorphTo::make(__('Target Model'),'model')->filterable()->sortable()
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
