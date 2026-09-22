<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use App\Nova\Resources\UserAndRoles\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Document extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Document>
     */
    public static $model = \App\Models\Document::class;

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
        'title',
        'description',
        'file_type',
    ];

    public static function label()
    {
        return '자료실';
    }

    public static function singularLabel()
    {
        return '자료';
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
            BelongsTo::make('등록자', 'user', User::class)
            ->readonly()->default(Auth::user()->id)
            ,
            Image::make('썸네일', 'thumbnail_path')
                ->path('documents'),
            Text::make('제목', 'title')->required()->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                $model->user_id = Auth::user()->id;
                $model->{$attribute} = $request->get($requestAttribute);
            }),
            Textarea::make('설명', 'description')->alwaysShow(),
            Text::make('파일 이름', 'file_name')->exceptOnForms(),
            Text::make('파일 유형', 'file_type')
                ->sortable()
                ->displayUsing(function ($value) {
                    $icons = [
                        'pdf' => '📄 PDF',
                        'doc' => '📝 DOC',
                        'docx' => '📝 DOCX',
                        'jpg' => '🖼️ JPG',
                        'png' => '🖼️ PNG',
                        // 기타 확장자에 대한 매핑
                    ];
                    return $icons[strtolower($value)] ?? strtoupper($value);
                })
                ->exceptOnForms(),
            File::make('파일', 'file_path')
                ->disk('public')
                ->path('documents')
                ->creationRules('required', 'mimes:pdf')
                ->updateRules('nullable')
                ->store(function (Request $request, $model) {
                    if ($request->hasFile('file_path')) {
                        $file = $request->file('file_path');
                        $path = $file->store('documents', 'public');

                        $model->file_name = $file->getClientOriginalName();
                        $model->file_path = $path;
                        $model->file_type = $file->getClientOriginalExtension();
                    }
                    return [];
                }),
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
