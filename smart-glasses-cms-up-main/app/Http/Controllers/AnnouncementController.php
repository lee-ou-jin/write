<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AnnouncementController extends Controller
{
    function index(Request $request, $slug = null): InertiaResponse
    {
        $categories = Category::query()
            ->whereHas('announcements')
            ->withCount('announcements')
            ->get();
        $categories = collect([
            (new Category([
                'id' => '',
                'name' => '전체'
            ]))->setAttribute('announcements_count',Announcement::query()->count()),
            ...$categories
        ]);

        $announcementQuery = Announcement::query()
            ->where('publish_at','<=',Carbon::now())
            ->orderBy('sort_order','DESC');

        $searchKeyword = $request->get('search_keyword','');
        if($searchKeyword !== '' && $searchKeyword !== null){
            $announcementQuery
                ->where(function(Builder $whereGroupQuery) use ($searchKeyword){
                    $searchKeyword = explode(" ",$searchKeyword);
                    foreach($searchKeyword as $keyword){
                        $whereGroupQuery->orWhere('title','like',"%".$keyword."%");
                        $whereGroupQuery->orWhere('content','like',"%".$keyword."%");
                    }
                });
        }

        $categoryId = $request->get('category_id','');
        if($categoryId !== '' && $categoryId != null){
            $categories->where('id',$categoryId)->first->setAttribute('current',true);
            $announcementQuery
                ->whereHas('categories',function($query) use($categoryId){
                $query->where('id',$categoryId);
            });
        }else{
            $categories->first->setAttribute('current',true);
        }

        $announcements = $announcementQuery->paginate();

        if($slug !== null){
            $announcement = Announcement::findBySlug($slug);
            if($announcement == null) unset($announcement);
        }

        return Inertia::render('Announcement/List',[
            'announcement' => $announcement ?? null,
            'categories' => $categories,
            'announcements' => $announcements,
            'searchKeyword' => $searchKeyword,
        ]);
    }
}
