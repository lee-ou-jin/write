<?php

namespace App\Settings;

use AmuzPackages\VimeoField\Nova\Resources\VimeoVideo;
use App\Nova\Dashboards\Main;
use App\Nova\Resources\Contents\Announcement;
use App\Nova\Resources\Contents\Category;
use App\Nova\Resources\Contents\Tag;
use App\Nova\Resources\Document;
use App\Nova\Resources\Equipment;
use App\Nova\Resources\EquipmentCompany;
use App\Nova\Resources\Logs\AuthenticationLog;
use App\Nova\Resources\Media\Audio;
use App\Nova\Resources\Media\Image;
use App\Nova\Resources\Media\Media;
use App\Nova\Resources\Media\Video;
use App\Nova\Resources\Meeting;
use App\Nova\Resources\OfflineTask;
use App\Nova\Resources\Ship;
use App\Nova\Resources\ShipsCompany;
use App\Nova\Resources\SmartGlasses;
use App\Nova\Resources\Task;
use App\Nova\Resources\UserAndRoles\Permission;
use App\Nova\Resources\UserAndRoles\Role;
use App\Nova\Resources\UserAndRoles\Team;
use Bolechen\NovaActivitylog\Resources\Activitylog;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;

class NovaMenu
{
    public function registerNovaMenu(Request $request, Menu $menu): Menu
    {
        $menu->items = [];
        $menu->append([
            MenuSection::dashboard(Main::class)->icon('chart-bar'),

            //            MenuSection::make(__('Media'),[
            //                MenuGroup::make(__('Library'),[
            //                    MenuItem::resource(Media::class)
            //                ]),
            //                MenuGroup::make(__('CK Editor'),[
            //                    MenuItem::resource(Image::class),
            //                    MenuItem::resource(Video::class),
            //                    MenuItem::resource(Audio::class)
            //                ]),
            //            ])->icon('photograph')->collapsable(),
            //            MenuSection::make(__('Contents'),[
            //                MenuGroup::make(__('Taxonomy'),[
            //                    MenuItem::resource(Category::class),
            //                    MenuItem::resource(Tag::class),
            //                ]),
            //                MenuGroup::make(__('Documents'),[
            //                    MenuItem::resource(Announcement::class)
            //                ]),
            //            ])->collapsable(),
            MenuSection::make(__('User & Roles'), [
                MenuGroup::make(__('Manage Users'), [
                    MenuItem::resource(\App\Nova\Resources\UserAndRoles\User::class),
                    //                    MenuItem::resource(Team::class),
                ]),
                //                MenuGroup::make(__('Roles & Permissions'),[
                //                    MenuItem::resource(Role::class),
                //                    MenuItem::resource(Permission::class),
                //                ]),
            ])->icon('user')->collapsable(),

            //            MenuSection::make(__('Logs'),[
            //                MenuItem::resource(AuthenticationLog::class),
            //                MenuItem::resource(Activitylog::class)
            //            ])->canSee(function($request){
            //                return $request->user()->isSuperAdmin();
            //            })->icon('document-duplicate')->collapsedByDefault(),

            MenuSection::make('스마트글래스 관리', [
                MenuItem::resource(SmartGlasses::class),
            ]),

            MenuSection::make('선사 관리', [
                MenuItem::resource(ShipsCompany::class),
            ]),

            MenuSection::make('선박 관리', [
                MenuItem::resource(Ship::class),
            ]),

            MenuSection::make('업체 관리', [
                MenuItem::resource(EquipmentCompany::class),
            ]),

            MenuSection::make('장비 관리', [
                MenuItem::resource(Equipment::class),
            ]),

            MenuSection::make('작업 관리', [
                // MenuItem::resource(Meeting::class),
                MenuItem::resource(Task::class),
                MenuItem::resource(OfflineTask::class),
            ]),

            MenuSection::make('자료실', [
                MenuItem::resource(Document::class),
                MenuItem::resource(VimeoVideo::class)
            ]),
        ]);

        return $menu;
    }
}
