<?php

declare(strict_types=1);

namespace easylibraryexamples\farm;

use pocketmine\plugin\PluginBase;

final class FarmConsumerExamplePlugin extends PluginBase {

    protected function onEnable(): void {
        $this->getLogger()->info('Farm consumer example enabled. It needs the economy provider example to fully work.');
    }
}
