<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Ship extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Ship>
     */
    public static $model = \App\Models\Ship::class;

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
        'imo_number'
    ];

    public static function label(): string
    {
        return '선박 목록';
    }

    public static function singularLabel(): string
    {
        return '선박';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('선박명', 'name'),
            Select::make('선박 종류', 'type')
                ->options([
                    0 => '기타',
                    1 => '화물선',
                    2 => '여객선',
                    3 => '유조선'
                ])
//                ->textAlign('center')
                ->displayUsingLabels(),
            Text::make('IMO', 'imo_number'),
            Select::make('상태', 'status')
                ->options([
                    0 => '기타',
                    1 => '운항중',
                    2 => '정박중',
                    3 => '수리중'
                ])
//                ->textAlign('center')
                ->displayUsingLabels(),
            BelongsTo::make('운영사', 'shipsCompany', ShipsCompany::class),
            DateTime::make('등록 시간', 'created_at')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
