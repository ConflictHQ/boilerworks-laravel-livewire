<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('viewer');

    $this->category = Category::factory()->create();
});

describe('index', function () {
    it('displays the products listing for authorized users', function () {
        Product::factory()->count(3)->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Products');
    });
});

describe('show', function () {
    it('displays a single product', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->get(route('products.show', $product))
            ->assertOk()
            ->assertSee($product->name);
    });
});

describe('create', function () {
    it('shows the create form for editors', function () {
        $this->actingAs($this->editor)
            ->get(route('products.create'))
            ->assertOk()
            ->assertSee('Create Product');
    });

    it('denies the create form for viewers', function () {
        $this->actingAs($this->viewer)
            ->get(route('products.create'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a product as editor', function () {
        $payload = [
            'name' => 'Test Product',
            'description' => 'A test product description',
            'price' => 29.99,
            'status' => 'active',
            'category_id' => $this->category->id,
        ];

        $this->actingAs($this->editor)
            ->post(route('products.store'), $payload)
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 29.99,
            'status' => 'active',
        ]);
    });

    it('denies product creation for viewers', function () {
        $payload = [
            'name' => 'Forbidden Product',
            'description' => 'Should not be created',
            'price' => 10.00,
            'status' => 'active',
            'category_id' => $this->category->id,
        ];

        $this->actingAs($this->viewer)
            ->post(route('products.store'), $payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('products', ['name' => 'Forbidden Product']);
    });

    it('validates required fields', function () {
        $this->actingAs($this->editor)
            ->post(route('products.store'), [])
            ->assertSessionHasErrors(['name', 'price', 'status']);
    });

    it('validates price is positive', function () {
        $this->actingAs($this->editor)
            ->post(route('products.store'), [
                'name' => 'Bad Price',
                'price' => 0,
                'status' => 'active',
            ])
            ->assertSessionHasErrors('price');
    });
});

describe('edit', function () {
    it('shows the edit form for editors', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->editor)
            ->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('Edit Product');
    });

    it('denies the edit form for viewers', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->get(route('products.edit', $product))
            ->assertForbidden();
    });
});

describe('update', function () {
    it('updates a product as editor', function () {
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->editor)
            ->put(route('products.update', $product), [
                'name' => 'Updated Name',
                'description' => 'Updated description',
                'price' => 49.99,
                'status' => 'active',
                'category_id' => $this->category->id,
            ])
            ->assertRedirect(route('products.show', $product));

        $this->assertDatabaseHas('products', [
            'uuid' => $product->uuid,
            'name' => 'Updated Name',
            'price' => 49.99,
        ]);
    });

    it('denies product update for viewers', function () {
        $product = Product::factory()->create([
            'name' => 'Should Stay',
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->viewer)
            ->put(route('products.update', $product), [
                'name' => 'Changed',
                'price' => 1.00,
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'uuid' => $product->uuid,
            'name' => 'Should Stay',
        ]);
    });
});

describe('destroy', function () {
    it('soft deletes a product as admin', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertSoftDeleted('products', ['uuid' => $product->uuid]);
    });

    it('denies product deletion for editors', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->editor)
            ->delete(route('products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['uuid' => $product->uuid, 'deleted_at' => null]);
    });

    it('denies product deletion for viewers', function () {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->delete(route('products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['uuid' => $product->uuid, 'deleted_at' => null]);
    });
});
