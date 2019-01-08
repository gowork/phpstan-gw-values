<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues;

use GW\PHPStan\GwValues\ArrayValueDynamicReturn;
use PHPStan\Testing\TestCase;

final class ArrayValueDynamicReturnTest extends TestCase
{
    /** @var ArrayValueDynamicReturn */
    private $extension;

    protected function setUp()
    {
        $this->extension = new ArrayValueDynamicReturn();
    }
}
