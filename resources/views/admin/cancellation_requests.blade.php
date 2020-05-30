@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{__('Cancellation Requests')}}</h3>
            <div class="pull-right clearfix">
            </div>
        </div>
    <div class="panel-body">
        @if (count($orders) == 0)
            <h4 class="text-center">No Data Found</h4>
        @endif
    @if (count($orders) > 0)
        <!-- Order history table -->
                <div>
                    <table class="table table-striped res-table mar-no" >
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('Order Code')}}</th>
                            <th>{{__('Num. of Products')}}</th>
                            <th>{{__('Customer')}}</th>
                            <th>{{__('Amount')}}</th>
                            <th>{{__('Delivery Status')}}</th>
                            <th>{{__('Payment Status')}}</th>
                            <th style="width: 20%">{{__('Options')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $key => $order_id)
                            @php
                                $order = \App\Order::find($order_id->id);
                            @endphp
                            @if($order != null)
                                <tr>
                                    <td>
                                        {{ $key+1}}
                                    </td>
                                    <td>
                                        <a href="#{{ $order->code }}" onclick="show_order_details({{ $order->id }})">{{ $order->code }}</a>
                                    </td>
                                    <td>
                                        {{ count($order->orderDetails->where('order_id', $order->id)) }}
                                    </td>
                                    <td>
                                        @if ($order->user_id != null)
                                            {{ $order->user->name }}
                                        @else
                                            Guest ({{ $order->guest_id }})
                                        @endif
                                    </td>
                                    <td>
                                        {{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price')) }}
                                    </td>
                                    <td>
                                        @php
                                            $status = $order->orderDetails->first()->delivery_status;
                                        @endphp
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </td>
                                    <td>
                                                            <span class="badge badge--2 mr-4">
                                                                @if ($order->orderDetails->where('order_id', $order->id)->first()->payment_status == 'paid')
                                                                    <i class="bg-green"></i> {{__('Paid')}}
                                                                @else
                                                                    <i class="bg-red"></i> {{__('Unpaid')}}
                                                                @endif
                                                            </span>
                                    </td>
                                    <td>
                                        @if($order->orderDetails->where('order_id', $order->id)->first()->is_accepted_cancellation == 0 || $order->orderDetails->where('order_id', $order->id)->first()->is_accepted_cancellation == '0')
                                            <button class="btn btn-success btn-sm" onclick="cancelOrderApproveModal({{$order->id}})" data-toggle="modal" data-target="#cancelOrderRequest">Accept</button>
                                            <button class="btn btn-danger btn-sm ml-2" onclick="cancelOrderRejectModal({{$order->id}})" data-toggle="modal" data-target="#cancelOrderRequest">Reject</button>
                                        @endif
                                        @if($order->orderDetails->where('order_id', $order->id)->first()->is_accepted_cancellation == 1 || $order->orderDetails->where('order_id', $order->id)->first()->is_accepted_cancellation == '1')
                                            <div style="background: green; padding: 5px; border-radius: 5px; color: white; width: 75px;">Approved</div>
                                        @endif

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
        @endif
            </div>
    </div>

    <div class="modal fade" id="cancelOrderRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('admin.admin_cancel')}}">
                    @csrf
                    <div class="modal-body">
                        <h6 id="cancel-type"></h6>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="order_id" id="cancel-modal-order-id">
                        <input type="hidden" name="incomming" value="admin.cancellation_requests">
                        <input type="hidden" name="type" id="cancel-type-id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>
    <script>
        function cancelOrderApproveModal(id) {
            document.getElementById('cancel-modal-order-id').value = id;
            document.getElementById('cancel-type-id').value = "approve";
            document.getElementById('cancel-type').innerHTML = "Are you sure you want to approve the request?"
        }

        function cancelOrderRejectModal(id) {
            document.getElementById('cancel-modal-order-id').value = id;
            document.getElementById('cancel-type-id').value = "reject";
            document.getElementById('cancel-type').innerHTML = "Are you sure you want to reject the request?"

        }
    </script>
@endsection
