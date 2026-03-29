<?php

use App\Livewire\Items\ItemForm;
use App\Livewire\Items\ItemIndex;
use App\Models\Category;
use App\Models\Item;
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

describe('ItemIndex', function () {
    it('renders the item listing', function () {
        Item::factory()->count(3)->create(['category_id' => $this->category->id]);

        Livewire::actingAs($this->viewer)
            ->test(ItemIndex::class)
            ->assertStatus(200)
            ->assertSee('Items');
    });

    it('filters items by search term', function () {
        Item::factory()->create([
            'name' => 'Widget Alpha',
            'category_id' => $this->category->id,
        ]);
        Item::factory()->create([
            'name' => 'Gadget Beta',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->viewer)
            ->test(ItemIndex::class)
            ->set('search', 'Widget')
            ->assertSee('Widget Alpha')
            ->assertDontSee('Gadget Beta');
    });

    it('shows empty state when no items match search', function () {
        Item::factory()->create([
            'name' => 'Widget Alpha',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->viewer)
            ->test(ItemIndex::class)
            ->set('search', 'Nonexistent')
            ->assertSee('No items found.');
    });

    it('soft-deletes a item via delete action', function () {
        $item = Item::factory()->create(['category_id' => $this->category->id]);

        Livewire::actingAs($this->admin)
            ->test(ItemIndex::class)
            ->call('delete', $item->uuid);

        $this->assertSoftDeleted('items', ['uuid' => $item->uuid]);
    });

    it('paginates items', function () {
        Item::factory()->count(30)->create(['category_id' => $this->category->id]);

        Livewire::actingAs($this->viewer)
            ->test(ItemIndex::class)
            ->assertSee('Items')
            ->assertStatus(200);
    });
});

describe('ItemForm', function () {
    it('renders the create form', function () {
        Livewire::actingAs($this->editor)
            ->test(ItemForm::class)
            ->assertStatus(200)
            ->assertSee('Create Item');
    });

    it('creates a item with valid data', function () {
        Livewire::actingAs($this->editor)
            ->test(ItemForm::class)
            ->set('name', 'New Widget')
            ->set('description', 'A fine widget')
            ->set('price', '19.99')
            ->set('status', 'active')
            ->set('category_id', $this->category->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('items', [
            'name' => 'New Widget',
            'status' => 'active',
        ]);
    });

    it('validates required fields', function () {
        Livewire::actingAs($this->editor)
            ->test(ItemForm::class)
            ->set('name', '')
            ->set('price', '')
            ->set('status', '')
            ->call('save')
            ->assertHasErrors(['name', 'price', 'status']);
    });

    it('validates price must be positive', function () {
        Livewire::actingAs($this->editor)
            ->test(ItemForm::class)
            ->set('name', 'Bad Price Item')
            ->set('price', '0')
            ->set('status', 'active')
            ->call('save')
            ->assertHasErrors(['price']);
    });

    it('loads existing item data for editing', function () {
        $item = Item::factory()->create([
            'name' => 'Existing Widget',
            'price' => 42.50,
            'status' => 'active',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->editor)
            ->test(ItemForm::class, ['item' => $item])
            ->assertSet('name', 'Existing Widget')
            ->assertSet('price', '42.50')
            ->assertSet('status', 'active')
            ->assertSee('Edit Item');
    });

    it('updates an existing item', function () {
        $item = Item::factory()->create([
            'name' => 'Old Name',
            'category_id' => $this->category->id,
        ]);

        Livewire::actingAs($this->editor)
            ->test(ItemForm::class, ['item' => $item])
            ->set('name', 'Updated Name')
            ->set('price', '55.00')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('items', [
            'uuid' => $item->uuid,
            'name' => 'Updated Name',
        ]);
    });
});
