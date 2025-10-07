<?php

namespace App\Http\Controllers;

use App\Http\Services\SearchAndFilterService;
use Illuminate\Http\Request;

class SearchAndFilterController extends Controller
{
    protected $search;
    public function __construct(SearchAndFilterService $search)
    {
        $this->search = $search;
    }

    public function searchFilter(Request $request)
    {
        return $this->search->search($request);
    }

    public function nameFilter(Request $request)
    {
        return $this->search->filterByName($request);
    }
}
