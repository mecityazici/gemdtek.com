<?php

namespace Tests\Unit;

use App\Support\CsvSafe;
use PHPUnit\Framework\TestCase;

class CsvSafeTest extends TestCase
{
    public function test_escapes_formula_leading_characters(): void
    {
        $this->assertSame("'=SUM(A1)", CsvSafe::cell('=SUM(A1)'));
        $this->assertSame("'+1234", CsvSafe::cell('+1234'));
        $this->assertSame("'-cmd", CsvSafe::cell('-cmd'));
        $this->assertSame("'@WEBSERVICE", CsvSafe::cell('@WEBSERVICE'));
    }

    public function test_leaves_safe_values_untouched(): void
    {
        $this->assertSame('Merhaba dünya', CsvSafe::cell('Merhaba dünya'));
        $this->assertSame('123 Sokak', CsvSafe::cell('123 Sokak'));
        $this->assertSame(42, CsvSafe::cell(42));
        $this->assertNull(CsvSafe::cell(null));
        $this->assertSame('', CsvSafe::cell(''));
    }
}
