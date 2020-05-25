
<div class="footer-menu row p-3">
    @auth
        <div class="col-lg-2 col-md-2 col-sm-2 col-xl-2 col-2 text-center">
            <a href="{{ route('dashboard') }}"> <i class="la la-dashboard"></i> </a>
        </div>
        @else
        <div class="col-lg-1 col-md-1 col-sm-1 col-xl-1 col-1">
            &nbsp;
        </div>
    @endauth
    <div class="col-lg-2 col-md-2 col-sm-2 col-xl-2 col-2 text-center">
        <a href="{{ route('home') }}"> <i class="la la-home"></i> </a>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xl-2 col-2 text-center">
        <a href="{{ route('categories.all') }}"> <i class="la la-list-alt"></i> </a>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xl-2 col-2 text-center">
        <div class="nav-search-box">
            <a href="#" class="nav-box-link"> <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i> </a>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xl-2 col-2 text-center">
        <a href="{{ route('cart') }}"> <i class="la la-shopping-cart"></i> </a>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xl-2 col-2 text-center">
        <a href=""> <i class="la la-bell"></i> </a>
    </div>

</div>