<?php

namespace App\Faker;


use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;

final class FakerImageProvider extends Base
{
    public function loremflickr(string $dir = '', int $width = 500, int $height = 500)
    {
        $name = $dir . '/' . str()->random(40) . '.webp';

        Storage::disk('public')->put(
            $name,
            file_get_contents("https://loremflickr.com/$width/$height")
        );

        return '/storage/' . $name;
    }
}
