<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryBulkImportRequest;
use App\Http\Services\CategoryBulkImport;
use App\Http\Services\CategoryBulkImportService;

class CategoryBulkImportController extends Controller
{
    protected $category;
    public function __construct(CategoryBulkImportService $category)
    {
        $this->category = $category;
    }

    public function importCsv(CategoryBulkImportRequest $request)
    {
        return $this->category->categoryImport($request->validated());
    }
}
