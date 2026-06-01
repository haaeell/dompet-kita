<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $couple = Auth::user()->couple;
        $categories = $couple->categories()->withCount('transactions')->orderBy('type')->orderBy('name')->get();
        $activeTab = $request->get('tab', 'expense');

        if (!in_array($activeTab, ['income', 'expense'], true)) {
            $activeTab = 'expense';
        }

        return view('categories.index', compact('categories', 'activeTab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|size:7',
            'type' => 'required|in:income,expense',
        ]);

        $category = Auth::user()->couple->categories()->create($request->only(['name', 'icon', 'color', 'type']));
        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan!', 'category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        $request->validate(['name' => 'required|string|max:255', 'icon' => 'required|string', 'color' => 'required|string|size:7']);
        $category->update($request->only(['name', 'icon', 'color']));
        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui!']);
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        if ($category->transactions()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak bisa dihapus karena masih digunakan!'], 422);
        }
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus!']);
    }

}
