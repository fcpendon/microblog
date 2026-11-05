<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageService
{
    /**
     * @param $image
     * @param string $category
     * @return string
     */
    public function store($image, string $category): string
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
