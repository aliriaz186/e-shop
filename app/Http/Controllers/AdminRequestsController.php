<?php

namespace App\Http\Controllers;

use App\CancellationRequests;
use App\Order;
use App\OrderDetail;
use App\RefundRequest;
use App\RequestsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRequestsController extends Controller
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
            if(CancellationRequests::where([['order_id', '=' ,$order->id]])->exists()){
                $orderDetail = OrderDetail::where([['order_id', '=' ,$order->id]])->first();
                $requestData = CancellationRequests::where([['order_id', '=' ,$order->id]])->first();
                $requestData->viewed = 1;
                $requestData->update();
                if ($orderDetail->delivery_status == 'Cancellation Pending' || $orderDetail->delivery_status == 'cancelled'){
                    array_push($orders, $orderDetail);
                }
            }
        }
        foreach ($orders as $order){
            if(RequestsNotification::where(['order_id' => $order->id, 'type' => 'cancel', 'seller_id' => Auth::user()->id])->exists()){
                $notifications = RequestsNotification::where(['order_id' => $order->id, 'type' => 'cancel', 'seller_id' => Auth::user()->id])->get();
                foreach ($notifications as $notification){
                    $notification->delete();
                }
            }
        }
        return view('admin.cancellation_requests', compact('orders'));
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
        foreach ($orders as $order){
            if(RequestsNotification::where(['order_id' => $order->id, 'type' => 'return', 'seller_id' => Auth::user()->id])->exists()){
                $notifications = RequestsNotification::where(['order_id' => $order->id, 'type' => 'return', 'seller_id' => Auth::user()->id])->get();
                foreach ($notifications as $notification){
                    $notification->delete();
                }
            }
        }
        return view('admin.return_requests', compact('orders'));
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
            if(RefundRequest::where([['order_id', '=' ,$order->id]])->exists()){
                $orderDetail = OrderDetail::where([['order_id', '=' ,$order->id]])->first();
                $requestData = RefundRequest::where([['order_id', '=' ,$order->id]])->first();
                $requestData->viewed = 1;
                $requestData->update();
                if ($orderDetail->delivery_status == 'Processing Refund' || explode(" ",$orderDetail->delivery_status)[0] == "refunded"){
                    $flag = false;
                    foreach ($orders as $o){
                        if ($o->id == $orderDetail->order_id){
                            $flag = true;
                        }
                    }
                    if (!$flag){
                        $orderDetail->refund_type = $requestData->refund_type;
                        $orderDetail->refund_amount = $requestData->amount;
                        array_push($orders, $orderDetail);
                    }
                }
            }
        }
        foreach ($orders as $order){
            if(RequestsNotification::where(['order_id' => $order->id, 'type' => 'refund', 'seller_id' => Auth::user()->id])->exists()){
                $notifications = RequestsNotification::where(['order_id' => $order->id, 'type' => 'refund', 'seller_id' => Auth::user()->id])->get();
                foreach ($notifications as $notification){
                    $notification->delete();
                }
            }
        }
        return view('admin.refund_requests', compact('orders'));
    }

    public function adminCancelledOrder(Request $request){
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        if ($request->type=="approve"){
            $orderDetail->delivery_status = 'cancelled';
            $orderDetail->is_accepted_cancellation = 1;
            if (CancellationRequests::where('order_id', $request->order_id)->exists()){
                $cancelRequest = CancellationRequests::where('order_id', $request->order_id)->first();
                $cancelRequest->is_accepted = 1;
                $cancelRequest->update();
            }
        }else if($request->type=="reject"){
            $orderDetail->is_accepted_cancellation = 0;
            $orderDetail->cancellation_request = "";
            $orderDetail->delivery_status = 'pending';
            if (CancellationRequests::where('order_id', $request->order_id)->exists()){
                $cancelRequest = CancellationRequests::where('order_id', $request->order_id)->first();
                $cancelRequest->delete();
            }
        }
        if($orderDetail->update()){
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function adminOrderReturn(Request $request){
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

    public function adminRefundRequest(Request $request){
        $orderDetail = OrderDetail::where('order_id', $request->order_id)->first();
        if ($request->type == "approve"){
            if (empty($request->amount)){
                $request->amount = Order::where('id', $request->order_id)->first()['grand_total'];
            }
            $orderDetail->is_refund_accepted = 1;
            if (empty($orderDetail->refund_request)){
                $orderDetail->refund_request = "refunded";
            }
            if (RefundRequest::where('order_id', $request->order_id)->exists()){
                $refundRequest = RefundRequest::where('order_id', $request->order_id)->first();
                $refundRequest->is_accepted = 1;
                $refundRequest->update();
                $orderDetail->delivery_status = 'refunded -' . $refundRequest->refund_type;
            }else{
                $refundRequest = new RefundRequest();
                $refundRequest->order_id = $request->order_id;
                $refundRequest->amount = $request->amount;
                $refundRequest->is_accepted = 1;
                $refundRequest->refund_type = $request->refund_request;
                $refundRequest->save();
                $orderDetail->delivery_status = 'refunded -' . $refundRequest->refund_type;
            }
        }else if ($request->type == "reject"){
            $orderDetail->refund_request = '';
            $orderDetail->is_refund_accepted = 0;
            $orderDetail->delivery_status = 'shipped';
            if (RefundRequest::where('order_id', $request->order_id)->exists()){
                $refundRequestDelete = RefundRequest::where('order_id', $request->order_id)->first();
                $refundRequestDelete->delete();
            }
        }
        if($orderDetail->update()){
            flash(__('successfully'))->success();
            return redirect()->route($request->incomming);
        }
        flash(__('Something went wrong'))->error();
        return back();
    }
}
