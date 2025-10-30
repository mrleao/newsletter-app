<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCategoryStoreRequest;
use App\Http\Requests\ArticleCategoryUpdateRequest;
use App\Models\ArticleCategory;
use App\Services\ArticleCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

final class ArticleCategoryController extends Controller
{
    public function __construct(private ArticleCategoryService $service) {}

    public function index(Request $request): Response
    {
        try {
            $perPage = (int) $request->integer('per_page', 15);
            $parentId = $request->filled('parent_id') ? (int) $request->input('parent_id') : null;
            $filters = [
                'parent_id' => $parentId,
                'q' => $request->string('q')->toString(),
            ];
            $categories = $this->service->paginate($perPage, $filters);
            return Inertia::render('ArticleCategories/Index', [
                'categories' => $categories
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('ArticleCategories/Index', [
                'categories' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function storeView()
    {
        return Inertia::render('ArticleCategories/Create', [
            'parentOptions' => $this->service->parentOptions(),
        ]);
    }

    public function updateView(int $id)
    {
        return Inertia::render('ArticleCategories/Edit', [
            'category' => $this->service->getById($id),
            'parentOptions' => $this->service->parentOptions(),
        ]);
    }

    public function store(ArticleCategoryStoreRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->toCreateDto());
            return Redirect::route('categories.index');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(int $categoryId, ArticleCategoryUpdateRequest $request): RedirectResponse
    {
        try {
            $this->service->updateById($categoryId, $request->toUpdateDto());
            return Redirect::route('categories.index');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $categoryId): RedirectResponse
    {
        try {
            $this->service->deleteById($categoryId);
            return redirect()->back()->with('success', 'Categoria removida');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
