<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src'
    ]);
    
    (new \IfCastle\CodeStyle\Rector\RectorConfigurator())->configureSets($rectorConfig);
};