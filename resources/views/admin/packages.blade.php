@extends('layouts.app')

@section('content')
    <div>
        <div class="bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{__('Classified Packages')}}</h3>
            <a class="btn btn-rounded btn-info pull-right" href="{{ route('admin.add_package') }}">Add New Package</a>
            <div class="pull-right clearfix">
            </div>
        </div>
        <div>
            @if (count($packages) == 0)
                <h4 class="text-center">No Data Found</h4>
            @endif
            @if (count($packages) > 0)
            <!-- Order history table -->
                <div class="d-flex flex-wrap" style="margin-top: 10px">
                        @foreach ($packages as $key => $package)
                            <div style="padding: 40px; background: white; margin-left: 10px">
                                <center><img  src="{{ asset('packages/' . $package->logo) }}" alt="Card image" style="width: 100px;height: 100px;"></center>
                                <h4 class="text-center">{{$package->name}}</h4>
                                <h3 class="text-center">{{single_price($package->price)}}</h3>
                                <h6 class="text-center">Product Upload {{$package->product_upload}}</h6>
                                <a class="btn btn-primary btn-sm" href="{{URL::to('')}}/admin/edit_package/{{$package->id}}">Edit</a>
                                <a class="btn btn-danger btn-sm" href="{{URL::to('')}}/admin/delete_package/{{$package->id}}">Delete</a>
                            </div>
                        @endforeach
                </div>
            @endif
        </div>
    </div>
    <script>
    </script>
@endsection
