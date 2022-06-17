<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
     {
        if(request()->ajax())
        {

            $query = Product::query();

            return DataTables::of($query)
            ->addColumn('action', function($item){
                return '
                    <a href="'. route('dashboard.products.gallery.index', $item->id) .'" class="bg-gray-800 text-white rounded-md px-2 py-1 mr-2">
                        Gallery
                    </a>
                    <a href="'. route('dashboard.products.edit', $item->id) .'" class="bg-gray-500 text-white rounded-md px-2 py-1 mr-2">
                        Edit
                    </a>
                    <form class="inline-block" action="'. route('dashboard.products.destroy', $item->id) .'" method="POST">
                        <button class="bg-red-500 text-white rounded-md px-2 py-1 mr-2">
                            Hapus
                        </button>
                    '. method_field('delete'). csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('price', function($item){
                return number_format($item->price);
            })
            ->rawColumns(['action'])
            ->make();
        }
        return view('pages.dashboard.product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.dashboard.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // cek data masuk atau gk nya
        // return $request->all();

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        Product::create($data);

        toast('Success Input Data Product','success');

        return redirect()->route('dashboard.products.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('pages.dashboard.product.edit', compact('product'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $product->update($data);

        toast('Success Update Data Product','success');
        return redirect()->route('dashboard.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        toast('Success Delete Data Product','success');
        return redirect()->route('dashboard.products.index');
    }
}
