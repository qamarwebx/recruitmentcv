<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Backs the existing navbar search input (resources/views/layout/admin/
 * admin_layout.blade.php's `.search-input`) with real, permission-scoped
 * results instead of the theme's static demo JSON. See GlobalSearchService
 * for how the module list and record queries are derived.
 */
class GlobalSearchController extends Controller
{
    public function __construct(protected GlobalSearchService $searchService)
    {
    }

    public function search(Request $request)
    {
        $user = Auth::guard('admin')->user();

        return response()->json(
            $this->searchService->search($user, (string) $request->query('q', ''))
        );
    }
}
