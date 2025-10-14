<?php

namespace App\Http\Controllers;

use App\Http\Services\AdminUserBlockService;
use Illuminate\Http\Request;

class AdminUserBlockController extends Controller
{
    protected $block;

    public function __construct(AdminUserBlockService $block)
    {
     $this->block = $block;   
    }

    public function blockUser($id)
    {
        return $this->block->userBlock($id);
    }

    public function unblockUser($id)
    {
        return $this->block->UserUnblock($id);
    }
}
