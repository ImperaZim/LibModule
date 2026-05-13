<?php

declare(strict_types=1);

namespace easylibraryexamples\diagnostics;

use pocketmine\plugin\PluginBase;

final class DiagnosticsExamplePlugin extends PluginBase {

    protected function onEnable(): void {
        $this->getLogger()->info('Diagnostics module example enabled. Some modules are intentionally broken for /easymodule doctor tests.');
    }
}
