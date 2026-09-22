<?php

namespace App\Nova\Resources;

use AmuzPackages\VimeoField\Nova\Fields\VimeoVideoField;
use AmuzPackages\VimeoField\Nova\Resources\VimeoVideo;
use App\Nova\Actions\TaskCompleteAction;
use App\Nova\Actions\TaskProgressAction;
use App\Nova\Resource;
use App\Nova\Resources\UserAndRoles\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class OfflineTask extends Resource
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
        'id', 'code', 'equip_name', 'status'
    ];

    public static function label()
    {
        return '오프라인 작업 목록';
    }

    public static function singularLabel()
    {
        return '오프라인 작업';
    }

    public static function indexQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::indexQuery($request, $query)->where('mode', 'offline');
    }

    public static function authorizedToCreate(Request $request)
    {
    }

    public function authorizedToUpdate(Request $request)
    {
    }

    public function authorizedToDelete(Request $request)
    {
        //추후삭제 gani
        return true;
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
            ID::make()->sortable()->fullWidth()->hide(),

            Text::make('작업 코드', 'code')->sortable()->exceptOnForms()->fullWidth(),

            BelongsTo::make('장비', 'equipment', Equipment::class)
                ->searchable()
                ->required()
                ->fullWidth()
                ->displayUsing(function ($value) {
                    if (!$value) {
                        return null;
                    }

                    $shipName = optional($value->ship)->name;
                    return ($shipName ? $shipName . ' - ' : '') . $value->name . ' (' . $value->type . ')';
                })
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $equipmentId = $request->input($requestAttribute);
                    $model->equipment_id = $equipmentId;

                    if ($equipmentId) {
                        $equipment = \App\Models\Equipment::query()->find($equipmentId);
                        $model->equip_name = $equipment?->name;
                    }
                })
                ->onlyOnForms(),

            Text::make('장비', function () {
                $equipment = $this->equipment;
                if (!$equipment && $this->equip_name) {
                    $equipment = \App\Models\Equipment::query()
                        ->with('ship:id,name')
                        ->where('name', $this->equip_name)
                        ->first();
                }

                if (!$equipment) {
                    return e($this->equip_name ?: '—');
                }

                $shipName = optional($equipment->ship)->name;
                $label = ($shipName ? $shipName . ' - ' : '') . $equipment->name . ' (' . $equipment->type . ')';
                $url = '/settings/resources/' . \App\Nova\Resources\Equipment::uriKey() . '/' . $equipment->getKey();

                return '<a class="link-default" href="' . e($url) . '">' . e($label) . '</a>';
            })
                ->asHtml()
                ->exceptOnForms(),

            Text::make('장비명', 'equip_name')
                ->sortable()
                ->exceptOnForms()
                ->fullWidth(),

            Textarea::make('작업 내용', 'description')->alwaysShow()->fullWidth(),

            Select::make('상태', 'status')->options([
                'progress' => 'progress',
                'complete' => 'complete',
            ])->sortable(),

            DateTime::make('작업 생성일', 'created_at')
//                ->size('w-1/2')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),

            DateTime::make('작업 완료일', 'completed_at')
//                ->size('w-1/2')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),

            Hidden::make('모드', 'mode')->default('offline')->fullWidth(),

//            HasMany::make('미팅', 'meetings', Meeting::class),

            VimeoVideoField::make('비디오', 'vimeoVideo')
                ->hideFromDetail()
                ->fullWidth(),

            Text::make('비디오', function () {
                $vimeoId = optional($this->vimeoVideo)->embed_url;
                if (!$vimeoId) {
                    return '—';
                }
                return '<iframe src="' . $vimeoId . '"
            width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
            })
                ->onlyOnDetail()
                ->asHtml(),

            BelongsTo::make('Target Smart Glasses', 'smartGlasses', SmartGlasses::class)->fullWidth(),

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
//            }),
            TaskCompleteAction::make()
            ->canSee(function ($request) {
                return !$this->resource->exists || $this->status == 'progress';
            }),

            TaskProgressAction::make()
            ->canSee(function ($request) {
                return !$this->resource->exists || $this->status == 'complete';
            }),
        ];
    }
}
