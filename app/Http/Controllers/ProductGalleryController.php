<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductGalleryRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ProductGallery;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
          if(request()->ajax())
        {

            $query = ProductGallery::query();

            return DataTables::of($query)
            ->addColumn('action', function($item){
                return '

                    <form class="inline-block" action="'. route('dashboard.gallery.destroy', $item->id) .'" method="POST">
                        <button class="bg-red-500 text-white rounded-md px-2 py-1 mr-2">
                            Hapus
                        </button>
                    '. method_field('delete'). csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('url', function($item){
                return '<img style="max-width 150px" src="'. Storage::url($item->url) .'">';
            })
             ->editColumn('is_featured', function($item){
                return $item->is_featured ? 'yes' : 'no';
            })
            ->rawColumns(['action','url'])
            ->make();
        }
        return view('pages.dashboard.gallery.index', compact('product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Product $product)
    {
        return view('pages.dashboard.gallery.create', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductGalleryRequest $request, Product $product)
    {
        $files = $request->file('files');

        if($request->hasFile('files'))
        {
            foreach ($files as $file) {
                $path = $file->store('public/gallery');

                ProductGallery::create([
                    'product_id' => $product->id,
                    'url' => $path,

                ]);
            }
        }
        toast()->success('Berhasil menambahkan gambar');
        return redirect()->route('dashboard.products.gallery.index', $product->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductGallery $gallery)
    {
        $gallery->delete();
        toast()->success('Berhasil menghapus gambar');
        return redirect()->route('dashboard.products.gallery.index', $gallery->product_id);
    }
}
