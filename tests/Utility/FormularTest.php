<?php

namespace App\Test\Utility;

use App\Utility\Formular;
use PHPUnit\Framework\TestCase;

class FormularTest extends TestCase
{
    public function testSum()
    {
        $a = 5;
        $b = 4;
        $this->assertEquals(9, Formular::sum($a, $b));
    }
}