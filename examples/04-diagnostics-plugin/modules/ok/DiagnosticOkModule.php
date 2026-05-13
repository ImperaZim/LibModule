<?php

declare(strict_types=1);

namespace easylibraryexamples\diagnostics\modules\ok;

use easylibraryexamples\diagnostics\modules\ok\command\DiagnosticCommand;
use imperazim\module\BaseModule;
use imperazim\module\health\ModuleHealthReport;
use imperazim\module\ModuleManager;

final class DiagnosticOkModule extends BaseModule {

    protected function onEnable(ModuleManager $manager): void {
        $this->addCommand(DiagnosticCommand::class);
        $this->logger()->info('Diagnostic OK module enabled.');
    }

    public function getHealth(): ModuleHealthReport {
        return ModuleHealthReport::ok(['purpose' => 'This module should always enable successfully.']);
    }
}
