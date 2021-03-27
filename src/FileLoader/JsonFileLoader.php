<?php

namespace SoureCode\ConventionalCommits\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;

class JsonFileLoader extends FileLoader
{
    public function load($resource, string $type = null)
    {
        $contents = file_get_contents($resource);
        $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    public function supports($resource, string $type = null)
    {
        return is_string($resource) && 'json' === pathinfo(
                $resource,
                PATHINFO_EXTENSION
            );
    }
}
