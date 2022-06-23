<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Midtrans\Config;
use Midtrans\Snap;

class FrontendController extends Controller
{
    public function index(Request $request)
    {

        $products = Product::with(['galleries'])->latest()->get();

        return view('pages.frontend.index', compact('products'));
    }

    public function details(Request $request, $slug)
    {

        $product = Product::with(['galleries'])->where('slug', $slug)->firstOrFail();
        $recomendations = Product::with(['galleries'])->inRandomOrder()->limit(4)->get();

        return view('pages.frontend.details', compact('product', 'recomendations'));
    }

    public function cartAdd(Request $request, $id)
    {
        Cart::create([
            'users_id' => Auth::user()->id,
            'products_id' => $id,
        ]);

        return redirect('carts');

    }

    public function cartDelete(Request $request, $id)
    {
        $item = Cart::findOrFail($id);

        $item->delete();

        return redirect('carts');
    }

    public function carts(Request $request)
    {
        // realtion bersarang yaitu ngambil data product dan gallery
        $carts = Cart::with(['product.galleries'])->where('users_id', Auth::user()->id)->get();

        return view('pages.frontend.carts', compact('carts'));
    }


    public function success(Request $request)
    {
        return view('pages.frontend.success');
    }

    public function checkout(CheckoutRequest $request)
    {
        $data = $request->all();

        // ambil dulu data cart
        $carts = Cart::with(['product'])->where('users_id', Auth::user()->id)->get();

        // baru masukan ke transaksi
        $data['users_id'] = Auth::user()->id;
        $data['total_price'] = $carts->sum('product.price');

        // buat data transaksi
        $transaction = Transaction::create($data);

        // buat transaksi item
        foreach ($carts as $cart) {
            $items[] = TransactionItem::create([
                'transactions_id' => $transaction->id,
                'users_id' => $cart->users_id,
                'products_id' => $cart->products_id,
            ]);
        }

        // hapus data cart habis transaksi
        Cart::where('users_id', Auth::user()->id)->delete();

        // konfigurasi midtrans
        // ngambil dari services.php
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Setup variable midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => 'TRIXIE-' . $transaction->id,
                'gross_amount' => (int) $transaction->total_price
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email,
            ],
            'enabled_payments' => ['gopay', 'bank_transfer'],
            'vtweb' => []
        ];

        // payment proses
        try {
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;

            $transaction->payment_url = $paymentUrl;
            $transaction->save();

            return redirect($paymentUrl);

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}
