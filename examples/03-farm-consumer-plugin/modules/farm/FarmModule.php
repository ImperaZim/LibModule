<?php

declare(strict_types=1);

namespace easylibraryexamples\farm\modules\farm;

use easylibraryexamples\economy\modules\economy\api\EconomyApi;
use easylibraryexamples\farm\modules\farm\command\FarmCommand;
use imperazim\module\BaseModule;
use imperazim\module\health\ModuleHealthReport;
use imperazim\module\ModuleManager;
use pocketmine\player\Player;

final class FarmModule extends BaseModule {

    private EconomyApi $economy;
    private int $harvests = 0;

    protected function onEnable(ModuleManager $manager): void {
        /** @var EconomyApi $economy */
        $economy = $this->getTypedService('examples:economy-service', EconomyApi::class);
        $this->economy = $economy;

        $this->addCommand(FarmCommand::class);
        $this->logger()->info('Farm module connected to examples:economy-service.');
    }

    public function harvest(Player $player): float {
        $reward = (float) $this->getModuleConfig('config.yml', [
            'reward-per-harvest' => 12.5
        ])->get('reward-per-harvest', 12.5);

        $this->economy->addMoney($player, $reward);
        $this->harvests++;
        return $reward;
    }

    public function formatMoney(float $amount): string {
        return $this->economy->format($amount);
    }

    public function getProviderInfo(): string {
        $provider = $this->getCapabilityProvider('economy');
        return $provider === null ? 'none' : $provider->getId() . ' from ' . $provider->getOwner()->getName();
    }

    public function getOptionalHelloMessage(Player|string $target): ?string {
        $api = $this->getOptionalApi('examples:hello');
        if ($api !== null && method_exists($api, 'getGreeting')) {
            return (string) $api->getGreeting($target);
        }

        return null;
    }

    public function getHealth(): ModuleHealthReport {
        return ModuleHealthReport::ok([
            'harvests' => $this->harvests,
            'economyProvider' => $this->getProviderInfo()
        ]);
    }
}
