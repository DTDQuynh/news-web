<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403);

        abort(501, 'The admin page is not implemented yet.');
    }
}
