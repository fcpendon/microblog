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
     * @param array $data
     * @param string $category
     * @return string
     */
    public function storeExternal(array $data, string $category): string
    {
        $path = $category . '/' . $data['id'] . '.png';
        Storage::put($path, file_get_contents($data['image']));

        return $path;
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
