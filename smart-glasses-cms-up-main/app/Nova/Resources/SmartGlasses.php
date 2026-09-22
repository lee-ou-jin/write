<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class SmartGlasses extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SmartGlasses>
     */
    public static $model = \App\Models\SmartGlasses::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'model';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'model', 'serial_number'
    ];


//$table->uuid('id')->primary();
//$table->string('model')->nullable()->comment('모델명');
//$table->string('serial_number')->nullable()->comment('시리얼번호');
//$table->string('firmware_version')->nullable()->comment('펌웨어 버전');
//$table->string('os_version')->nullable()->comment('OS 버전');
//$table->enum('status', ['active', 'inactive'])->default('active')->comment('상태');
//$table->timestamps();

    public static function label(): string
    {
        return '스마트글래스 목록';
    }

    public static function singularLabel(): string
    {
        return '스마트글래스';
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
//            ID::make()->sortable(),
            Panel::make('스마트 글래스 앱 등록 정보', [

                Heading::make('스마트글래스 앱을 사용하기 위해서 소유자 명과 소유자 직책을 설정하세요.')
                    ->onlyOnDetail(),
                Heading::make('소유자 명, 제품 인증번호, 소유자 직책 정보는 앱에서 사용됩니다.')
                    ->onlyOnDetail(),

                Text::make('소유자 명', 'user_name')
                    ->placeholder('사용자 이름을 입력하세요.')
                    ->help('스마트글래스를 사용하는 사용자의 이름을 입력하세요.'),

                Text::make('소유자 직책', 'position')
                    ->placeholder('사용자 직책을 입력하세요.')
                    ->help('스마트글래스를 사용하는 사용자의 직책을 입력하세요.'),

                Text::make('제품 인증번호', 'auth_code')
                    ->onlyOnDetail()
                    ->help('스마트글래스 앱의 제품 인증을 위한 코드입니다.'),

                BelongsTo::make('소유 선박', 'ship', Ship::class)
                    ->sortable()
                    ->nullable()
                    ->exceptOnForms()
                    ->searchable(),
            ]),

            Text::make('모델명', 'model')->sortable()->required(),
            Text::make('시리얼번호', 'serial_number')->sortable()->required(),
            Text::make('펌웨어 버전', 'firmware_version')->sortable(),
            Text::make('OS 버전', 'os_version')->sortable(),
            Select::make('상태', 'status')->options([
                'active' => '활성',
                'inactive' => '비활성',
            ])->displayUsingLabels()->sortable(),

            DateTime::make('등록 시간', 'created_at')
                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
                ->exceptOnForms()
                ->filterable()
                ->sortable(),
//            DateTime::make('업데이트 날짜', 'updated_at')
//                ->displayUsing(fn($value) => $value ? $value->format('Y/m/d H:i:s') : '')
//                ->exceptOnForms()->filterable()
//                ->sortable(),

            Panel::make('관계 정보', [
                // ShipsCompany 선택 후 해당 선박 목록을 보여줌
                Select::make('선사', 'ships_company_id')
                    ->options(\App\Models\ShipsCompany::all()->pluck('name', 'id'))
                    ->searchable()
                    ->displayUsingLabels()
                    ->onlyOnForms(),

//                BelongsTo::make('Ship', 'ship', Ship::class)
//                    ->dependsOn(['ships_company_id'], function ($query, $values) {
//                        return $query->where('ships_company_id', $values['ships_company_id']);
//                    })
//                    ->searchable()
//                    ->rules('required'),

                Select::make('선박', 'ship_id')
                    ->dependsOn('ships_company_id', function (Select $field, NovaRequest $request, FormData $formData) {
                        $ships = \App\Models\Ship::query()
                            ->where('ships_company_id', $formData->get('ships_company_id'))
                            ->get()
                            ->pluck('name', 'id');

                        $field->options($ships);
                    })
                    ->searchable()
                    ->displayUsingLabels()
                    ->onlyOnForms(),
            ]),

            HasMany::make('Tasks', 'tasks', Task::class),

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
