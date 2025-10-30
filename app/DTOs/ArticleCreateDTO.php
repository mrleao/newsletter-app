<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

final readonly class ArticleCreateDTO
{
    public function __construct(
        public int $id_user,
        public ?int $category_id,
        public ?string $slug,
        public string $title,
        public string $body,
        public string $status,
        public ?string $published_at,
        public ?string $image_path,
        public ?UploadedFile $image,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            Auth::user()->id,
            isset($data['category_id']) ? (int)$data['category_id'] : null,
            $data['slug'] ?? null,
            $data['title'],
            $data['body'],
            $data['status'],
            $data['published_at'] ?? null,
            $data['image_path'] ?? null,
            $data['image'] ?? null,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            Auth::user()->id,
            isset($request['category_id']) ? (int)$request['category_id'] : null,
            $request['slug'] ?? null,
            $request['title'],
            $request['body'],
            $request['status'],
            $request['published_at'] ?? null,
            $request['image_path'] ?? null,
            $request['image'] ?? null,
        );
    }
    
}
