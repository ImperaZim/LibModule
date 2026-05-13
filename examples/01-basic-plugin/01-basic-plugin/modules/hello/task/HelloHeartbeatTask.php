<?php

declare(strict_types=1);

namespace easylibraryexamples\basic\modules\hello\task;

use easylibraryexamples\basic\modules\hello\HelloModule;
use pocketmine\scheduler\Task;

final class HelloHeartbeatTask extends Task {

    public function __construct(private HelloModule $module) {}

    public function onRun(): void {
        $this->module->getOwner()->getLogger()->debug('[HelloModule] heartbeat task is alive.');
    }
}
