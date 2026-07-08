<?php

namespace App\Http\Controllers;

use App\Models\DirectoryPlace;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * GET /api/directory/search?q=...
     * Used by the business registration autocomplete — searches the locally
     * stored Trincomalee directory (no external network call, instant).
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DirectoryPlace::where('name', 'like', '%' . $q . '%')
            ->orderByRaw('LENGTH(name) ASC') // shorter/closer matches first
            ->limit(8)
            ->get(['name', 'category', 'address', 'latitude', 'longitude']);

        return response()->json($results);
    }
}
