<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(Request $request): Response
    {
        $faqs = Faq::query()
            ->with('category')
            ->when($request->string('q')->toString(), function ($q, string $term): void {
                $q->where(function ($inner) use ($term): void {
                    $like = '%'.mb_strtolower($term).'%';
                    $inner->whereRaw('LOWER(question) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(answer) LIKE ?', [$like])
                        ->orWhereHas('category', function ($c) use ($like): void {
                            $c->whereRaw('LOWER(name) LIKE ?', [$like]);
                        });
                });
            })
            ->when($request->filled('category_id'), function ($q) use ($request): void {
                $q->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('status') && $request->string('status')->toString() !== 'all', function ($q) use ($request): void {
                $q->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->orderByDesc('priority')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $categories = FaqCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('admin/faqs/Index', [
            'faqs' => $faqs,
            'categories' => $categories,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'category_id' => $request->integer('category_id') ?: null,
                'status' => $request->string('status')->toString() ?: 'all',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/faqs/Form', [
            'faq' => null,
            'categories' => FaqCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'FAQ berhasil dibuat.']);

        return to_route('admin.faqs.index');
    }

    public function edit(Faq $faq): Response
    {
        return Inertia::render('admin/faqs/Form', [
            'faq' => $faq,
            'categories' => FaqCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'FAQ berhasil diperbarui.']);

        return to_route('admin.faqs.index');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'FAQ dihapus.']);

        return back();
    }
}
