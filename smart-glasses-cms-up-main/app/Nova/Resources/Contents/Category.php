<?php

namespace App\Nova\Resources\Contents;

use App\Nova\Resource;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Category extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Category>
     */
    public static string $model = \App\Models\Category::class;

    public function title(): string
    {
        /** @var \App\Models\Category $category */
        $category = $this->resource;
        $title = $category->getAttribute('name');
        $first = $category->parent?->getAttribute('parent_id') == null ? '' : ' - ';
        $second = $category->getAttribute('parent_id') == null ? '' : ' - ';
        return sprintf("%s%s%s",$first,$second, $title);
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','name'
    ];

    public static function label()
    {
        return __('Categories');
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q
                ->orderBy('name','ASC')
                ->orderBy('_lft','ASC')
                ->orderBy('_rgt','ASC');
        });

        return $query;
    }

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
            BelongsTo::make(__('Parent Item'),'parent',Category::class)
                ->hideFromIndex()
                ->nullable(),
            Text::make(__('Title'),'name')
                ->fullWidth()
                ->hideFromIndex()
                ->translatable()
                ->required(),

            Text::make(__('Title'),'name')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail()
                ->displayUsing(function($title){
                    $first = $this->resource->parent?->getAttribute('parent_id') == null ? '' : ' - ';
                    $second = $this->resource->getAttribute('parent_id') == null ? '' : ' - ';
                    return sprintf("%s%s%s",$first,$second, $title);
                }),
            TextArea::make(__('Description'),'description'),
            HasMany::make(__('Children Item'),'children',Category::class)
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
