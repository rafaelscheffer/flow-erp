<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\ValidCpf;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidCpfTest extends TestCase
{
    #[DataProvider('validCpfs')]
    public function test_it_accepts_valid_cpfs(string $cpf): void
    {
        $this->assertTrue(ValidCpf::isValid($cpf));
    }

    #[DataProvider('invalidCpfs')]
    public function test_it_rejects_invalid_cpfs(string $cpf): void
    {
        $this->assertFalse(ValidCpf::isValid($cpf));
    }

    public static function validCpfs(): array
    {
        return [
            ['52998224725'],
            ['11144477735'],
        ];
    }

    public static function invalidCpfs(): array
    {
        return [
            'all repeated digits' => ['11111111111'],
            'invalid checksum' => ['12345678900'],
            'too short' => ['1234'],
            'empty' => [''],
        ];
    }
}
