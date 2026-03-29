<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('viewer');
});

describe('index', function () {
    it('displays the categories listing for authorized users', function () {
        Category::factory()->count(3)->create();

        $this->actingAs($this->viewer)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Categories');
    });
});

describe('show', function () {
    it('displays a single category', function () {
        $category = Category::factory()->create();

        $this->actingAs($this->viewer)
            ->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee($category->name);
    });
});

describe('create', function () {
    it('shows the create form for editors', function () {
        $this->actingAs($this->editor)
            ->get(route('categories.create'))
            ->assertOk()
            ->assertSee('Create Category');
    });

    it('denies the create form for viewers', function () {
        $this->actingAs($this->viewer)
            ->get(route('categories.create'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a category as editor', function () {
        $this->actingAs($this->editor)
            ->post(route('categories.store'), [
                'name' => 'Electronics',
                'description' => 'Electronic devices',
            ])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    });

    it('denies category creation for viewers', function () {
        $this->actingAs($this->viewer)
            ->post(route('categories.store'), [
                'name' => 'Forbidden',
                'description' => 'Should not exist',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Forbidden']);
    });

    it('validates required fields', function () {
        $this->actingAs($this->editor)
            ->post(route('categories.store'), [])
            ->assertSessionHasErrors('name');
    });
});

describe('edit', function () {
    it('shows the edit form for editors', function () {
        $category = Category::factory()->create();

        $this->actingAs($this->editor)
            ->get(route('categories.edit', $category))
            ->assertOk()
            ->assertSee('Edit Category');
    });

    it('denies the edit form for viewers', function () {
        $category = Category::factory()->create();

        $this->actingAs($this->viewer)
            ->get(route('categories.edit', $category))
            ->assertForbidden();
    });
});

describe('update', function () {
    it('updates a category as editor', function () {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->editor)
            ->put(route('categories.update', $category), [
                'name' => 'New Name',
                'description' => 'Updated description',
            ])
            ->assertRedirect(route('categories.show', $category));

        $this->assertDatabaseHas('categories', [
            'uuid' => $category->uuid,
            'name' => 'New Name',
        ]);
    });

    it('denies category update for viewers', function () {
        $category = Category::factory()->create(['name' => 'Unchanged']);

        $this->actingAs($this->viewer)
            ->put(route('categories.update', $category), [
                'name' => 'Changed',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'uuid' => $category->uuid,
            'name' => 'Unchanged',
        ]);
    });
});

describe('destroy', function () {
    it('soft deletes a category as admin', function () {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertSoftDeleted('categories', ['uuid' => $category->uuid]);
    });

    it('denies category deletion for editors', function () {
        $category = Category::factory()->create();

        $this->actingAs($this->editor)
            ->delete(route('categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['uuid' => $category->uuid, 'deleted_at' => null]);
    });

    it('denies category deletion for viewers', function () {
        $category = Category::factory()->create();

        $this->actingAs($this->viewer)
            ->delete(route('categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['uuid' => $category->uuid, 'deleted_at' => null]);
    });
});
