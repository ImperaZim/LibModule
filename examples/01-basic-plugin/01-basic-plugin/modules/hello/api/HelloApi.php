<?php

declare(strict_types=1);

namespace easylibraryexamples\basic\modules\hello\api;

use pocketmine\player\Player;

interface HelloApi {

    public function getGreeting(Player|string $target): string;

    public function getOpenCount(): int;
}
