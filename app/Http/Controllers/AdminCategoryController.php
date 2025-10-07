<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCategoryRequest;
use App\Http\Services\AdminCategoryService;



class AdminCategoryController
{
    protected $adminCategory;

    public function __construct(AdminCategoryService $adminCategory)
    {
        $this->adminCategory = $adminCategory;
    }
    //  create category by admin
    public function createCategory(AdminCategoryRequest $request)
    {
        return $this->adminCategory->addCategories($request->validated());
    }
    //  edit category by admin
    public function editCategory($id,AdminCategoryRequest $request)
    {
        return $this->adminCategory->editCategories($id,$request->validated());
    }  
        //  delete category by admin
        public function deleteCategory($id) 
    {
        return $this->adminCategory->deleteCategories($id);
    }
}
