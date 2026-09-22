<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use App\Nova\Resources\UserAndRoles\User;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class EquipmentCompany extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EquipmentCompany>
     */
    public static $model = \App\Models\EquipmentCompany::class;

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
        return '업체 목록';
    }

    public static function singularLabel(): string
    {
        return '업체';
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
            Text::make('업체명', 'name')->sortable(),
            Text::make('주소', 'address')->sortable(),
            Text::make('전화번호', 'phone')->sortable(),

//            Text::make('업체 타입', 'type')->sortable()->exceptOnForms(),

            Select::make('업체 타입', 'type')
                ->options([
                    '장비업체' => '장비업체',
                    '관리업체' => '관리업체',
                ])
                ->displayUsingLabels()->sortable(),


            MorphMany::make('운영자', 'users', User::class),

            DateTime::make('등록 시간', 'created_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->exceptOnForms()->filterable()
                ->sortable(),
            DateTime::make('수정일', 'updated_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->onlyOnDetail()->filterable()
                ->sortable(),
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
