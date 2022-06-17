<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(Request $request)
    {

        $products = Product::with(['galleries'])->latest()->get();

        return view('pages.frontend.index', compact('products'));
    }

    public function details(Request $request, $slug)
    {
        return view('pages.frontend.details');
    }

    public function carts(Request $request)
    {
        return view('pages.frontend.carts');
    }

    public function success(Request $request)
    {
        return view('pages.frontend.success');
    }

}
