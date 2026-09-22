<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Exceptions\CantOpenFileFromUrlException;

class UrlUploadedFile extends UploadedFile
{
    /**
     * @throws CantOpenFileFromUrlException
     */
    public static function createFromUrl(string $url, string $originalName = '', string $mimeType = null, int $error = null, bool $test = false): self
    {
        if (! $stream = @fopen($url, 'r')) {
            throw new CantOpenFileFromUrlException($url);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'url-file-');

        file_put_contents($tempFile, $stream);

        return new static($tempFile, $originalName, $mimeType, $error, $test);
    }


    public static function createFromPath($path): UploadedFile{

        $path_parts = pathinfo($path);

        $newPath = $path_parts['dirname'] . '/tmp-files/';
        if(!is_dir ($newPath)){
            mkdir($newPath, 0777);
        }

        $newUrl = $newPath . $path_parts['basename'];
        copy($path, $newUrl);
        $imgInfo = getimagesize($newUrl);

        return new UploadedFile(
            $newUrl,
            $path_parts['basename'],
            $imgInfo['mime'],
            filesize($path),
            true,
            TRUE
        );
    }
}
