<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        return view('items.index', [
            'items' => Item::with('category')
                ->orderBy('created_at', 'desc')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('items.create', [
            'categories' => Category::orderBy('name')->get(['id', 'uuid', 'name']),
        ]);
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        Item::create($request->validated());

        return redirect()
            ->route('items.index')
            ->with('success', 'Item created.');
    }

    public function show(Item $item): View
    {
        return view('items.show', [
            'item' => $item->load('category', 'creator'),
        ]);
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(['id', 'uuid', 'name']),
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Item updated.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item deleted.');
    }
}
