<?php

namespace App\Nova\Resources\UserAndRoles;

use App\Nova\Resource;
use App\Nova\Resources\EquipmentCompany;
use App\Nova\Resources\ShipsCompany;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static string $model = \App\Models\User::class;
    public static $displayInNavigation = true;

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
        'id', 'name', 'email',
    ];

    public static function label(){
        return '관리자 및 사용자';
    }

    public static function singularLabel(){
        return '관리자 및 사용자';
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
            Id::make()->hide(),


            Text::make('구분', function () {
                $role = $this->role;
                // 관리자의 경우 빨간색 텍스트, 선사의 경우 파란색 텍스트, 업체의 경우 초록색 텍스트
                if($role == '관리자') {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$role}</span>";
                } elseif($role == '선사') {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800'>{$role}</span>";
                } elseif($role == '업체') {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$role}</span>";
                }
            })
                ->textAlign('center')
                ->asHtml()
                ->exceptOnForms(),
//            Image::make(__('Profile Photo'),'profile_photo_path')->maxWidth(50)->path('profile-photos'),

            Text::make(__('Name'),'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Email'),'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make(__('Password'),"password")
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),

            MorphTo::make('관계', 'roleable')
                ->types([
                    ShipsCompany::class,
                    EquipmentCompany::class,
                ])
                ->help('관계을 선택하지 않을 경우, 관리자로 설정됩니다.')
//                ->hideFromIndex()
                ->nullable(),



//            MorphToMany::make('Roles', 'roles', \Sereny\NovaPermissions\Nova\Role::class),
//            MorphToMany::make('Permissions', 'permissions', \Sereny\NovaPermissions\Nova\Permission::class),
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
