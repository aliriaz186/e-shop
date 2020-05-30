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
                                                                <button onclick="show_chat_modal({{ $order->id }})" class="dropdown-item" data-toggle="modal" data-target="#chatModal">{{__('Contact Buyer')}}</button>
                                                                <button onclick="show_chat_modal({{ $order->links }})" class="dropdown-item">{{__('Request Feedback')}}</button>
                                                                @if($order->orderDetails->where('seller_id', Auth::user()->id)->first()->delivery_status == 'pending' || $order->orderDetails->where('seller_id', Auth::user()->id)->first()->delivery_status == 'review')
                                                                <button data-toggle="modal" data-target="#cancelRequest" class="dropdown-item" onclick="cancelOrderBySeller({{$order->id}})">{{__('Cancel Order')}}</button>
                                                                @endif
                                                                <button data-toggle="modal" data-target="#refundOrderRequest" onclick="refundOrderApproveModal({{$order->id}})" class="dropdown-item">{{__('Refund Order')}}</button>
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
            <form method="post" action="{{route('orders.seller_approved_cancel')}}" id="cancel-order-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Order Cancellation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="exampleFormControlTextarea1">Reason for Cancellation *</label>
                        <div class="form-group">
                            <select class="form-control" required name="cancellation_request" id="cancellationrequest">
                                <option value="0">--please select--</option>
                                <option value="duplicate-order">Duplicate Order</option>
                                <option value="order-mistake">Ordered By Mistake</option>
                                <option value="no-longer">No Longer Needed</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="text-danger" id="cancellationrequestError" style="display: none">
                            Please select reason!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="order_id" id="can-modal-order-id">
                        <input type="hidden" name="status" value="cancelled">
                        <input type="hidden" name="incomming" value="orders.index">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                        <input type="hidden" name="incomming" value="orders.index">
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
    <div class="modal" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title strong-600 heading-5">{{__('Contact Buyer')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3" name="title" placeholder="Order Id" id="product-code-con" required readonly>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required placeholder="Your Question"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{__('Cancel')}}</button>
                        <button type="submit" class="btn btn-base-1 btn-styled">{{__('Send')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
    function show_chat_modal(code){
        document.getElementById("product-code-con").value = code;
    }
    function cancelOrderBySeller(id) {
        document.getElementById('can-modal-order-id').value = id;
    }
    $('#cancel-order-form').submit(function(){
        document.getElementById('cancellationrequestError').style.display = "none";
        if (document.getElementById('cancellationrequest').value === "0" ||document.getElementById('cancellationrequest').value === 0 || document.getElementById('cancellationrequest').value === '') {
            document.getElementById('cancellationrequestError').style.display = "inline";
            return false;
        }
    });
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
    function refundOrderApproveModal(id) {
        document.getElementById('refund-modal-order-id').value = id;
        document.getElementById('refund-type-id').value = "approve";
        document.getElementById('refund-type').innerHTML = "";
        document.getElementById('isApproveType').style.display = 'inline';
    }
</script>
@endsection
