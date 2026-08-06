<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\ValidCnpj;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidCnpjTest extends TestCase
{
    #[DataProvider('validCnpjs')]
    public function test_it_accepts_valid_cnpjs(string $cnpj): void
    {
        $this->assertTrue(ValidCnpj::isValid($cnpj));
    }

    #[DataProvider('invalidCnpjs')]
    public function test_it_rejects_invalid_cnpjs(string $cnpj): void
    {
        $this->assertFalse(ValidCnpj::isValid($cnpj));
    }

    public static function validCnpjs(): array
    {
        return [
            ['11222333000181'],
            ['11444777000161'],
        ];
    }

    public static function invalidCnpjs(): array
    {
        return [
            'all repeated digits' => ['00000000000000'],
            'invalid checksum' => ['11222333000199'],
            'too short' => ['1234'],
            'empty' => [''],
        ];
    }
}
