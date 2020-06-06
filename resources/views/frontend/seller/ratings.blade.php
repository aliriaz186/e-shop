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
                                        {{__('Product Reviews')}}
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{__('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('ratings.seller') }}">{{__('Seller Reviews')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Order history table -->
                        <div class="card no-border mt-4">
                            <div>
                                <table class="table table-sm table-responsive-md">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('Customer')}}</th>
                                        <th>{{__('Rating')}}</th>
                                        <th>{{__('Comment')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($reviews) > 0)
                                        @foreach ($reviews as $key => $value)
                                            @php
                                                $review = \App\SellerFeedback::find($value->id);
                                            @endphp
                                            @if($review != null)
                                                <tr>
                                                    <td>
                                                        {{ $key+1 }}
                                                    </td>
                                                    <td>{{ \App\User::where('id', $review->user_id)->first()['name'] }} ({{ \App\User::where('id', $review->user_id)->first()['email'] }})</td>
                                                    <td>
                                                        <div class="star-rating star-rating-sm mt-1">
                                                            @for ($i=0; $i < floor($review->rating); $i++)
                                                                <i class="fa fa-star active"></i>
                                                            @endfor
                                                            @for ($i=0; $i < ceil(5-$review->rating); $i++)
                                                                <i class="fa fa-star
                                                                        @if($i==0 && ($review->rating - floor($review->rating)) > 0 && ($review->rating - floor($review->rating)) <= 0.5)
                                                                        half
@elseif($i==0 && (ceil($review->rating) - $review->rating) > 0 && (ceil($review->rating) - $review->rating) <= 0.5)
                                                                        active
@endif">
                                                                </i>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td>{{ $review->message }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center pt-5 h4" colspan="100%">
                                                <i class="la la-meh-o d-block heading-1 alpha-5"></i>
                                                <span class="d-block">{{ __('No review found.') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
