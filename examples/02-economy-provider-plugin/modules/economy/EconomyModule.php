<?php

declare(strict_types=1);

namespace easylibraryexamples\economy\modules\economy;

use easylibraryexamples\economy\modules\economy\api\EconomyApi;
use easylibraryexamples\economy\modules\economy\command\EconomyCommand;
use easylibraryexamples\economy\modules\economy\service\InMemoryEconomyService;
use imperazim\module\BaseModule;
use imperazim\module\health\ModuleHealthReport;
use imperazim\module\ModuleApiProvider;
use imperazim\module\ModuleManager;

final class EconomyModule extends BaseModule implements ModuleApiProvider {

    private InMemoryEconomyService $service;

    protected function onEnable(ModuleManager $manager): void {
        $config = $this->getModuleConfig('config.yml', [
            'starting-balance' => 100.0
        ]);

        $this->service = new InMemoryEconomyService((float) $config->get('starting-balance', 100.0));
        $this->provideService('examples:economy-service', $this->service, EconomyApi::class, '1.0.0');
        $this->addCommand(EconomyCommand::class);

        $this->logger()->info('Economy service examples:economy-service is available.');
    }

    public function getApi(): EconomyApi {
        return $this->service;
    }

    public function getEconomy(): InMemoryEconomyService {
        return $this->service;
    }

    public function getHealth(): ModuleHealthReport {
        return ModuleHealthReport::ok([
            'trackedPlayers' => count($this->service->getBalances()),
            'service' => 'examples:economy-service',
            'capability' => 'economy'
        ]);
    }
}
