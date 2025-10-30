<?php

namespace App\DTOs;

final readonly class ArticleCategoryUpdateDTO
{
    public function __construct(
        public string $name,
        public ?string $slug,
        public ?int $parent_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['slug'] ?? null,
            isset($data['parent_id']) ? (int)$data['parent_id'] : null,
        );
    }
}
