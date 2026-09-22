<?php

namespace App\Nova\Resources\Contents;

use App\Nova\Resource;
use App\Nova\Resources\UserAndRoles\User;
use App\Services\SortsIndexEntries;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Mahi\SpatieTagsNovaFilter\SpatieTagsNovaFilter;
use Mostafaznv\NovaCkEditor\CkEditor;
use PixelCreation\NovaFieldSortable\Sortable;
use Spatie\TagsField\Tags;
use ZiffMedia\NovaSelectPlus\SelectPlus;

class Announcement extends Resource
{
    use SortsIndexEntries;
    public static string $defaultSortField = 'sort_order';
    public static string $defaultSortDirection = 'DESC';

    public static string $model = \App\Models\Announcement::class;

    public static $title = 'title';

    public static $search = [
        'title','content',
    ];

    public static function label(): string
    {
        return __('Announcement');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Sortable::make(__('Priority'),'sort_order')
                ->onlyOnIndex(),

            SelectPlus::make(__('Category'),'categories',Category::class)
                ->label('tree_name')
                ->usingIndexLabel('name'),

            Text::make(__('Title'),'title')
                ->fullWidth()
                ->hideFromIndex()
                ->translatable()
                ->required(),

            Text::make(__('Title'),'title')
                ->displayUsing(function ($value) {
                    return Str::limit($value, 40);
                })
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail(),

            Slug::make(__('Slug'),'slug')
                ->hideFromIndex()
                ->hideFromDetail()
                ->placeholder(__('If you leave this field blank, it will be created automatically.'))
                ->from('title')
                ->translatable()
                ->separator('-'),

            Panel::make(__('Body'),[
                CkEditor::make(__('Content'), 'content')
                    ->hideFromIndex()
                    ->fullWidth()->stacked(),

                Images::make(__('Thumbnail'), 'thumbnail')
                    ->conversionOnIndexView('thumb')
                    ->size('w-1/4'),

                Images::make(__('Gallery'), 'gallery')
                    ->hideFromIndex()
                    ->conversionOnIndexView('thumb')
                    ->size('w-3/4'),

                Tags::make(__('Tags'),'tags')
                    ->size('w-full')
                    ->hideFromIndex()
                    ->stacked(),
            ]),
            Panel::make(__('Author and publication schedule'),[
                BelongsTo::make(__('Author'), "user", User::class)
                    ->size('w-1/3')
                    ->default(Auth::user()->getKey()),

                DateTime::make(__('Publish At'), "publish_at")
                    ->size('w-2/3')
                    ->default(Carbon::now()),
            ])
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
        return [
            (new SpatieTagsNovaFilter)
                ->label(__('Tags'))
                ->withMeta([
                    'withAnyTags' => false,
                    'tag_type' => null
                ])
        ];
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
