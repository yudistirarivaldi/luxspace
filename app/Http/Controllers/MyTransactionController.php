<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\TransactionItem;
use App\Models\Product;

class MyTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
   {
         {
        if(request()->ajax())
        {

            $query = Transaction::with(['user'])->where('users_id', Auth::user()->id);

            return DataTables::of($query)
            ->addColumn('action', function($item){
                return '
                    <a href="'. route('dashboard.my-transaction.show', $item->id) .'" class="bg-gray-800 text-white rounded-md px-2 py-1 mr-2">
                        Detail
                    </a>


                ';
            })
            ->editColumn('total_price', function($item){
                return number_format($item->total_price);
            })
            ->rawColumns(['action'])
            ->make();
        }
        return view('pages.dashboard.transaction.index');
    }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $myTransaction)
     {
       if(request()->ajax())
       {
            $query = TransactionItem::with(['product'])->where('transactions_id', $myTransaction->id);

            return DataTables::of($query)
                // masuk ke relasi product price
                ->editColumn('product.price', function($item){
                    return number_format($item->product->price);
                })

                ->make();
       }

         return view('pages.dashboard.transaction.show', [
            'transaction' => $myTransaction,
         ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return abort(404);
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
        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return abort(404);
    }
}
