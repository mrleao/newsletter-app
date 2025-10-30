<?php

namespace App\DTOs;

final readonly class ArticleUpdateDTO
{
    public function __construct(
        public ?int $category_id,
        public ?string $slug,
        public ?string $title,
        public ?string $body,
        public ?string $status,
        public ?string $published_at,
        public ?string $image_path
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['category_id']) ? (int)$data['category_id'] : null,
            $data['slug'] ?? null,
            $data['title'] ?? null,
            $data['body'] ?? null,
            $data['status'] ?? null,
            $data['published_at'] ?? null,
            $data['image_path'] ?? null,
        );
    }
}
