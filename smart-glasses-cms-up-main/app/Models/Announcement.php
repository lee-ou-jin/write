<?php

namespace App\Models;

use App\Services\HasCategories;
use App\Services\HasThumbnailFromMediaLibrary;
use App\Services\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Announcement extends Model implements HasMedia, Sortable
{
    use SortableTrait;
    use InteractsWithMedia;
    use HasFactory;
    use HasTags;
    use HasThumbnailFromMediaLibrary;
    use LogsActivity;
    use HasTranslations, HasTranslatableSlug;
    use HasCategories;

    public array $translatable = [
        'title',
        'slug'
    ];

    public array $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'gallery' => 'array'
    ];

    protected $appends = [
        'published',
        'thumbnail',
        'gallery',
        'href',
        'summary'
    ];

    protected $with = [
        'user',
    ];

    public function getPublishedAttribute(){
        return $this->getAttribute('publish_at')->diffForHumans();
    }

    public function getThumbnailAttribute(): string
    {
        return $this->getThumbnailUrl('thumbnail');
    }

    public function getGalleryAttribute(): array
    {
        return [];
    }
    public function getSummaryAttribute(): string
    {
        return Str::limit(html_entity_decode(strip_tags($this->getAttribute('content')),50));
    }

    public function getHrefAttribute(): string
    {
        return route('announcement.index',[
            'slug' => $this->getAttribute('slug')
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = new LogOptions();
        $logOptions->logAttributes = ['*'];
        $logOptions->logOnlyDirty = true;
        return $logOptions;
    }

    public function getSlugOptions() : SlugOptions
    {
        $locales = config('nova-language-switch.supported-languages');

        $slugOptions = new SlugOptions();
        $slugOptions->generateUniqueSlugs = true;

        return $slugOptions::createWithLocales(array_keys($locales))
            ->generateSlugsFrom(function($model, $locale) {
//                return "{$locale} {$model->title}";
                return "{$model->title}";
            })
            ->usingLanguage(app()->getLocale())
//            ->doNotGenerateSlugsOnUpdate()
            ->saveSlugsTo('slug')
            ->usingSeparator('-')
            ->slugsShouldBeNoLongerThan(50);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
