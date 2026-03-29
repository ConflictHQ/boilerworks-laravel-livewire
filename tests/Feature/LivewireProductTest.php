<?php

use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('viewer');

    $this->category = Category::factory()->create();
});

describe('ProductIndex', function () {
    it('renders the product listing', function () {
        Product::factory()->count(3)->create(['category_id' => $this->category->id]);

        Livewire::actingAs($this->viewer)
            ->test(ProductIndex::class)
            ->assertStatus(200)
            ->assertSee('Products');
    });

    it('filters products by search term', function () {
        Product::factory()->create([
            'name' => 'Widget Alpha',
            'category_id' => $this->category->id,
        ]);
        Product::factory()->create([
            'name' => 'Gadget Beta',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->viewer)
            ->test(ProductIndex::class)
            ->set('search', 'Widget')
            ->assertSee('Widget Alpha')
            ->assertDontSee('Gadget Beta');
    });

    it('shows empty state when no products match search', function () {
        Product::factory()->create([
            'name' => 'Widget Alpha',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->viewer)
            ->test(ProductIndex::class)
            ->set('search', 'Nonexistent')
            ->assertSee('No products found.');
    });

    it('soft-deletes a product via delete action', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        Livewire::actingAs($this->admin)
            ->test(ProductIndex::class)
            ->call('delete', $product->uuid);

        $this->assertSoftDeleted('products', ['uuid' => $product->uuid]);
    });

    it('paginates products', function () {
        Product::factory()->count(30)->create(['category_id' => $this->category->id]);

        Livewire::actingAs($this->viewer)
            ->test(ProductIndex::class)
            ->assertSee('Products')
            ->assertStatus(200);
    });
});

describe('ProductForm', function () {
    it('renders the create form', function () {
        Livewire::actingAs($this->editor)
            ->test(ProductForm::class)
            ->assertStatus(200)
            ->assertSee('Create Product');
    });

    it('creates a product with valid data', function () {
        Livewire::actingAs($this->editor)
            ->test(ProductForm::class)
            ->set('name', 'New Widget')
            ->set('description', 'A fine widget')
            ->set('price', '19.99')
            ->set('status', 'active')
            ->set('category_id', $this->category->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'New Widget',
            'status' => 'active',
        ]);
    });

    it('validates required fields', function () {
        Livewire::actingAs($this->editor)
            ->test(ProductForm::class)
            ->set('name', '')
            ->set('price', '')
            ->set('status', '')
            ->call('save')
            ->assertHasErrors(['name', 'price', 'status']);
    });

    it('validates price must be positive', function () {
        Livewire::actingAs($this->editor)
            ->test(ProductForm::class)
            ->set('name', 'Bad Price Product')
            ->set('price', '0')
            ->set('status', 'active')
            ->call('save')
            ->assertHasErrors(['price']);
    });

    it('loads existing product data for editing', function () {
        $product = Product::factory()->create([
            'name' => 'Existing Widget',
            'price' => 42.50,
            'status' => 'active',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->editor)
            ->test(ProductForm::class, ['product' => $product])
            ->assertSet('name', 'Existing Widget')
            ->assertSet('price', '42.50')
            ->assertSet('status', 'active')
            ->assertSee('Edit Product');
    });

    it('updates an existing product', function () {
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->editor)
            ->test(ProductForm::class, ['product' => $product])
            ->set('name', 'Updated Name')
            ->set('price', '55.00')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'uuid' => $product->uuid,
            'name' => 'Updated Name',
        ]);
    });
});
