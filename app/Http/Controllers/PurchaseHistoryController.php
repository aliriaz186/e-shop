<?php

namespace App\Http\Controllers;

use App\Product;
use App\RequestsNotification;
use App\Review;
use App\SellerFeedback;
use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use Auth;

class PurchaseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('code', 'desc')->paginate(9);
        foreach ($orders as $order){
            $order->detail = OrderDetail::where('order_id', $order->id)->first();
        }
        return view('frontend.purchase_history', compact('orders'));
    }

    public function purchase_history_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = 1;
        $order->payment_status_viewed = 1;
        $order->save();
        return view('frontend.partials.order_details_customer', compact('order'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    public function returnProduct(Request $request)
    {
        $order_details = OrderDetail::findOrFail($request->order_id);
        $order_details->return_request= $request->return_request;
        $order_details->return_reason = $request->return_reason;
        $notification = new RequestsNotification();
        $notification->order_id = $request->order_id;
        $notification->seller_id = $order_details->seller_id;
        $notification->type = "return";
        $notification->save();
        if($order_details->update()){
            flash(__('successfully'))->success();
            return redirect()->route('purchase_history.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function cancel(Request $request)
    {
        $order_details = OrderDetail::findOrFail($request->order_id);
        $order_details->cancellation_request = $request->cancellation_request;
        $notification = new RequestsNotification();
        $notification->order_id = $request->order_id;
        $notification->seller_id = $order_details->seller_id;
        $notification->type = "cancel";
        $notification->save();
        if($order_details->update()){
            flash(__('successfully'))->success();
            return redirect()->route('purchase_history.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function refundRequest(Request $request)
    {
        $order_details = OrderDetail::findOrFail($request->order_id);
        $order_details->refund_request = $request->refund_request;
        $notification = new RequestsNotification();
        $notification->order_id = $request->order_id;
        $notification->seller_id = $order_details->seller_id;
        $notification->type = "refund";
        $notification->save();
        if($order_details->update()){
            flash(__('successfully'))->success();
            return redirect()->route('purchase_history.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function return_request()
    {
        $orders = OrderDetail::leftJoin('orders', function($join) {
            $join->on('orders.id', '=', 'order_details.order_id');
        })->where('order_details.seller_id', Auth::user()->id)
            ->where('order_details.return_request', '!=', '0')
            ->orderBy('orders.created_at', 'desc')->paginate(9);
        return view('frontend.return_request', compact('orders'));
    }

    public function cancellation_request()
    {
        //
        $orders = OrderDetail::leftJoin('orders', function($join) {
            $join->on('orders.id', '=', 'order_details.order_id');
        })->where('order_details.seller_id', Auth::user()->id)
            ->where('order_details.cancellation_request', '!=', '')
            ->orderBy('orders.created_at', 'desc')->paginate(9);
        return view('frontend.cancellation_request', compact('orders'));
    }

    public function sellerFeedback(Request $request){
        $feedback = new SellerFeedback();
        $feedback->user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $feedback->order_id = $request->order_id;
        $feedback->rating = $request->rating;
        $feedback->message = $request->message;
        if($feedback->save()){
            flash(__('successfully'))->success();
            return redirect()->route('purchase_history.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function productFeedback(Request $request){
        $feedback = new Review();
        $feedback->user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $feedback->product_id = OrderDetail::where('order_id', $request->order_id)->first()['product_id'];
        $feedback->comment = $request->message;
        $feedback->rating = $request->rating;
        if($feedback->save()){
            flash(__('successfully'))->success();
            return redirect()->route('purchase_history.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }


}
