<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    public function store(UploadedFile $image, string $category): string
    {
        return $image->store($category);
    }

    public function delete(string $path): void
    {
        Storage::delete($path);
    }
}
