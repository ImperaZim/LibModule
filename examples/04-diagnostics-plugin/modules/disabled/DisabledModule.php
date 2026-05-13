<?php

declare(strict_types=1);

namespace easylibraryexamples\diagnostics\modules\disabled;

use imperazim\module\BaseModule;
use imperazim\module\ModuleManager;

final class DisabledModule extends BaseModule {

    protected function onEnable(ModuleManager $manager): void {
        $this->logger()->warning('This should not enable because enabled=false in module.yml.');
    }
}
