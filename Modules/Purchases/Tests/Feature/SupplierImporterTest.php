<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature;

use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Modules\Purchases\Enums\SupplierType;
use Modules\Purchases\Filament\Imports\SupplierImporter;
use Modules\Purchases\Filament\Resources\Suppliers\Pages\ListSuppliers;
use Modules\Purchases\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierImporterTest extends TestCase
{
    use RefreshDatabase;

    private function makeImporter(): SupplierImporter
    {
        $import = Import::query()->create([
            'file_name' => 'suppliers.csv',
            'file_path' => 'suppliers.csv',
            'importer' => SupplierImporter::class,
            'total_rows' => 1,
            'user_id' => User::factory()->create()->id,
        ]);

        $columnMap = collect(SupplierImporter::getColumns())
            ->mapWithKeys(fn ($column): array => [$column->getName() => $column->getName()])
            ->all();

        return new SupplierImporter($import, columnMap: $columnMap, options: []);
    }

    public function test_importing_a_new_document_creates_a_supplier(): void
    {
        $importer = $this->makeImporter();

        $importer([
            'type' => 'company',
            'name' => 'Fornecedor Ltda',
            'document' => '11222333000181',
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('suppliers', [
            'document' => '11222333000181',
            'name' => 'Fornecedor Ltda',
            'type' => SupplierType::Company->value,
            'is_active' => true,
        ]);
    }

    public function test_importing_an_existing_document_updates_the_supplier(): void
    {
        $supplier = Supplier::factory()->create([
            'type' => SupplierType::Company,
            'document' => '11222333000181',
            'name' => 'Nome Antigo',
        ]);

        $importer = $this->makeImporter();

        $importer([
            'type' => 'company',
            'name' => 'Nome Novo',
            'document' => '11222333000181',
        ]);

        $this->assertSame(1, Supplier::query()->count());
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Nome Novo',
        ]);
    }

    public function test_importing_an_invalid_cnpj_fails_validation(): void
    {
        $importer = $this->makeImporter();

        $this->expectException(ValidationException::class);

        $importer([
            'type' => 'company',
            'name' => 'CNPJ Inválido',
            'document' => '11111111111111',
        ]);
    }

    public function test_import_action_is_only_visible_with_create_permission(): void
    {
        Permission::query()->create(['name' => 'suppliers.view']);
        Permission::query()->create(['name' => 'suppliers.create']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['suppliers.view', 'suppliers.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        Livewire::test(ListSuppliers::class)->assertActionVisible('import');

        $userWithoutCreate = User::factory()->create();
        $userWithoutCreate->givePermissionTo('suppliers.view');
        $this->actingAs($userWithoutCreate);

        Livewire::test(ListSuppliers::class)->assertActionHidden('import');
    }
}
