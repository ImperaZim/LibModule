<?php

declare(strict_types=1);

namespace easylibraryexamples\farm\modules\farm\command;

use easylibraryexamples\farm\modules\farm\FarmModule;
use imperazim\module\ModuleManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

final class FarmCommand extends Command {

    public function __construct(
        private Plugin $plugin,
        private FarmModule $module,
        private ModuleManager $manager
    ) {
        parent::__construct('examplefarm', 'Tests a module that consumes another plugin service.', '/examplefarm [harvest|provider]');
        $this->setPermission('easylibrary.examples.farm');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return true;
        }

        $sub = strtolower((string) ($args[0] ?? 'harvest'));

        if ($sub === 'provider') {
            $sender->sendMessage('§aEconomy provider: §f' . $this->module->getProviderInfo());
            return true;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage('§cUse this command in-game to simulate a harvest.');
            return true;
        }

        $reward = $this->module->harvest($sender);
        $sender->sendMessage('§aHarvest complete. Reward: §f' . $this->module->formatMoney($reward));

        $hello = $this->module->getOptionalHelloMessage($sender);
        if ($hello !== null) {
            $sender->sendMessage('§7Optional hello module says: §f' . $hello);
        }

        return true;
    }
}
