<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(Request $request): Response
    {
        $faqs = Faq::query()
            ->when($request->string('q')->toString(), function ($q, string $term): void {
                $q->where(function ($inner) use ($term): void {
                    $inner->whereRaw('LOWER(question) LIKE ?', ['%'.mb_strtolower($term).'%'])
                        ->orWhereRaw('LOWER(answer) LIKE ?', ['%'.mb_strtolower($term).'%'])
                        ->orWhereRaw('LOWER(category) LIKE ?', ['%'.mb_strtolower($term).'%']);
                });
            })
            ->when($request->filled('category'), function ($q) use ($request): void {
                $q->where('category', $request->string('category')->toString());
            })
            ->when($request->filled('status') && $request->string('status')->toString() !== 'all', function ($q) use ($request): void {
                $q->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->orderByDesc('priority')
            ->orderBy('category')
            ->paginate(15)
            ->withQueryString();

        $categories = Faq::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return Inertia::render('admin/faqs/Index', [
            'faqs' => $faqs,
            'categories' => $categories,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'category' => $request->string('category')->toString(),
                'status' => $request->string('status')->toString() ?: 'all',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/faqs/Form', [
            'faq' => null,
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
