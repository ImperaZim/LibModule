<?php

declare(strict_types=1);

namespace easylibraryexamples\diagnostics\modules\missingdependency;

use imperazim\module\BaseModule;
use imperazim\module\ModuleManager;

final class NeedsMissingModule extends BaseModule {

    protected function onEnable(ModuleManager $manager): void {
        $this->logger()->warning('This should not enable because examples:not-installed is missing.');
    }
}
