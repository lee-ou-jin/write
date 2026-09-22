<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use App\Nova\Resources\UserAndRoles\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use InteractionDesignFoundation\HtmlCard\HtmlCard;
use Jerry\ChatCard\ChatCard;
use Jerry\LiveCard\LiveCard;
use Jerry\TaskLogModal\TaskLogModal;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Meeting extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Task>
     */
    public static $model = \App\Models\Meeting::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'title'
    ];

    public static function label()
    {
        return '작업 지원 목록(화상)';
    }

    public static function singularLabel()
    {
        return '작업 지원(화상)';
    }

    public static function indexQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::indexQuery($request, $query);
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

            Text::make('회의 제목', 'title')->sortable()->required(),

            Text::make('회의 내용', 'content')->sortable()->required(),

            Text::make('녹화 파일 경로', 'recordings_path')->exceptOnForms(),

            DateTime::make('회의 생성일', 'start_time')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),

            DateTime::make('회의 종료일', 'end_time')
                ->exceptOnForms()
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i') : ''),

            BelongsToMany::make('참석자', 'users', User::class),

            BelongsTo::make('작업', 'task', Task::class)
                ->exceptOnForms(),

            //            HasMany::make('채팅', 'messages', Message::class),

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
        return [
            // ChatCard::make('Chat Card')->resourceId($request->resourceId)->width('full')->onlyOnDetail(),
            LiveCard::make('Live Meet')->resourceId($request->resourceId)->width('full')->onlyOnDetail(),
            TaskLogModal::make('Task Log Modal')->resourceId($request->resourceId)->onlyOnDetail(),
            //            new TaskLogModal($this->resource->task_id),
            //            (new HtmlCard())->width('full')
            //                ->html('
            //            <div style="text-align: center; padding: 20px;">
            //                <h3 style="margin-bottom: 15px;">스마트글래스 수신 화면</h3>
            //                <img src="https://lh5.googleusercontent.com/proxy/XI441zH-IKdLdGIYIs782RHlEdEAWHWyK8tsvSBforeMteZJrzXGQY79B3q14wXYPiGkwHDPZN-1MiH_A-EOIoLjWVnVjUTRYwv_5w"
            //                     alt="Ship Interior"
            //                     style="display: block; margin: 0 auto; height: 600px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            //            </div>
            //        ')
            //                ->onlyOnDetail(),

        ];
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
