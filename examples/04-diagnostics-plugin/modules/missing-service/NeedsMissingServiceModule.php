<?php

declare(strict_types=1);

namespace easylibraryexamples\diagnostics\modules\missingservice;

use imperazim\module\BaseModule;
use imperazim\module\ModuleManager;

final class NeedsMissingServiceModule extends BaseModule {

    protected function onEnable(ModuleManager $manager): void {
        $this->logger()->warning('This should not enable because examples:not-installed-service is missing.');
    }
}
