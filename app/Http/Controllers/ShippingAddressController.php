<?php

namespace App\Http\Controllers;

use App\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
    public function addNew(Request $request){
        $shippingAddress = new ShippingAddress();
        $shippingAddress->user_id = Auth::user()->id;
        if($shippingAddress->save()){
            flash(__('successfully'))->success();
            return redirect()->route('profile');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function saveChanges(Request $request){
        $shippingAddress = ShippingAddress::where('id', $request->shippingId)->first();
        $shippingAddress->address = $request->address;
        $shippingAddress->country = $request->country;
        $shippingAddress->city = $request->city;
        $shippingAddress->postal_code = $request->postal_code;
        $shippingAddress->phone = $request->phone;
        if(ShippingAddress::where('id', $request->shippingId)->first()['is_default'] == 1){
            $user = Auth::user();
            $user->address = $shippingAddress->address;
            $user->country = $shippingAddress->country;
            $user->city = $shippingAddress->city;
            $user->postal_code = $shippingAddress->postal_code;
            $user->phone = $shippingAddress->phone;
            $user->update();
        }
        if($shippingAddress->update()){
            flash(__('successfully'))->success();
            return redirect()->route('profile');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function makeAsDefault(Request $request){
        foreach (ShippingAddress::where('user_id', Auth::user()->id)->get() as $add){
            $add->is_default = 0;
            $add->update();
        }
        $shippingAddress = ShippingAddress::where('id', $request->shippingId)->first();
        $user = Auth::user();
        $user->address = $shippingAddress->address;
        $user->country = $shippingAddress->country;
        $user->city = $shippingAddress->city;
        $user->postal_code = $shippingAddress->postal_code;
        $user->phone = $shippingAddress->phone;
        $shippingAddress->is_default = 1;
        $shippingAddress->update();
        if($user->update()){
            flash(__('successfully'))->success();
            return redirect()->route('profile');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function deleteShipping(Request $request){
        $shippingAddress = ShippingAddress::where('id', $request->shippingId)->first();
        if ($shippingAddress->is_default != 1){
            $shippingAddress->delete();
            return json_encode(true);
        }
        return json_encode(false);
    }
}
