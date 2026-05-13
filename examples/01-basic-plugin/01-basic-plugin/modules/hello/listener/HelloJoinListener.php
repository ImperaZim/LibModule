<?php

declare(strict_types=1);

namespace easylibraryexamples\basic\modules\hello\listener;

use easylibraryexamples\basic\modules\hello\HelloModule;
use imperazim\module\event\BaseModuleListener;
use pocketmine\event\player\PlayerJoinEvent;

final class HelloJoinListener extends BaseModuleListener {

    public function onJoin(PlayerJoinEvent $event): void {
        /** @var HelloModule $module */
        $module = $this->getModule();
        if (!$module->isJoinMessageEnabled()) {
            return;
        }

        $event->getPlayer()->sendMessage($module->getHelloService()->getGreeting($event->getPlayer()));
    }
}
