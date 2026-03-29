<?php

use App\Livewire\Categories\CategoryForm;
use App\Livewire\Categories\CategoryIndex;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('viewer');
});

describe('CategoryIndex', function () {
    it('renders the category listing', function () {
        Category::factory()->count(2)->create();

        Livewire::actingAs($this->viewer)
            ->test(CategoryIndex::class)
            ->assertStatus(200)
            ->assertSee('Categories');
    });

    it('filters categories by search', function () {
        Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->create(['name' => 'Furniture']);

        Livewire::actingAs($this->viewer)
            ->test(CategoryIndex::class)
            ->set('search', 'Electro')
            ->assertSee('Electronics')
            ->assertDontSee('Furniture');
    });

    it('soft-deletes a category', function () {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->call('delete', $category->uuid);

        $this->assertSoftDeleted('categories', ['uuid' => $category->uuid]);
    });
});

describe('CategoryForm', function () {
    it('creates a category with valid data', function () {
        Livewire::actingAs($this->admin)
            ->test(CategoryForm::class)
            ->set('name', 'New Category')
            ->set('description', 'A new test category')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    });

    it('validates name is required', function () {
        Livewire::actingAs($this->admin)
            ->test(CategoryForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    });
});
