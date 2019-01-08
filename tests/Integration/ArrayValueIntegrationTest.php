<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues\Integration;

use PHPStan\Testing\LevelsTestCase;

final class ArrayValueIntegrationTest extends LevelsTestCase
{
    public function dataTopics(): array
    {
        return [
            ['arrayValue'],
        ];
    }

    public function getDataPath(): string
    {
        return __DIR__ . '/data';
    }

    public function getPhpStanExecutablePath(): string
    {
        return __DIR__ . '/../../vendor/bin/phpstan';
    }

    public function getPhpStanConfigPath(): ?string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
