<?php

declare(strict_types=1);

namespace easylibraryexamples\basic\modules\hello\service;

use easylibraryexamples\basic\modules\hello\api\HelloApi;
use pocketmine\player\Player;

final class HelloService implements HelloApi {

    private int $openCount = 0;

    public function __construct(private string $prefix) {}

    public function getGreeting(Player|string $target): string {
        $name = $target instanceof Player ? $target->getName() : $target;
        return $this->prefix . ' Ola, ' . $name . '!';
    }

    public function incrementOpenCount(): void {
        $this->openCount++;
    }

    public function getOpenCount(): int {
        return $this->openCount;
    }
}
