<?php

use App\Models\Category;
use App\Models\Item;
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
    it('displays the items listing for authorized users', function () {
        Item::factory()->count(3)->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->get(route('items.index'))
            ->assertOk()
            ->assertSee('Items');
    });
});

describe('show', function () {
    it('displays a single item', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->get(route('items.show', $item))
            ->assertOk()
            ->assertSee($item->name);
    });
});

describe('create', function () {
    it('shows the create form for editors', function () {
        $this->actingAs($this->editor)
            ->get(route('items.create'))
            ->assertOk()
            ->assertSee('Create Item');
    });

    it('denies the create form for viewers', function () {
        $this->actingAs($this->viewer)
            ->get(route('items.create'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a item as editor', function () {
        $payload = [
            'name' => 'Test Item',
            'description' => 'A test item description',
            'price' => 29.99,
            'status' => 'active',
            'category_id' => $this->category->id,
        ];

        $this->actingAs($this->editor)
            ->post(route('items.store'), $payload)
            ->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'name' => 'Test Item',
            'price' => 29.99,
            'status' => 'active',
        ]);
    });

    it('denies item creation for viewers', function () {
        $payload = [
            'name' => 'Forbidden Item',
            'description' => 'Should not be created',
            'price' => 10.00,
            'status' => 'active',
            'category_id' => $this->category->id,
        ];

        $this->actingAs($this->viewer)
            ->post(route('items.store'), $payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('items', ['name' => 'Forbidden Item']);
    });

    it('validates required fields', function () {
        $this->actingAs($this->editor)
            ->post(route('items.store'), [])
            ->assertSessionHasErrors(['name', 'price', 'status']);
    });

    it('validates price is positive', function () {
        $this->actingAs($this->editor)
            ->post(route('items.store'), [
                'name' => 'Bad Price',
                'price' => 0,
                'status' => 'active',
            ])
            ->assertSessionHasErrors('price');
    });
});

describe('edit', function () {
    it('shows the edit form for editors', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->editor)
            ->get(route('items.edit', $item))
            ->assertOk()
            ->assertSee('Edit Item');
    });

    it('denies the edit form for viewers', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->get(route('items.edit', $item))
            ->assertForbidden();
    });
});

describe('update', function () {
    it('updates a item as editor', function () {
        $item = Item::factory()->create([
            'name' => 'Original Name',
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->editor)
            ->put(route('items.update', $item), [
                'name' => 'Updated Name',
                'description' => 'Updated description',
                'price' => 49.99,
                'status' => 'active',
                'category_id' => $this->category->id,
            ])
            ->assertRedirect(route('items.show', $item));

        $this->assertDatabaseHas('items', [
            'uuid' => $item->uuid,
            'name' => 'Updated Name',
            'price' => 49.99,
        ]);
    });

    it('denies item update for viewers', function () {
        $item = Item::factory()->create([
            'name' => 'Should Stay',
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->viewer)
            ->put(route('items.update', $item), [
                'name' => 'Changed',
                'price' => 1.00,
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('items', [
            'uuid' => $item->uuid,
            'name' => 'Should Stay',
        ]);
    });
});

describe('destroy', function () {
    it('soft deletes a item as admin', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin)
            ->delete(route('items.destroy', $item))
            ->assertRedirect(route('items.index'));

        $this->assertSoftDeleted('items', ['uuid' => $item->uuid]);
    });

    it('denies item deletion for editors', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->editor)
            ->delete(route('items.destroy', $item))
            ->assertForbidden();

        $this->assertDatabaseHas('items', ['uuid' => $item->uuid, 'deleted_at' => null]);
    });

    it('denies item deletion for viewers', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->viewer)
            ->delete(route('items.destroy', $item))
            ->assertForbidden();

        $this->assertDatabaseHas('items', ['uuid' => $item->uuid, 'deleted_at' => null]);
    });
});
