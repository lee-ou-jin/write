<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaLibraryPathGenerator extends  DefaultPathGenerator{

    protected function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');

        $modelName = Str::kebab(class_basename($media->getAttribute('model_type')));

        if ($prefix !== '') {
            return $prefix.'/'.$modelName.'/'.$media->getKey();
        }

        return $modelName.'/'.$media->getKey();
    }
}
