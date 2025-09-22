<?php

namespace App\Http\Controllers;

use App\Http\Services\AdminUserService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $users;
    public function __construct(AdminUserService $users)
    {
        $this->users = $users;
    }
    public function fetchUser()
    {
        return $this->users->listUsers();
    }

public function userDelete($id)
{
    return $this->users->deleteUsers($id);
}
}
