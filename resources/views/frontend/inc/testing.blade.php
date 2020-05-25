<div class="footer-menu">
    <ul @auth style="margin:0 5%;" @else style="margin:0 20%;" @endauth>
        <li><a href="{{ route('home') }}"> 
        <i class="la la-home"></i> 
        <h4 class="heading-5">{{__('Home')}}</h4>
        </a>
        </li>
        <li><a href="{{ route('categories.all') }}"> 
        <i class="la la-list-alt"></i> 
        <h4 class="heading-5">{{__('Categories')}}</h4>
        </a>
        </li>
        <li><div class="nav-search-box">
<a href="#" class="nav-box-link"> 
<i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i> 
<h4 class="heading-5">{{__('Search')}}</h4>
</a>
</div></li>
        <li><a href="{{ route('cart') }}"> 
        <i class="la la-shopping-cart"></i> 
        <h4 class="heading-5">{{__('Cart')}}</h4>
        </a>
        </li>
 	<li><a href=""> 
 	<i class="la la-bell"></i> 
 	<h4 class="heading-5">{{__('Update')}}</h4>
 	</a>
 	</li>
	@auth
        <li><a href="{{ route('dashboard') }}"> 
        <i class="la la-dashboard"></i> 
        <h4 class="heading-5">{{__('Dashboard')}}</h4>
        </a>
        </li>
	@endauth
    </ul>
</div>


<section class="slice-sm footer-top-bar bg-white">
    <div class="container sct-inner">
        <div class="row no-gutters">
            <div class="col-lg-3 col-md-6">
                <div class="footer-top-box text-center">
                    <a href="{{ route('sellerpolicy') }}">
                        <i class="la la-file-text"></i>
                        <h4 class="heading-5">{{__('Seller Policy')}}</h4>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-top-box text-center">
                    <a href="{{ route('returnpolicy') }}">
                        <i class="la la-mail-reply"></i>
                        <h4 class="heading-5">{{__('Return Policy')}}</h4>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-top-box text-center">
                    <a href="{{ route('supportpolicy') }}">
                        <i class="la la-support"></i>
                        <h4 class="heading-5">{{__('Support Policy')}}</h4>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-top-box text-center">
                    <a href="{{ route('profile') }}">
                        <i class="la la-dashboard"></i>
                        <h4 class="heading-5">{{__('My Profile')}}</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>