<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature;

use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Modules\Products\Enums\ProductType;
use Modules\Products\Filament\Imports\ProductImporter;
use Modules\Products\Filament\Resources\Products\Pages\ListProducts;
use Modules\Products\Models\Brand;
use Modules\Products\Models\Product;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductImporterTest extends TestCase
{
    use RefreshDatabase;

    private function makeImporter(): ProductImporter
    {
        $import = Import::query()->create([
            'file_name' => 'products.csv',
            'file_path' => 'products.csv',
            'importer' => ProductImporter::class,
            'total_rows' => 1,
            'user_id' => User::factory()->create()->id,
        ]);

        $columnMap = collect(ProductImporter::getColumns())
            ->mapWithKeys(fn ($column): array => [$column->getName() => $column->getName()])
            ->all();

        return new ProductImporter($import, columnMap: $columnMap, options: []);
    }

    public function test_importing_a_new_sku_creates_a_product(): void
    {
        $brand = Brand::factory()->create(['name' => 'Acme']);

        $importer = $this->makeImporter();

        $importer([
            'brand' => 'Acme',
            'type' => 'simple',
            'name' => 'Produto Importado',
            'sku' => 'SKU-IMPORT-1',
            'cost_price' => '10.50',
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-IMPORT-1',
            'name' => 'Produto Importado',
            'brand_id' => $brand->id,
            'type' => ProductType::Simple->value,
        ]);
    }

    public function test_importing_an_existing_sku_updates_the_product(): void
    {
        $product = Product::factory()->create([
            'sku' => 'SKU-EXISTING',
            'name' => 'Nome Antigo',
        ]);

        $importer = $this->makeImporter();

        $importer([
            'type' => 'simple',
            'name' => 'Nome Novo',
            'sku' => 'SKU-EXISTING',
        ]);

        $this->assertSame(1, Product::query()->count());
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nome Novo',
        ]);
    }

    public function test_importing_without_a_required_field_fails_validation(): void
    {
        $importer = $this->makeImporter();

        $this->expectException(ValidationException::class);

        $importer([
            'type' => 'simple',
            'name' => '',
            'sku' => 'SKU-INVALID',
        ]);
    }

    public function test_import_action_is_only_visible_with_create_permission(): void
    {
        Permission::query()->create(['name' => 'products.view']);
        Permission::query()->create(['name' => 'products.create']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['products.view', 'products.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        Livewire::test(ListProducts::class)->assertActionVisible('import');

        $userWithoutCreate = User::factory()->create();
        $userWithoutCreate->givePermissionTo('products.view');
        $this->actingAs($userWithoutCreate);

        Livewire::test(ListProducts::class)->assertActionHidden('import');
    }
}
