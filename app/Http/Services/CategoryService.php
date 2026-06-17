<?php

namespace App\Http\Services;

use App\Models\Category;

class CategoryService
{
    public function getAll(): mixed
    {
        return Category::all();
    }

    public function getById(int $id): Category
    {
        $category = Category::find($id);
        if (!$category) throw new \Exception('Category not found', 404);
        return $category;
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->getById($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): array
    {
        $category = $this->getById($id);
        $category->delete();
        return ['message' => 'Category deleted'];
    }
}