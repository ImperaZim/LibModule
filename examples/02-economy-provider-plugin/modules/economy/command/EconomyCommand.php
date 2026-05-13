<?php

declare(strict_types=1);

namespace easylibraryexamples\economy\modules\economy\command;

use easylibraryexamples\economy\modules\economy\EconomyModule;
use imperazim\module\ModuleManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

final class EconomyCommand extends Command {

    public function __construct(
        private Plugin $plugin,
        private EconomyModule $module,
        private ModuleManager $manager
    ) {
        parent::__construct('exampleeco', 'Tests the example economy service.', '/exampleeco balance|give|take [player] [amount]');
        $this->setPermission('easylibrary.examples.economy');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return true;
        }

        $service = $this->module->getEconomy();
        $sub = strtolower((string) ($args[0] ?? 'balance'));
        $target = (string) ($args[1] ?? $sender->getName());
        $amount = isset($args[2]) ? (float) $args[2] : 0.0;

        if ($sub === 'give') {
            $service->addMoney($target, $amount);
            $sender->sendMessage('§aAdded §f' . $service->format($amount) . ' §ato §f' . $target . '§a.');
            return true;
        }

        if ($sub === 'take') {
            $ok = $service->takeMoney($target, $amount);
            $sender->sendMessage($ok
                ? '§aRemoved §f' . $service->format($amount) . ' §afrom §f' . $target . '§a.'
                : '§cNot enough money.');
            return true;
        }

        $sender->sendMessage('§aBalance of §f' . $target . '§a: §f' . $service->format($service->getBalance($target)));
        $sender->sendMessage('§7Service registered: §f' . ($this->manager->services()->has('examples:economy-service') ? 'yes' : 'no'));
        return true;
    }
}
