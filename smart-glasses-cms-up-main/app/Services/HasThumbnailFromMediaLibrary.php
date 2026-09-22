<?php
namespace App\Services;

/**
 * @brief
 * 미디어라이브러리에서 썸네일 뽑는걸 도와준다.
 * 사전에 모델에서 컬렉션에 관련된 정보를 등록해두어야한다.
 */
trait HasThumbnailFromMediaLibrary{

    public string $defaultTitleKey = 'title';

    private function getThumbnailUrl($collectionName = 'default'): string
    {
        $firstImageUrl = $this->getFirstMediaUrl($collectionName);
        if($firstImageUrl == '') $firstImageUrl = null;
        return ($firstImageUrl !== null) ? $firstImageUrl : $this->makeDefaultImage();
    }

    protected function makeDefaultImage(): string
    {
        $name = trim(collect(explode(' ', $this->getAttribute($this->defaultTitleKey)))
            ->map(function ($segment) {
                    return mb_substr($segment, 0, 1);
                })
            ->join(' '));
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
}
