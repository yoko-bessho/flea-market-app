<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\PaymentMethod;
use App\Models\Purchase;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Services\StripeSessionWrapper;



class PurchaseController extends Controller
{

    public function purchase(Item $item)
    {
        $user = auth()->user();
        $paymentlabels = PaymentMethod::labels();

        if ($user->postal_code == null || $user->address == null )
        {
            return redirect('/mypage/profile');
        }

        return view('purchase', compact('item', 'user', 'paymentlabels'));
    }





    protected StripeSessionWrapper $stripe;

    public function __construct(StripeSessionWrapper $stripe)
    {
        $this->stripe = $stripe;
    }


    public function checkout(Request $request)
    {
        if (!app()->environment('testing')) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
        }

        $item = Item::findOrFail($request->item_id);
        $user = Auth::user();

        $checkout_session = $this->stripe->create([
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->title,
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'item_id' => $item->id,
                'buyer_id' => Auth::id(),
            ],
            'success_url' => route('success'),
            'cancel_url' => route('cancel'),
            ]);

            Purchase::create([
                'item_id' => $item->id,
                'buyer_id' => Auth::id(),
                'shipping_postal_code' => $user->postal_code,
                'shipping_address' => $user->address,
                'shipping_building' => $user->building,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'checkout_session_id' => $checkout_session->id,
            ]);

            return redirect($checkout_session->url);
    }



    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            if (app()->environment('testing')) {
                $eventArray = json_decode($payload, true);
                $event = json_decode(json_encode($eventArray));
            } else {
                $event = Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $endpointSecret
                );
            }
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            return response('Invalid payload', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $purchase = Purchase::where('checkout_session_id', $session->id)->first();

            if ($purchase && $purchase->payment_status !== 'succeeded') {
                $purchase->update([
                    'payment_status' => 'success',
                    'paid_at' => now(),
                ]);

                $item = Item::find($session->metadata->item_id);
                if ($item && !$item->is_sold) {
                    $item->update(['is_sold' => true]);
                }
            }

        }
        return response()->noContent();
    }





    public function success()
    {
        return redirect('/');
    }

    public function cancel()
    {
        return redirect('/');
    }


    public function addressEdit(Item $item)
    {
        $user = auth()->user();
        return view('change-address', compact('item', 'user'));
    }

    public function addressUpdate(Request $request, Item $item)
    {
        $user = Auth::user();
        $userData = $request->only(['postal_code', 'address', 'building']);
        $user->update($userData);

        return redirect()->route('purchase', ['item' => $item]);
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
