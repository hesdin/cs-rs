<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqCategoryRequest;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = FaqCategory::query()
            ->withCount('faqs')
            ->when($request->string('q')->toString(), function ($q, string $term): void {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($term).'%']);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/categories/Index', [
            'categories' => $categories,
            'filters' => ['q' => $request->string('q')->toString()],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/categories/Form', [
            'category' => null,
        ]);
    }

    public function store(FaqCategoryRequest $request): RedirectResponse
    {
        FaqCategory::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kategori dibuat.']);

        return to_route('admin.categories.index');
    }

    public function edit(FaqCategory $category): Response
    {
        return Inertia::render('admin/categories/Form', [
            'category' => $category,
        ]);
    }

    public function update(FaqCategoryRequest $request, FaqCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kategori diperbarui.']);

        return to_route('admin.categories.index');
    }

    public function destroy(FaqCategory $category): RedirectResponse
    {
        if ($category->faqs()->exists()) {
            return back()->withErrors([
                'category' => 'Kategori tidak dapat dihapus karena masih digunakan oleh FAQ.',
            ]);
        }

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kategori dihapus.']);

        return back();
    }
}
