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
                                        {{__('Orders')}}
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{__('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('orders.index') }}">{{__('Orders')}}</a></li>
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
                                            <th>{{__('Options')}}</th>
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
                                                        <div class="dropdown">
                                                            <button class="btn" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>

                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                                                <button onclick="show_order_details({{ $order->id }})" class="dropdown-item">{{__('Order Details')}}</button>
                                                                <button onclick="show_chat_modal({{ $order->id }})" class="dropdown-item">{{__('Contact Buyer')}}</button>
                                                                <button onclick="show_chat_modal({{ $order->links }})" class="dropdown-item">{{__('Request Feedback')}}</button>
                                                                <button data-toggle="modal" data-target="#cancelRequest" class="dropdown-item">{{__('Cancel Order')}}</button>
                                                                @if(empty($order->refund_requests->first()))
                                                                <button data-toggle="modal" data-target="#refundOrder" onclick="refundOrderModal({{$order->id}})" class="dropdown-item">{{__('Refund Order')}}</button>
                                                                @endif
                                                                    <button onclick="show_chat_modal({{ $order->id }})" class="dropdown-item">{{__('Ship Order')}}</button>
                                                                <a href="{{ route('seller.invoice.download', $order->id) }}" class="dropdown-item">{{__('Download Invoice')}}</a>
                                                            </div>
                                                        </div>
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
                                {{ $orders->links() }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="cancelRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Order Cancellation Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="exampleFormControlTextarea1">Reason for Cancellation *</label>
                    <form method="post" action="#">
                        <div class="form-group">
                            <select class="form-control" required>
                                <option value="0">--please select--</option>
                                <option value="duplicate-order">Duplicate Order</option>
                                <option value="order-mistake">Customer Ordered By Mistake</option>
                                <option value="no-longer">No Longer Needed By Customer</option>
                                <option value="incomplete-address">Incomplete Details</option>
                                <option value="undeliverable">Undeliverable Shipping Address</option>
                                <option value="product-stock">Product Out of Stock</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="refundOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Refund Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('orders.seller_refund')}}" id="refund-order-form">
                    @csrf
                <div class="modal-body">
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
                <div class="modal-footer">
                    <input type="hidden" name="order_id" id="refund-modal-order-id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
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
    function refundOrderModal(id) {
        console.log(document.getElementById('refundAmount').value);
        document.getElementById('refund-modal-order-id').value = id;
    }
    $('#refund-order-form').submit(function(){
        document.getElementById('refundrequestError').style.display = "none";
        document.getElementById('refundrequestAmountError').style.display = "none";
        if (document.getElementById('refundrequest').value === "0" ||document.getElementById('returnrequest').value === 0 || document.getElementById('returnrequest').value === '') {
            document.getElementById('refundrequestError').style.display = "inline";
            return false;
        }
        console.log(document.getElementById('refundAmount').value);
        if (document.getElementById('refundAmount').value === "" || document.getElementById('refundAmount').value === '' || document.getElementById('refundAmount').value === undefined || document.getElementById('refundAmount').value === 'undefined') {
            document.getElementById('refundrequestAmountError').style.display = "inline";
            return false;
        }
    });
</script>
@endsection
