<?php

declare(strict_types=1);

namespace easylibraryexamples\economy\modules\economy\service;

use easylibraryexamples\economy\modules\economy\api\EconomyApi;
use pocketmine\player\Player;

final class InMemoryEconomyService implements EconomyApi {

    /** @var array<string, float> */
    private array $balances = [];

    public function __construct(private float $startingBalance = 0.0) {}

    public function getBalance(Player|string $player): float {
        $name = $this->normalize($player);
        return $this->balances[$name] ?? $this->startingBalance;
    }

    public function addMoney(Player|string $player, float $amount): void {
        $name = $this->normalize($player);
        $this->balances[$name] = $this->getBalance($name) + max(0.0, $amount);
    }

    public function takeMoney(Player|string $player, float $amount): bool {
        $name = $this->normalize($player);
        $amount = max(0.0, $amount);
        $balance = $this->getBalance($name);
        if ($balance < $amount) {
            return false;
        }

        $this->balances[$name] = $balance - $amount;
        return true;
    }

    public function format(float $amount): string {
        return '$' . number_format($amount, 2, '.', ',');
    }

    /** @return array<string, float> */
    public function getBalances(): array {
        return $this->balances;
    }

    private function normalize(Player|string $player): string {
        return strtolower($player instanceof Player ? $player->getName() : $player);
    }
}
