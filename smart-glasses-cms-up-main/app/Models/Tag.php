<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\Tag as SpatieTagModel;

/**
 * use HasTags; trait 가 사용된 모델을 추가해준다.
 */
class Tag extends SpatieTagModel
{
    /**
     * @return MorphToMany
     */
    public function announcements(): MorphToMany
    {
        return $this->morphedByMany(Announcement::class,'taggable');
    }
}
