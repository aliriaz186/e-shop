@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{__('Edit Package')}}</h3>
            <a style="float: right" href="{{ route('admin.packages') }}">Packages</a>
            <div class="pull-right clearfix">
            </div>
        </div>
        <div class="panel-body">
            <form style="width: 300px" method="post" action="{{route('admin.package_update')}}" enctype="multipart/form-data">
                @csrf
                <div>Logo</div>
                <input style="margin-top: 10px" type="file" name="image" class="form-control">
                <input type="hidden" value="{{$package->id}}" name="id">
                <div style="margin-top: 20px!important;">Name</div>
                <input  style="margin-top: 10px" name="name" value="{{$package->name}}" type="text" class="form-control" placeholder="enter package name">
                <div style="margin-top: 20px!important;">Price</div>
                <input  style="margin-top: 10px" value="{{$package->price}}" name="price" type="number" class="form-control" placeholder="enter package price">
                <label style="margin-top: 20px!important;">Product Upload</label>
                <input  style="margin-top: 10px" value="{{$package->product_upload}}" name="product_upload" type="number" class="form-control" placeholder="enter products allowed">
                <button type="submit" style="margin-top: 10px" class="btn btn-rounded btn-info">Update</button>
            </form>
        </div>
    </div>
    <script>
    </script>
@endsection
