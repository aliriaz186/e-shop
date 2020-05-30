<?php

namespace App\Http\Controllers;

use App\Product;
use App\SellerSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerSettingsController extends Controller
{
    public function update(Request $request){
        if (SellerSettings::where('seller_id', Auth::user()->id)->exists()){
            $settings = SellerSettings::where('seller_id', Auth::user()->id)->first();
            $allProducts = Product::where(["added_by" => "seller", "user_id" =>  Auth::user()->id])->get();
            foreach ($allProducts as $product){
                $product->published = 1;
                $product->update();
            }
            return json_encode($settings->delete());
        }else{
            $settings = new SellerSettings();
            $settings->seller_id = Auth::user()->id;
            $settings->mode = "Maintenance Mode";
            $allProducts = Product::where(["added_by" => "seller", "user_id" =>  Auth::user()->id])->get();
            foreach ($allProducts as $product){
                $product->published = 0;
                $product->update();
            }
            return json_encode($settings->save());
        }
    }
}
