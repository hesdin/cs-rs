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
                    $inner->where('question', 'ILIKE', "%{$term}%")
                        ->orWhere('answer', 'ILIKE', "%{$term}%")
                        ->orWhere('category', 'ILIKE', "%{$term}%");
                });
            })
            ->orderByDesc('priority')
            ->orderBy('category')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/faqs/Index', [
            'faqs' => $faqs,
            'filters' => ['q' => $request->string('q')->toString()],
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
