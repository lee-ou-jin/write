<?php

namespace App\Nova\Resources\Logs;

use App\Nova\Resources;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class AuthenticationLog extends Resource
{
    public static $displayInNavigation = true;
    public static $group = 'Logins';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'authenticatable.name',
        'ip_address',
        'user_agent',
        'login_at',
        'login_successful',
        'logout_at',
        'cleared_by_user',
        'location',
    ];

    public static function label()
    {
        return __('Authentication Log');
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            MorphTo::make(__('Authenticatable'))
                ->types($this->authenticatableResources()),
            Text::make(__('Ip Address'))->sortable(),
            Text::make(__('User Agent'))->sortable(),
            DateTime::make(__('Login At'))->sortable(),
            Boolean::make(__('Login Successful'))->sortable(),
            DateTime::make(__('Logout At'))->sortable(),
            Boolean::make(__('Cleared By User'))->sortable(),
            KeyValue::make(__('Location'))->sortable(),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }

    public function authenticatableResources(): array
    {
        return [
            Resources\UserAndRoles\User::class
        ];
    }
}
