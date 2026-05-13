<?php

declare(strict_types=1);

namespace easylibraryexamples\basic\modules\hello\command;

use easylibraryexamples\basic\modules\hello\HelloModule;
use imperazim\module\ModuleManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

final class HelloCommand extends Command {

    public function __construct(
        private Plugin $plugin,
        private HelloModule $module,
        private ModuleManager $manager
    ) {
        parent::__construct('examplehello', 'Tests the EasyLibrary hello module.', '/examplehello [api|service|config]');
        $this->setPermission('easylibrary.examples.hello');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return true;
        }

        $sub = strtolower((string) ($args[0] ?? ''));
        $service = $this->module->getHelloService();
        $service->incrementOpenCount();

        if ($sub === 'api') {
            $api = $this->manager->getApi('examples:hello');
            $sender->sendMessage('§aAPI class: §f' . $api::class);
            return true;
        }

        if ($sub === 'service') {
            $sender->sendMessage('§aService registered: §f' . ($this->manager->services()->has('examples:hello-service') ? 'yes' : 'no'));
            return true;
        }

        if ($sub === 'config') {
            $config = $this->module->getConfig();
            $sender->sendMessage('§aModule manifest version: §f' . ($config['version'] ?? 'unknown'));
            return true;
        }

        $sender->sendMessage($service->getGreeting($sender->getName()));
        $sender->sendMessage('§7Open count: §f' . $service->getOpenCount());
        return true;
    }
}
