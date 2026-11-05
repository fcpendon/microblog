<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    /**
     * @param UploadedFile $image
     * @param string $category
     * @return string
     */
    public function store(UploadedFile $image, string $category): string
    {
        return $image->store($category);
    }

    /**
     * @param string $path
     * @return void
     */
    public function delete(string $path)
    {
        Storage::delete($path);
    }
}
