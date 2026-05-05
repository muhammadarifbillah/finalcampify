<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class PembeliCategoryController extends Controller
{


    public function show($slug)
    {
        return view('pembeli.category.index_pembeli', compact('slug'));
    }


}
