<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;
use App\User;
use App\Payment;  
use Auth;
use DB;
use App\SeoResult;
use App\BacklinkResults;
use App\KeywordResults;
use App\TrafficResults;
use App\Audit;
use App\AuditResults;
use App\WhiteLabel;

class DashboardController extends Controller
{
    public function home(){
        if(!empty(auth()->user()->id)) {
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
        if(isset($Payment['product_id'])){
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET_KEY')
            );
            $product = $stripe->products->retrieve(
                $Payment['product_id'],
                []
            );
                $productname = $product->name;
                $created = date("F j, Y", $product->created); 
        }else{
            $productname = 'Free';
            $created = 'N/A';
        }
} else {
     $productname = 'Free';
         $created = 'N/A';
}
         return view('dashboard/home',compact('productname','created'));
    }


        public function services(){
            if(!empty(auth()->user()->id)) {
            $payment=Payment::where('user_id',auth()->user()->id)->where('user_id',auth()->user()->id)->first();
            if(empty($payment)){
                    $payment = false;
                }else {
                    $payment = true;
                }
         return view('dashboard/services',compact('payment'));
    } else{
        $payment = false;
        return view('dashboard/services',compact('payment'));
    }
}

    public function seo_audit(){
                if(!empty(auth()->user()->id)) {
            $payment=Payment::where('user_id',auth()->user()->id)->where('user_id',auth()->user()->id)->first();
            if(empty($payment)){
                    $payment = false;
                }else {
                    $payment = true;
                }
            $audit_results = AuditResults::select('id','site_url','updated_at')->where('user_id',auth()->user()->id)->get()->toArray();
        } else {
            $audit_results = null;
             $payment = false;
         }

        return view('dashboard/seo_audit',compact('audit_results','payment'));
        

    }
public function seo_backlinks(){
        if(!empty(auth()->user()->id)) {
            $payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
            if(empty($payment)){
                    $payment = false;
                }else {
                    $payment = true;
                }
            $backlink_results = BacklinkResults::select('id','backlinks_num','domains_num','site_url','updated_at')->where('user_id',auth()->user()->id)->get()->toArray();
        } else {
            $backlink_results = null;
            $payment = false;
          }
         
        return view('dashboard/backlinks',compact('backlink_results','payment'));

    }
    public function seo_rankings(){

                if(!empty(auth()->user()->id)) {
                $payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
                if(empty($payment)){
                    $payment = false;
                }else {
                    $payment = true;
                }
            $ranking_results = KeywordResults::select('id','site_url','keywords','updated_at')->where('user_id',auth()->user()->id)->get()->toArray();
        } else {
            $ranking_results = null;
            $payment = false;
          }
        return view('dashboard/rankings',compact('ranking_results','payment'));
    }
    public function seo_analysis(){
    
        if(!empty(auth()->user()->id)) {
            $payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
            if(empty($payment)){
                    $payment = false;
                }else {
                    $payment = true;
                }
            $seo_results = SeoResult::select('id','passed_score','error_score','url','updated_at')->where('user_id',auth()->user()->id)->get();
        } else {
            $seo_results = null;
            $payment = false;
         }
        return view('dashboard/seo_analysis',compact('seo_results','payment'));
        
    }
        public function seo_traffic(){
    
        if(!empty(auth()->user()->id)) {
            $payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
            if(empty($payment)){
                    $payment = false;
                }else {
                    $payment = true;
                }
            $traffic_results = TrafficResults::select('id','domain','traffic','updated_at')->where('user_id',auth()->user()->id)->get();
        } else {
            $traffic_results = null;
            $payment = false;
          }
        return view('dashboard/traffic',compact('traffic_results','payment'));
       
    }
    public function account(){
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();

        if($Payment['product_id'] == env('AGENCY_ID')){
            $has_white_label = 1;
        }else{
            $has_white_label = 0;
        }
        if($Payment['product_id']){
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET_KEY')
            );
            $product = $stripe->products->retrieve(
                $Payment['product_id'],
                []
            );
            $status = $Payment['status'];

        }else{
            $product = 'N/A';
            $status = 'N/A';
        }
        $white_label=WhiteLabel::where('user_id',auth()->user()->id)->first();

        if($white_label){
            $white_label = $white_label->image_path;
        }else{
            $white_label = 0;
        }
        return view('dashboard/account',compact('product','status','white_label','has_white_label'));
    }
    public function subscription(){
        if(!empty(auth()->user()->id)) {
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
        if(isset($Payment['product_id'])){
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET_KEY')
            );
            $product = $stripe->products->retrieve(
                $Payment['product_id'],
                []
            );
            $status = $Payment['status'];

        }else{
            $product = 'N/A';
            $status = 'N/A';
        }
    } else {
        $status = 0;
        $product = 'N/A';
    }
        return view('dashboard/subscription',compact('product','status'));
    }
    public function destroy($id){
        $user=User::findOrFail($id);
        $user->delete();
        return redirect('/home');
    }

    public function cancelSubscription(){
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
        //dd($Payment['subscription_id']);
        if($Payment){
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET_KEY')
            );
            $stripe->subscriptions->cancel(
                $Payment['subscription_id'],
                []
            );
        }
        $Payment->status = 0;
        $Payment->save();
        return redirect('/account')->with('message', 'Subscription cancelled successfully.');
    }
    public function pricing(){
        return view('dashboard/pricing');
    }

    public function imageUploadPost(Request $request)
{       
    try {
        
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
            if($Payment) {
       $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
  
        $imageName = $request->image->getClientOriginalName(); 
   
        $request->image->move(public_path('images/wl'), $imageName);

                        $create_white_label = new WhiteLabel;
                        $create_white_label->user_id =auth()->user()->id;
                        $create_white_label->payment_id =$Payment->id;
                        $create_white_label->image_path = 'images/wl/'.$imageName;
                        $create_white_label->save();

   
        return back()
            ->with('message','You have successfully upload image.')
            ->with('image',$imageName);
                } else{
                    return redirect('/account')->with('message', 'You must upgrade in order to use the white label feature.');
                }

} catch (Exception $e) {
        dd($e);
    }

        }
}
