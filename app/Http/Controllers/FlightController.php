<?php

namespace App\Http\Controllers;

use App\Models\Flightticketvendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlightController extends Controller
{

    public function flightvendorList(Request $request){

        $post = Flightticketvendor::with(['admin']);

        if ($request->ajax()) {
            $post->FilterSearchText($request->search_text);

            $posts = $post->orderBy('id','DESC')->paginate($request->paginate_list ?? 10)->withQueryString();
            return view('admin.flight.vendor.load',compact('posts'));
        }

        $posts = $post->orderBy('id','DESC')->paginate($request->paginate_list ?? 10)->withQueryString();

        return view('admin.flight.vendor.list',compact('posts'));
    }

    public function flightvendorstore(Request $request){
        $post = new Flightticketvendor();
        $post->name = $request->name;
        $post->contact_no = $request->contact_no;
        $post->email = $request->email;
        $post->website = $request->website;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Flight Vendor added!');
    }

    public function flightvendoredit(Request $request){
        $post = Flightticketvendor::find($request->id);

        return response()->json($post);
    }

    public function flightvendorupdate(Request $request){
        $post = Flightticketvendor::find($request->edit_id);
        $post->name = $request->name;
        $post->contact_no = $request->contact_no;
        $post->email = $request->email;
        $post->website = $request->website;
        $post->save();

        return redirect()->back()->with('success','Flight Vendor Updated!');
    }

    public function flightvendorcheckcontact(Request $request){
        $contact_no = $request->contact_no;

        if (isset($request->id) && $request->id != '') {
            $post = Flightticketvendor::where('contact_no','=',$contact_no)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        } else {
            $post = Flightticketvendor::where('contact_no','=',$contact_no)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }

    }

    public function flightvendorcheckemail(Request $request){
        $email = $request->email;

        if (isset($request->id) && $request->id != '') {
            $post = Flightticketvendor::where('email','=',$email)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        } else {
            $post = Flightticketvendor::where('email','=',$email)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }



    }

    public function flightvendorcheckwebsite(Request $request){
        $website = $request->website;

        if (isset($request->id) && $request->id != '') {
            $post = Flightticketvendor::where('website','=',$website)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        } else {
            $post = Flightticketvendor::where('website','=',$website)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }


    }

}
