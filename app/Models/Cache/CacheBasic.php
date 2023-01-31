<?php

namespace App\Models\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheBasic
{
    public function __construct()
    {
    }

    public function getFilesystemAdapterCache(): FilesystemAdapter
    {
        return new FilesystemAdapter();
    }
}
