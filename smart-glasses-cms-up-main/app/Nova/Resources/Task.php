<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ActionFields;
use App\Nova\Actions\TaskCompleteAction;
use App\Nova\Actions\TaskProgressAction;
use App\Nova\Resources\UserAndRoles\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Nova\Fields\HasOne;

class Task extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Task>
     */
    public static $model = \App\Models\Task::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'code';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'code',
        'equip_name',
        'status'
    ];

    public static function label()
    {
        return '온라인 작업 목록';
    }

    public static function singularLabel()
    {
        return '온라인 작업';
    }

    //    public static function authorizedToCreate(Request $request)
    //    {
    //    }

    public function authorizedToUpdate(Request $request) {}

    //    public function authorizedToDelete(Request $request) {}

    public static function indexQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::indexQuery($request, $query)->where('mode', 'online');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Code', 'code')->sortable()->exceptOnForms(),

            BelongsTo::make('Equipment', 'equipment', Equipment::class)
                ->displayUsing(function ($value) {
                    if ($value) {
                        return $value->ship->name . ' - ' . $value->name . ' (' . $value->type . ')';
                    }
                    return null;
                }),
            BelongsTo::make('Smart Glasses', 'smartGlasses', SmartGlasses::class),


            // Text::make('Equipment Name', 'equip_name')
            //     ->required()
            //     ->sortable(),

            Text::make('Title', 'title')->required(),

            Textarea::make('Description', 'description')->alwaysShow(),

            Select::make('status', 'status')->options([
                'progress' => 'progress',
                'complete' => 'completed',
            ])->sortable()->exceptOnForms()->default('progress'),

            DateTime::make('Completed At', 'completed_at')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),

            DateTime::make('Created At', 'created_at')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),

            HasOne::make('Online Meeting Info', 'meeting', Meeting::class),

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
        return [
            //            Action::using('작업 완료 처리', function (ActionFields $fields, $models) use ($request) {
            //                $models->each->update(['status' => 'complete', 'completed_at' => now()]);
            //            })->canSee(function ($request) {
            //                return !$this->resource->exists || $this->status == 'progress';
            //            }),
            //            Action::using('작업 진행 처리', function (ActionFields $fields, $models) use ($request) {
            //                $models->each->update(['status' => 'progress', 'completed_at' => null]);
            //            })->canSee(function ($request) {
            //                return !$this->resource->exists || $this->status == 'complete';
            //            }),];

            // TaskCompleteAction::make(),
            // TaskProgressAction::make(),
        ];
    }
}
