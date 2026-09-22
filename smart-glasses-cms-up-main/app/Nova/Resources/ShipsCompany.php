<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use App\Nova\Resources\UserAndRoles\User;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ShipsCompany extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ShipsCompany>
     */
    public static $model = \App\Models\ShipsCompany::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'address', 'phone'
    ];

    public static function label(): string
    {
        return '선사 목록';
    }

    public static function singularLabel(): string
    {
        return '선사';
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
//            ID::make()->sortable(),
            Text::make('Name', 'name')->sortable(),
            Text::make('Address', 'address')->sortable(),
            Text::make('Phone', 'phone')->sortable(),

            MorphMany::make('사용자', 'users', User::class),

            DateTime::make('Created At', 'created_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->exceptOnForms()->filterable()
                ->sortable(),
            DateTime::make('Updated At', 'updated_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->exceptOnForms()->filterable()
                ->sortable(),

            HasMany::make('선박 목록', 'ships', Ship::class),
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
