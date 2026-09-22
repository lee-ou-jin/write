<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($document) {
            // 파일 삭제
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            // 썸네일 삭제
            if ($document->thumbnail_path) {
                Storage::disk('public')->delete($document->thumbnail_path);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
