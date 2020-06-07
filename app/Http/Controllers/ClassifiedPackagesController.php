<?php

namespace App\Http\Controllers;

use App\ClassifiedPackage;
use Illuminate\Http\Request;

class ClassifiedPackagesController extends Controller
{
    public function viewPackages(){
        return view('admin.packages')->with(['packages' => ClassifiedPackage::all()]);
    }

    public function viewAddPackage(){
        return view('admin.add_package');
    }

    public function store(Request $request){
        try{
            if (empty($request->name) || empty($request->price) || empty($request->product_upload)){
                flash(__('Something went wrong'))->error();
                return back();
            }
            $package = new ClassifiedPackage();
            $package->name = $request->name;
            $package->price = $request->price;
            $package->product_upload = $request->product_upload;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $request->image->move(public_path('packages/'), $fileName);
                $input["image"] = $fileName;
                $package->logo = $fileName;
            }else{
                flash(__('Something went wrong'))->error();
                return back();
            }
            if($package->save()){
                flash(__('successfully'))->success();
                return redirect()->route("admin.packages");
            }
            flash(__('Something went wrong'))->error();
            return back();
        }catch (\Exception $exception){
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function editPackage($id){
        return view('admin.edit_package')->with(['package' => ClassifiedPackage::where('id', $id)->first()]);
    }

    public function update(Request $request){
        try{
            if (empty($request->name) || empty($request->price) || empty($request->product_upload)){
                flash(__('Something went wrong'))->error();
                return back();
            }
            $package = ClassifiedPackage::where('id', $request->id)->first();
            $package->name = $request->name;
            $package->price = $request->price;
            $package->product_upload = $request->product_upload;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $request->image->move(public_path('packages/'), $fileName);
                $input["image"] = $fileName;
                $package->logo = $fileName;
            }
            if($package->update()){
                flash(__('successfully'))->success();
                return redirect()->route("admin.packages");
            }
            flash(__('Something went wrong'))->error();
            return back();
        }catch (\Exception $exception){

            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function delete($id){
        if (ClassifiedPackage::where('id', $id)->exists())
        {
           $package = ClassifiedPackage::where('id', $id)->first();
            if($package->delete()){
                flash(__('successfully'))->success();
                return redirect()->route("admin.packages");
            }
            flash(__('Something went wrong'))->error();
            return back();
        }
        else{
            flash(__('Something went wrong'))->error();
            return back();
        }
    }
}
