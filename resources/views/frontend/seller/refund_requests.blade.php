@extends('frontend.layouts.app')

@section('content')

    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @include('frontend.inc.seller_side_nav')
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{__('Refund Requests')}}
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{__('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('orders.refund_requests') }}">{{__('Refund Requests')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @if (count($orders) > 0)
                        <!-- Order history table -->
                            <div class="card no-border mt-4">
                                <div>
                                    <table class="table table-sm table-hover table-responsive-md">
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
                                                        {{ count($order->orderDetails->where('seller_id', Auth::user()->id)) }}
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
                                                                @if ($order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status == 'paid')
                                                                    <i class="bg-green"></i> {{__('Paid')}}
                                                                @else
                                                                    <i class="bg-red"></i> {{__('Unpaid')}}
                                                                @endif
                                                            </span>
                                                    </td>
                                                    <td>
                                                        @if($order->orderDetails->where('seller_id', Auth::user()->id)->first()->is_refund_accepted == 0 || $order->orderDetails->where('seller_id', Auth::user()->id)->first()->is_refund_accepted == '0')
                                                            <button class="btn btn-success btn-sm" onclick="refundOrderApproveModal({{$order->id}})" data-toggle="modal" data-target="#refundOrderRequest">Accept</button>
                                                            <button class="btn btn-danger btn-sm ml-2" onclick="refundOrderRejectModal({{$order->id}})" data-toggle="modal" data-target="#refundOrderRequest">Reject</button>
                                                        @endif
                                                        @if($order->orderDetails->where('seller_id', Auth::user()->id)->first()->is_refund_accepted == 1 || $order->orderDetails->where('seller_id', Auth::user()->id)->first()->is_refund_accepted == '1')
                                                            <div style="background: green; padding: 5px; border-radius: 5px; color: white; width: 75px;">Approved</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="pagination-wrapper py-4">
                            <ul class="pagination justify-content-end">
                                {{--                                {{ $orders->links() }}--}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="refundOrderRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Refund Comfirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('orders.seller_refund_request')}}">
                    @csrf
                    <div class="modal-body">
                        <h6 id="refund-type"></h6>
                        <div id="isApproveType" style="display: none">
                            <div class="form-group">
                                <select class="form-control" required name="refund_request" id="refundrequest">
                                    <option value="0">--please select--</option>
                                    <option value="full-refund">Full Refund</option>
                                    <option value="partial-refund">Partial Refund</option>
                                </select>
                                <div class="text-danger" id="refundrequestError" style="display: none">
                                    Please select option!
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Amount to Refund (partial refund only)</label>
                                <input name="amount" id="refundAmount" type="text" class="form-control mb-3" sum="sum" placeholder="{{__('Enter Amount (ex £9.99)')}}">
                                <div class="text-danger" id="refundrequestAmountError" style="display: none">
                                    Please enter amount!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="order_id" id="refund-modal-order-id">
                        <input type="hidden" name="incomming" value="orders.refund_requests">
                        <input type="hidden" name="type" id="refund-type-id">
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
        function refundOrderApproveModal(id) {
            document.getElementById('refund-modal-order-id').value = id;
            document.getElementById('refund-type-id').value = "approve";
            document.getElementById('refund-type').innerHTML = "";
            document.getElementById('isApproveType').style.display = 'inline';
        }

        function refundOrderRejectModal(id) {
            document.getElementById('refund-modal-order-id').value = id;
            document.getElementById('refund-type-id').value = "reject";
            document.getElementById('refund-type').innerHTML = "Are you sure you want to reject the request?"

        }
    </script>
@endsection
