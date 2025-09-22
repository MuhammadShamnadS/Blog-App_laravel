<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminTagRequest;
use App\Http\Services\AdminTagService;
use Illuminate\Http\Request;

class AdminTagController extends Controller
{

    protected $adminTag;
    public function __construct(AdminTagService $adminTag)
    {
        $this->adminTag = $adminTag;
    }

    // add tags under a category by admin
    public function addTags(AdminTagRequest $request, $id)
    {
        return $this->adminTag->addTags($request->validated(), $id);
    }

    //  list tags under a category by admin
    public function listTags($id)
    {
        return $this->adminTag->fetchTags($id);
    }

    //  edit tags by admin
        public function editTag($id, AdminTagRequest $request)
    {
        return $this->adminTag->editTags($request->validated(), $id);
    }

    //  delete tags by admin
    public function deleteTag($id)
    {
        return $this->adminTag->deleteTag($id);
    }
}
