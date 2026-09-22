<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Equipment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Equipment>
     */
    public static $model = \App\Models\Equipment::class;

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
        'id',
        'name',
        'type',
        'serial_number',
    ];

    public static function label(): string
    {
        return '장비 목록';
    }

    public static function singularLabel(): string
    {
        return '장비';
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

            BelongsTo::make('소속 선박', 'ship', Ship::class),

            Text::make('장비명', 'name')->sortable()->required(),
            // Text::make('모델명', 'model')->sortable(),
            Text::make('시리얼 번호', 'serial_number')->sortable(),
            Text::make('장비 타입', 'type')->sortable(),

            DateTime::make('등록 시간', 'created_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->onlyOnDetail()->filterable()
                ->exceptOnForms()
                ->sortable(),
            DateTime::make('수정일', 'updated_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->onlyOnDetail()->filterable()
                ->exceptOnForms()
                ->sortable(),


            HasMany::make('작업', 'tasks', Task::class)->onlyOnDetail(),
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
