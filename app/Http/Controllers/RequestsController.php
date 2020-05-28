<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use App\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestsController extends Controller
{
    public function cancellationRequests(){
        $orders = [];
        $ordersList = Order::where([['id', '>' ,0]])->orderBy('code', 'DESC')->get();
        foreach ($ordersList as $order){
            if (OrderDetail::where([['order_id', '=' ,$order->id],['seller_id', '=' ,Auth::user()->id]])->exists()){
               $orderDetail = OrderDetail::where([['order_id', '=' ,$order->id],['seller_id', '=' ,Auth::user()->id]])->first();
               if (!empty($orderDetail->cancellation_request)){
                   array_push($orders, $order);
               }
            }
        }
        return view('frontend.seller.cancellation_requests', compact('orders'));
    }

    public function returnRequests(){
        $orders = [];
        $ordersList = Order::where([['id', '>' ,0]])->orderBy('code', 'DESC')->get();
        foreach ($ordersList as $order){
            if (OrderDetail::where([['order_id', '=' ,$order->id],['seller_id', '=' ,Auth::user()->id]])->exists()){
               $orderDetail = OrderDetail::where([['order_id', '=' ,$order->id],['seller_id', '=' ,Auth::user()->id]])->first();
               if (!empty($orderDetail->return_request)){
                   array_push($orders, $order);
               }
            }
        }
        return view('frontend.seller.return_requests', compact('orders'));
    }

    public function refundRequests(){
        $orders = [];
        $ordersList = Order::where([['id', '>' ,0]])->orderBy('code', 'DESC')->get();
        foreach ($ordersList as $order){
            if (OrderDetail::where([['order_id', '=' ,$order->id],['seller_id', '=' ,Auth::user()->id]])->exists()){
               $orderDetail = OrderDetail::where([['order_id', '=' ,$order->id],['seller_id', '=' ,Auth::user()->id]])->first();
               if (!empty($orderDetail->refund_request)){
                   array_push($orders, $order);
               }
            }
        }
        return view('frontend.seller.refund_requests', compact('orders'));
    }

    public function sellerRefund(Request $request){
        if (empty($request->amount)){
            $request->amount = 0;
        }
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        $orderDetail->delivery_status = 'refund requested';
        $orderDetail->update();
        $order = new RefundRequest();
        $order->order_id = $request->order_id;
        $order->amount = $request->amount;
        $order->refund_type = $request->refund_request;
        if($order->save()){
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function sellerRefundRequest(Request $request){
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        if ($request->type == "approve"){
            if (empty($request->amount)){
                $request->amount = Order::where('id', $request->order_id)->first()['grand_total'];
            }
            $orderDetail->delivery_status = 'refunded';
            $orderDetail->is_refund_accepted = 1;
            if (empty($orderDetail->refund_request)){
                $orderDetail->refund_request = "refunded";
            }
            $refundRequest = new RefundRequest();
            $refundRequest->order_id = $request->order_id;
            $refundRequest->amount = $request->amount;
            $refundRequest->is_accepted = 0;
            $refundRequest->refund_type = $request->refund_request;
            $refundRequest->save();
        }else if ($request->type == "reject"){
            $orderDetail->refund_request = '';
            $orderDetail->is_refund_accepted = 0;
        }
        if($orderDetail->update()){
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function cancelledBySeller(Request $request){
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        if ($request->type=="approve"){
            $orderDetail->delivery_status = 'cancelled';
            $orderDetail->is_accepted_cancellation = 1;
        }else if($request->type=="reject"){
            $orderDetail->is_accepted_cancellation = 0;
            $orderDetail->cancellation_request = "";
        }
        if($orderDetail->update()){
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function sellerReturn(Request $request){
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        if ($request->type=="approve"){
            $orderDetail->delivery_status = 'returned';
            $orderDetail->is_accepted_return = 1;
        }else if($request->type=="reject"){
            $orderDetail->is_accepted_return = 0;
            $orderDetail->return_request = "";
            $orderDetail->return_reason = "";
        }
        if($orderDetail->update()){
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function approvedCancelBySeller(Request $request){
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        $orderDetail->delivery_status = 'cancelled';
        $orderDetail->is_accepted_cancellation = 1;
        $orderDetail->cancellation_request = $request->cancellation_request;
        if ($orderDetail->update()) {
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

}
