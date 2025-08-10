<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Auth;
use Illuminate\Http\Request;
use App\Enums\PaymentMethod;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchase($id)
    {
        $item = Item::find($id);
        $user = auth()->user();
        $paymentlabels = PaymentMethod::labels();

        return view('purchase', compact('item', 'user', 'paymentlabels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseItems(Request $request, Item $item)
    {
        $user = auth::user();

        $user->buyItems()->create([
            'item_id' => $item->id,
            'shipping_postal_code' => $user->postal_code,
            'shipping_address' => $user->address,
            'shipping_building' => $user->building,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
        ]);

        $item->update(['is_sold' => 1]);

        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
