<?php

declare(strict_types=1);

namespace easylibraryexamples\economy;

use pocketmine\plugin\PluginBase;

final class EconomyProviderExamplePlugin extends PluginBase {

    protected function onEnable(): void {
        $this->getLogger()->info('Economy provider example enabled.');
    }
}
