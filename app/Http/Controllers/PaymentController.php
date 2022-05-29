<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Payment;   
class PaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment($id)
    {
        $id = $id;
        $date = strtotime("+7 day");
        $trial_end = date('M d, Y', $date);
        $next_billing = strtotime("+30 day");
        $next_billing = date('M d', $next_billing);
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
     
        return view('dashboard/payment',compact('id','status','next_billing'));
    }
  
    public function stripePost(Request $request,$id)
    {
        $Payment=Payment::where('user_id',auth()->user()->id)->where('status',1)->first();
        
        $stripe = new \Stripe\StripeClient(
         env('STRIPE_SECRET_KEY')
          );

        if(!empty($Payment->subscription_id) && $Payment->status == 1){
            
            if($id == 1){
                if($Payment->plan_id ==1){
                    return redirect('/account')->with('message', 'You are already subscribed this plan.');
                }else{
                    $plan_id = '1';
                    $no_allowed_site = '100';
                    $no_allowed_audits = '25';
                    $no_allowed_backlinks = '5';
                    $no_allowed_rankings = '10';
                    $no_allowed_traffic = '15';
                    $retrive= $stripe->subscriptions->retrieve(
                        $Payment->subscription_id,
                        []
                    );
                    
                        $charge = $stripe->subscriptions->update(
                        $Payment->subscription_id,
                        [
                
                            'items' => [
                                [
                                'id' => $retrive->items->data[0]->id,
                                'price' => env('WEBMASTER_PRICE'),
                                ],
                            ],
                        ]);
                        $subscribe = array(
                            'user_id'           => auth()->user()->id,
                            'subscription_id'   => $charge->id,
                            'plan_id'           => $plan_id,
                            'no_allowed_analysis'   => $no_allowed_site,
                            'no_allowed_audits'   => $no_allowed_audits,
                            'no_allowed_backlinks'   => $no_allowed_backlinks,
                            'no_allowed_rankings'   => $no_allowed_rankings,
                             'no_allowed_traffic'   => $no_allowed_traffic,
                            'currency'          => $charge->plan->currency,
                            'amount'            => $charge->plan->amount,
                            'interval'          => $charge->plan->interval,
                            'product_id'        => $charge->plan->product,
                            'current_period_start'  => $charge->current_period_start,
                            'current_period_end'    => $charge->current_period_end,
                            'status'  => 1
                        );
                        Payment::where('user_id',auth()->user()->id)->where('status',1)->update($subscribe);
                        return redirect('/account')->with('message', 'Subscription updated successfully.');    
                }   
            }else if($id == 2){
                if($Payment->plan_id ==2){
                    return redirect('/account')->with('message', 'You are already subscribed this plan.');
                }else{
                    $plan_id = '2';
                    $no_allowed_site = '999';
                    $no_allowed_audits = '100';
                     $no_allowed_backlinks = '15';
                    $no_allowed_rankings = '25';
                    $no_allowed_traffic = '30';
                    $retrive= $stripe->subscriptions->retrieve(
                        $Payment->subscription_id,
                        []
                    );
                    //dd($retrive);
                    $charge = $stripe->subscriptions->update(
                        $Payment->subscription_id,
                        [
                            'items' => [
                                [
                                'id' => $retrive->items->data[0]->id,
                                'price' => env('BUSINESS_PRICE'),
                                ],
                            ],
                        ]);
                        $subscribe = array(
                            'user_id'           => auth()->user()->id,
                            'subscription_id'   => $charge->id,
                            'plan_id'           => $plan_id,
                            'no_allowed_analysis'   => $no_allowed_site,
                            'no_allowed_audits'   => $no_allowed_audits,
                            'no_allowed_backlinks'   => $no_allowed_backlinks,
                            'no_allowed_rankings'   => $no_allowed_rankings,
                            'no_allowed_traffic'   => $no_allowed_traffic,
                            'currency'          => $charge->plan->currency,
                            'amount'            => $charge->plan->amount,
                            'interval'          => $charge->plan->interval,
                            'product_id'        => $charge->plan->product,
                            'current_period_start'  => $charge->current_period_start,
                            'current_period_end'    => $charge->current_period_end,
                            'status'  => 1
                        );
                        Payment::where('user_id',auth()->user()->id)->where('status',1)->update($subscribe);
                        return redirect('/account')->with('message', 'Subscription updated successfully.');  
                }  
            }else if($id == 3){
                if($Payment->plan_id == 3){
                    return redirect('/account')->with('message', 'You are already subscribed to this plan.');
                }else{
                    $plan_id = '3';
                    $no_allowed_site = '999';
                    $no_allowed_audits = '250';
                    $no_allowed_backlinks = '25';
                    $no_allowed_rankings = '25';
                    $no_allowed_traffic = '50';
                    $retrive= $stripe->subscriptions->retrieve(
                        $Payment->subscription_id,
                        []
                    );

                    $charge = $stripe->subscriptions->update(
                        $Payment->subscription_id,
                        [
                
                            'items' => [
                                [
                                'id' => $retrive->items->data[0]->id,
                                'price' => env('AGENCY_PRICE'),
                                ],
                            ],
                        ]);
                        $subscribe = array(
                            'user_id'           => auth()->user()->id,
                            'subscription_id'   => $charge->id,
                            'plan_id'           => $plan_id,
                            'no_allowed_analysis'   => $no_allowed_site,
                            'no_allowed_audits'     => $no_allowed_audits,
                            'no_allowed_backlinks'   => $no_allowed_backlinks,
                            'no_allowed_rankings'   => $no_allowed_rankings,
                            'no_allowed_traffic'   => $no_allowed_traffic,
                            'currency'          => $charge->plan->currency,
                            'amount'            => $charge->plan->amount,
                            'interval'          => $charge->plan->interval,
                            'product_id'        => $charge->plan->product,
                            'current_period_start'  => $charge->current_period_start,
                            'current_period_end'    => $charge->current_period_end,
                            'status'  => 1
                        );
                        Payment::where('user_id',auth()->user()->id)->where('status',1)->update($subscribe);
                        return redirect('/account')->with('message', 'Subscription updated successfully.'); 
                }       
            }
           
           
        }else{
            if($id == 1){
                $customer = $stripe->customers->create([
                    "email"       => auth()->user()->email,
                    "name"        => auth()->user()->name,
                    "source" => $request->stripeToken
                ]);

                $charge = $stripe->subscriptions->create([
                        "customer" => $customer->id,
                        'items' => [
                            ['price' => env('WEBMASTER_PRICE')],
                        ],
                    
                ]);
                $plan_id = '1';
                $no_allowed_site = '100';
                $no_allowed_audits = '25';
                $no_allowed_backlinks = '5';
                $no_allowed_rankings = '10';
                $no_allowed_traffic = '15';
            }else if($id == 2){
                $customer = $stripe->customers->create([
                    "email"       => auth()->user()->email,
                    "name"        => auth()->user()->name,
                    "source" => $request->stripeToken
                ]);

                $charge = $stripe->subscriptions->create([
                        "customer" => $customer->id,
                        'items' => [
                            ['price' => env('BUSINESS_PRICE')],
                        ],
                    
                ]);
                $plan_id = '2';
                $no_allowed_site = '999';
                $no_allowed_audits = '100';
                $no_allowed_backlinks = '15';
                $no_allowed_rankings = '25';
                $no_allowed_traffic = '30';
            }else if($id == 3){
                $customer = $stripe->customers->create([
                    "email"       => auth()->user()->email,
                    "name"        => auth()->user()->name,
                    "source" => $request->stripeToken
                ]);

                $charge = $stripe->subscriptions->create([
                        "customer" => $customer->id,
                        'items' => [
                            ['price' => env('AGENCY_PRICE')],
                        ],
                    
                ]);
                $plan_id = '3';
                $no_allowed_site = '999';
                $no_allowed_audits = '250';
                $no_allowed_backlinks = '100';
                $no_allowed_rankings = '100';
                $no_allowed_traffic = '50';
            }
            $create_user = new Payment;
            $create_user->user_id = auth()->user()->id;
            $create_user->subscription_id = $charge->id;
            $create_user->plan_id = $plan_id;
            $create_user->no_allowed_analysis = $no_allowed_site;
            $create_user->no_allowed_audits = $no_allowed_audits;
            $create_user->no_allowed_backlinks = $no_allowed_backlinks;
            $create_user->no_allowed_rankings = $no_allowed_rankings;
            $create_user->no_allowed_traffic = $no_allowed_traffic;
            $create_user->currency = $charge->plan->currency;
            $create_user->amount = $charge->plan->amount;
            $create_user->interval = $charge->plan->interval;
            $create_user->product_id = $charge->plan->product;
            $create_user->current_period_start = $charge->current_period_start;
            $create_user->current_period_end = $charge->current_period_end;
            $create_user->status = '1';
            $create_user->save();
        }
        return redirect('/account')->with('message', 'You are now subscribed!');
        // dd($charge);
    }
    
}