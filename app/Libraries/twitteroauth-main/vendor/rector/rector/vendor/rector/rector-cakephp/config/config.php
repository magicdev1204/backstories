<?php

declare (strict_types=1);
namespace RectorPrefix202209;

use Rector\Config\RectorConfig;
return static function (RectorConfig $rectorConfig) : void {
    $services = $rectorConfig->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Rector\\CakePHP\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/{Rector,ValueObject,Contract}']);
};
