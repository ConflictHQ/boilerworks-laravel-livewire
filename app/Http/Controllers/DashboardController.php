<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'items' => Item::count(),
                'categories' => Category::count(),
                'users' => User::count(),
            ],
        ]);
    }
}
