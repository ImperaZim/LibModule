<?php

declare(strict_types=1);

namespace easylibraryexamples\basic\modules\hello;

use easylibraryexamples\basic\modules\hello\api\HelloApi;
use easylibraryexamples\basic\modules\hello\command\HelloCommand;
use easylibraryexamples\basic\modules\hello\listener\HelloJoinListener;
use easylibraryexamples\basic\modules\hello\service\HelloService;
use easylibraryexamples\basic\modules\hello\task\HelloHeartbeatTask;
use imperazim\module\BaseModule;
use imperazim\module\health\ModuleHealthReport;
use imperazim\module\ModuleApiProvider;
use imperazim\module\ModuleManager;

final class HelloModule extends BaseModule implements ModuleApiProvider {

    private HelloService $service;

    protected function onEnable(ModuleManager $manager): void {
        $config = $this->getModuleConfig('config.yml', [
            'prefix' => '§a[HelloModule]§r',
            'join-message' => true,
            'heartbeat-seconds' => 60
        ]);

        $prefix = (string) $config->get('prefix', '§a[HelloModule]§r');
        $this->service = new HelloService($prefix);

        $this->provideService('examples:hello-service', $this->service, HelloApi::class, '1.0.0');

        $this->addCommand(HelloCommand::class);
        $this->addListener(HelloJoinListener::class);

        $seconds = max(5, (int) $config->get('heartbeat-seconds', 60));
        $this->scheduleRepeatingTask(new HelloHeartbeatTask($this), 20 * $seconds);

        $this->addCleanup(function(): void {
            $this->logger()->debug('HelloModule cleanup callback executed.');
        });

        $this->logger()->info('Hello module enabled with service examples:hello-service.');
    }

    protected function onDisable(ModuleManager $manager): void {
        $this->logger()->info('Hello module disabled. Resources will be cleaned by the lifecycle.');
    }

    public function getApi(): HelloApi {
        return $this->service;
    }

    public function getHelloService(): HelloService {
        return $this->service;
    }

    public function isJoinMessageEnabled(): bool {
        return (bool) $this->getModuleConfig('config.yml')->get('join-message', true);
    }

    public function getHealth(): ModuleHealthReport {
        $config = $this->getModuleConfig('config.yml');
        $warnings = [];
        if ((string) $config->get('prefix', '') === '') {
            $warnings[] = 'Prefix is empty.';
        }

        return $warnings === []
            ? ModuleHealthReport::ok(['openCount' => $this->service->getOpenCount()])
            : ModuleHealthReport::warning($warnings, ['openCount' => $this->service->getOpenCount()]);
    }
}
