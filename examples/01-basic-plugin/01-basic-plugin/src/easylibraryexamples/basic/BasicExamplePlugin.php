<?php

declare(strict_types=1);

namespace easylibraryexamples\basic;

use pocketmine\plugin\PluginBase;

final class BasicExamplePlugin extends PluginBase {

    protected function onEnable(): void {
        $this->getLogger()->info('Basic module example plugin enabled. Use /easymodule list to see its modules.');
    }
}
