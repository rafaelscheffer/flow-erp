<?php

declare(strict_types=1);

namespace Modules\Customers\Tests\Feature;

use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Modules\Customers\Enums\CustomerType;
use Modules\Customers\Filament\Imports\CustomerImporter;
use Modules\Customers\Filament\Resources\Customers\Pages\ListCustomers;
use Modules\Customers\Models\Customer;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerImporterTest extends TestCase
{
    use RefreshDatabase;

    private function makeImporter(): CustomerImporter
    {
        $import = Import::query()->create([
            'file_name' => 'customers.csv',
            'file_path' => 'customers.csv',
            'importer' => CustomerImporter::class,
            'total_rows' => 1,
            'user_id' => User::factory()->create()->id,
        ]);

        $columnMap = collect(CustomerImporter::getColumns())
            ->mapWithKeys(fn ($column): array => [$column->getName() => $column->getName()])
            ->all();

        return new CustomerImporter($import, columnMap: $columnMap, options: []);
    }

    public function test_importing_a_new_document_creates_a_customer(): void
    {
        $importer = $this->makeImporter();

        $importer([
            'type' => 'individual',
            'name' => 'João da Silva',
            'document' => '52998224725',
            'email' => 'joao@example.com',
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('customers', [
            'document' => '52998224725',
            'name' => 'João da Silva',
            'type' => CustomerType::Individual->value,
            'is_active' => true,
        ]);
    }

    public function test_importing_an_existing_document_updates_the_customer(): void
    {
        $customer = Customer::factory()->create([
            'type' => CustomerType::Individual,
            'document' => '52998224725',
            'name' => 'Nome Antigo',
        ]);

        $importer = $this->makeImporter();

        $importer([
            'type' => 'individual',
            'name' => 'Nome Novo',
            'document' => '52998224725',
        ]);

        $this->assertSame(1, Customer::query()->count());
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Nome Novo',
        ]);
    }

    public function test_importing_an_invalid_cpf_fails_validation(): void
    {
        $importer = $this->makeImporter();

        $this->expectException(ValidationException::class);

        $importer([
            'type' => 'individual',
            'name' => 'CPF Inválido',
            'document' => '11111111111',
        ]);
    }

    public function test_import_action_is_only_visible_with_create_permission(): void
    {
        Permission::query()->create(['name' => 'customers.view']);
        Permission::query()->create(['name' => 'customers.create']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['customers.view', 'customers.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        Livewire::test(ListCustomers::class)->assertActionVisible('import');

        $userWithoutCreate = User::factory()->create();
        $userWithoutCreate->givePermissionTo('customers.view');
        $this->actingAs($userWithoutCreate);

        Livewire::test(ListCustomers::class)->assertActionHidden('import');
    }
}
