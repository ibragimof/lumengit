<?php

namespace App\Http\Controllers;

use App\Services\Git;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function current(Request $request)
    {
        $repository = $request->get('repository', null);
        return Git::branchCurrent($repository);
    }

    public function list(Request $request)
    {
        $repository = $request->get('repository', null);
        return Git::branchList($repository);
    }


}
