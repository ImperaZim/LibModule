<?php

declare(strict_types=1);

namespace easylibraryexamples\diagnostics\modules\ok\command;

use easylibraryexamples\diagnostics\modules\ok\DiagnosticOkModule;
use imperazim\module\ModuleManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

final class DiagnosticCommand extends Command {

    public function __construct(
        private Plugin $plugin,
        private DiagnosticOkModule $module,
        private ModuleManager $manager
    ) {
        parent::__construct('examplediag', 'Shows diagnostics example status.', '/examplediag');
        $this->setPermission('easylibrary.examples.diagnostics');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return true;
        }

        $failures = $this->manager->failures();
        $sender->sendMessage('§aDiagnostic OK module is enabled.');
        $sender->sendMessage('§7Current module failures/waiting states: §f' . count($failures));
        foreach ($failures as $id => $reason) {
            $sender->sendMessage('§8- §c' . $id . '§7: §f' . $reason);
        }
        return true;
    }
}
