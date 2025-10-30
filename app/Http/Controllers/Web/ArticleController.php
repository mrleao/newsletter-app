<?php

namespace App\Http\Controllers\Web;

use App\DTOs\ArticleCreateDTO;
use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Services\ArticleCategoryService;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

final class ArticleController extends Controller
{
    public function __construct(
        private ArticleService $service,
        private ArticleCategoryService $articleCategoryService
    ) {}

    public function index(Request $request): Response
    {
        try {
            $perPage = (int) $request->integer('per_page', 15);
            $filters = [
                'category_id' => $request->filled('category_id') ? (int) $request->input('category_id') : null,
                'status' => $request->filled('status') ? (string) $request->input('status') : null,
                'q' => $request->string('q')->toString(),
            ];
            $articles = $this->service->paginate($perPage, $filters);
            return Inertia::render('Articles/Index', [
                'articles' => $articles,
                'categories' => $this->articleCategoryService->parentOptions(),
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Articles/Index', [
                'articles' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function readArticlePage(int $id): Response
    {
        try {
            $article = $this->service->find($id);
            return Inertia::render('Site/ReadArticle', [
                'article' => $article,
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Site/ReadArticle', [
                'article' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function articlesPage(Request $request): Response
    {
        try {
            $filters = [
                'category_id' => $request->filled('category_id') ? (int) $request->input('category_id') : null,
                'status' => 'published',
                'q' => $request->string('q')->toString(),
            ];

            $articles = $this->service->paginate(12, $filters);
            return Inertia::render('Site/Articles', [
                'articles' => $articles,
                'categories' => $this->articleCategoryService->parentOptions(),
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Site/Articles', [
                'articles' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function storeView(): Response
    {
        try {
            return Inertia::render('Articles/Create', [
                'articlesCategories' => $this->articleCategoryService->parentOptions()
            ]);
        } catch (\Throwable $e) {
            return Inertia::render(
                'Articles/Create',
                ['articlesCategories' => null, 'error' => $e->getMessage()]
            );
        }
    }

    public function updateView(int $id): Response
    {
        try {
            return Inertia::render('Articles/Edit', [
                'article' => $this->service->find($id),
                'articlesCategories' => $this->articleCategoryService->parentOptions(),
            ]);
        } catch (\Throwable $e) {
            return Inertia::render(
                'Articles/Create',
                ['articlesCategories' => null, 'error' => $e->getMessage()]
            );
        }
    }

    public function store(ArticleStoreRequest $request): RedirectResponse
    {
        try {
            $dto = ArticleCreateDTO::fromRequest($request);
            $this->service->create($dto);
            return Redirect::route('articles.index');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(int $articleId, ArticleUpdateRequest $request): RedirectResponse
    {
        try {
            $dto = ArticleCreateDTO::fromRequest($request);
            $this->service->update($articleId, $dto);
            return Redirect::route('articles.index');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $articleId): RedirectResponse
    {
        try {
            $this->service->deleteById($articleId);
            return redirect()->back()->with('success', 'Notícia removida');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
