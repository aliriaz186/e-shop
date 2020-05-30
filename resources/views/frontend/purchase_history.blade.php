@extends('frontend.layouts.app')

@section('content')

    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if(Auth::user()->user_type == 'seller')
                        @include('frontend.inc.seller_side_nav')
                    @elseif(Auth::user()->user_type == 'customer')
                        @include('frontend.inc.customer_side_nav')
                    @endif
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{__('Purchase History')}}
                                    </h2>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{__('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('purchase_history.index') }}">{{__('Purchase History')}}</a></li>
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
                                            <th>{{__('Code')}}</th>
                                            <th>{{__('Date')}}</th>
                                            <th>{{__('Amount')}}</th>
                                            <th>{{__('Delivery Status')}}</th>
                                            <th>{{__('Payment Status')}}</th>
                                            <th>{{__('Options')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($orders as $key => $order)
                                            <tr>
                                                <td>
                                                    <a href="#{{ $order->code }}" onclick="show_purchase_history_details({{ $order->id }})">{{ $order->code }}</a>
                                                </td>
                                                <td>{{ date('d-m-Y', $order->date) }}</td>
                                                <td>
                                                    {{ single_price($order->grand_total) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $status = $order->orderDetails->first()->delivery_status;
                                                    @endphp
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </td>
                                                <td>
                                                        <span class="badge badge--2 mr-4">
                                                            @if ($order->payment_status == 'paid')
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
                                                            <button onclick="show_purchase_history_details({{ $order->id }})" class="dropdown-item">{{__('Order Details')}}</button>
                                                            <button data-toggle="modal" data-target="#chatModal" onclick="show_chat_modal({{ $order->id }})" class="dropdown-item">{{__('Contact Seller')}}</button>
                                                            @if($order->detail->delivery_status == 'pending' || $order->detail->delivery_status == 'review')
                                                            <button data-toggle="modal" data-target="#cancelRequest" class="dropdown-item" onclick="cancelOrderModal({{$order->id}})">{{__('Cancel Order')}}</button>
                                                             @endif
                                                            @if($order->detail->delivery_status == 'delivered' || $order->detail->delivery_status == 'shipped')
                                                            <button class="dropdown-item" data-toggle="modal" data-target="#returnRequest" onclick="returnOrderModal({{$order->id}})">{{__('Return Request')}}</button>
                                                            @endif
                                                            @if($order->detail->delivery_status == 'delivered' || $order->detail->delivery_status == 'shipped' || $order->detail->delivery_status == 'delivery')
                                                            <button class="dropdown-item" data-toggle="modal" data-target="#refundRequest" onclick="refundOrderModal({{$order->id}})">{{__('Refund Request')}}</button>
                                                            @endif
                                                            <button onclick="show_chat_modal({{ $order->id }})" class="dropdown-item">{{__('Dispute')}}</button>
                                                            <button data-toggle="modal" data-target="#productFeedback"  class="dropdown-item" onclick="product_feedback({{$order->id}})">{{__('Leave Product Review')}}</button>
                                                            <button data-toggle="modal" data-target="#sellerFeedback" class="dropdown-item" onclick="seller_feedback({{$order->id}})">{{__('Leave Seller Feedback')}}</button>

                                                            <a href="{{ route('customer.invoice.download', $order->id) }}" class="dropdown-item">{{__('Download Invoice')}}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
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

    <div class="modal fade" id="returnRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{route('purchase_history.return_product')}}" id="returnRequestform">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Return Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control" required name="return_request" id="returnrequest">
                                <option value="0">--please select--</option>
                                <option value="not-described">Item not as described</option>
                                <option value="incompatible">Incompatible</option>
                                <option value="not-useful">Not useful for intended purpose</option>
                                <option value="damaged">Damaged</option>
                                <option value="wrong-item">Wrong Item Received</option>
                                <option value="others">Others</option>
                            </select>
                            <div class="text-danger" id="returnrequestError" style="display: none">
                                Please select reason!
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Reason</label>
                            <textarea class="form-control" name="return_reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="order_id" id="return-modal-order-id">
                        <input type="hidden" name="status" value="return requested">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{route('purchase_history.cancel')}}" id="cancel-order-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Order Cancellation Request</h5>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="refundRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{route('purchase_history.refund')}}" id="refund-order-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Order Refund Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="exampleFormControlTextarea1">Reason for Refund *</label>
                        <div class="form-group">
                            <select class="form-control" required name="refund_request" id="refundrequest">
                                <option value="0">--please select--</option>
                                <option value="duplicate-order">Duplicate Order</option>
                                <option value="order-mistake">Ordered By Mistake</option>
                                <option value="no-longer">No Longer Needed</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="text-danger" id="refundrequestError" style="display: none">
                            Please select reason!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="order_id" id="refund-modal-order-id">
                        <input type="hidden" name="status" value="refund requested">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="sellerFeedback" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Leave Seller Feedback</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('purchase_history.seller_feedback')}}" id="refund-order-form">
                    @csrf
                <div class="modal-body">
                        <div class="form-group">
                            <div class="form-group">
                                <input type="hidden" id="order-id-feedback" name="order_id">
                                <label for="exampleFormControlTextarea1"></label>
                                <textarea class="form-control" name="message" rows="4" placeholder="{{__('Enter Seller Review & Feedback')}}"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="productFeedback" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Leave Product Feedback</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('purchase_history.product_feedback')}}" id="refund-order-form">
                    @csrf
                <div class="modal-body">
                        <div class="form-group">
                            <div class="form-group">
                                <input type="hidden" id="order-id-pro-feedback" name="order_id">
                                <label for="exampleFormControlTextarea1"></label>
                                <textarea class="form-control" name="message" rows="4" placeholder="{{__('Enter Product Review & Feedback')}}"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
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

    <div class="modal" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title strong-600 heading-5">{{__('Contact Seller')}}</h5>
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

@endsection
@section('script')
    <script type="text/javascript">
        function show_chat_modal(code){
            document.getElementById("product-code-con").value = code;
        }

        function seller_feedback(id){
             document.getElementById("order-id-feedback").value = id;
        }
        function product_feedback(id){
             document.getElementById("order-id-pro-feedback").value = id;
        }
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
        function cancelOrderModal(id) {
            document.getElementById('can-modal-order-id').value = id;
        }

        function returnOrderModal(id) {
            document.getElementById('return-modal-order-id').value = id;
        }

        function refundOrderModal(id) {
            document.getElementById('refund-modal-order-id').value = id;
        }

        $('#cancel-order-form').submit(function(){
            document.getElementById('cancellationrequestError').style.display = "none";
            if (document.getElementById('cancellationrequest').value === "0" ||document.getElementById('cancellationrequest').value === 0 || document.getElementById('cancellationrequest').value === '') {
                document.getElementById('cancellationrequestError').style.display = "inline";
                return false;
            }
        });

        $('#returnRequestform').submit(function(){
            document.getElementById('returnrequestError').style.display = "none";
            if (document.getElementById('returnrequest').value === "0" ||document.getElementById('returnrequest').value === 0 || document.getElementById('returnrequest').value === '') {
                document.getElementById('returnrequestError').style.display = "inline";
                return false;
            }
        });

        $('#refund-order-form').submit(function(){
            document.getElementById('refundrequestError').style.display = "none";
            if (document.getElementById('refundrequest').value === "0" ||document.getElementById('returnrequest').value === 0 || document.getElementById('returnrequest').value === '') {
                document.getElementById('refundrequestError').style.display = "inline";
                return false;
            }
        });
    </script>

@endsection
