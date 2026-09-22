<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Rinvex\Categories\Models\Category as Taxonomy;
class Category extends Taxonomy
{
    protected $appends = [
        'tree_name'
    ];

    public function getTreeNameAttribute(){
        if($this->parent == null) return $this->getAttribute('name');
        return $this->parent->getAttribute('tree_name') . " > " .$this->getAttribute('name');
    }

    public function announcements(): MorphToMany
    {
        return $this->entries(Announcement::class);
    }
}
