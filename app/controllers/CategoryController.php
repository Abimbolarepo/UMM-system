<?php

require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private Category $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Categories
    |--------------------------------------------------------------------------
    */
    public function index(): array
    {
        return $this->category->getAllCategories();
    }

    /*
    |--------------------------------------------------------------------------
    | Search Categories
    |--------------------------------------------------------------------------
    */
    public function search(string $keyword): array
    {
        if (trim($keyword) === '') {
            return $this->index();
        }

        return $this->category->search($keyword);
    }

    /*
    |--------------------------------------------------------------------------
    | Get One Category
    |--------------------------------------------------------------------------
    */
    public function show(int $id): ?array
    {
        return $this->category->getCategoryById($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Category
    |--------------------------------------------------------------------------
    */
    public function store(array $data): array
    {
        $name = trim($data['category_name'] ?? '');
        $description = trim($data['description'] ?? '');

        if ($name === '') {
            return [
                'success' => false,
                'message' => 'Category name is required.'
            ];
        }

        if ($this->category->exists($name)) {
            return [
                'success' => false,
                'message' => 'Category already exists.'
            ];
        }

        $saved = $this->category->create([
            'category_name' => $name,
            'description' => $description
        ]);

        return [
            'success' => $saved,
            'message' => $saved
                ? 'Category created successfully.'
                : 'Unable to create category.'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Update Category
    |--------------------------------------------------------------------------
    */
    public function update(int $id, array $data): array
    {
        $name = trim($data['category_name'] ?? '');
        $description = trim($data['description'] ?? '');

        if ($name === '') {
            return [
                'success' => false,
                'message' => 'Category name is required.'
            ];
        }

        if ($this->category->exists($name, $id)) {
            return [
                'success' => false,
                'message' => 'Another category already has this name.'
            ];
        }

        $updated = $this->category->update($id, [
            'category_name' => $name,
            'description' => $description
        ]);

        return [
            'success' => $updated,
            'message' => $updated
                ? 'Category updated successfully.'
                : 'Unable to update category.'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Category
    |--------------------------------------------------------------------------
    */
    public function destroy(int $id): array
    {
        $deleted = $this->category->delete($id);

        return [
            'success' => $deleted,
            'message' => $deleted
                ? 'Category deleted successfully.'
                : 'Category cannot be deleted because it is being used by one or more service requests.'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Statistics
    |--------------------------------------------------------------------------
    */
    public function totalCategories(): int
    {
        return $this->category->countCategories();
    }
}