<?php
namespace App\Http\Controllers;

use Exception;
use Goutte\Client;
use Illuminate\Http\Request;
use App\User;
use App\Payment;
use App\Traffic;
use App\TrafficResults;
use App\WhiteLabel;
use Log;
use Redirect;
use Session;
use GuzzleHttp\Client as guzzler;

class trafficController extends Controller
{

    public function get_traffic_results(Request $request)
    {
        $domain = $request->input('domain');

        $time = date('F d Y, h:i:s A');

		        try{
            
            $Payment = Payment::withCount('traffic_results')->where('user_id',auth()->user()->id)->where('status',1)->first();
            $already_ran = TrafficResults::where('domain',$domain)->where('user_id',auth()->user()->id)->first();
            
        }catch(Exception $e){}

        if(empty($Payment)){
               return 'payme';
        }else if($Payment->status == 0){
            return 'notsuccessful';
        }else if ($Payment->plan_id == 1 && $Payment->no_allowed_traffic <= $Payment->traffic_results_count){
            return 'exceeded';
        } else if ($Payment->plan_id == 2 && $Payment->no_allowed_traffic <= $Payment->traffic_results_count){
            return 'exceeded';
        } else if ($Payment->plan_id == 3 && $Payment->no_allowed_traffic <= $Payment->traffic_results_count){
            return 'exceeded';
        } else if(!empty($already_ran)) {
                return 'duplicate';
        }
        else
        {
           return  $this->get_traffic($domain,$Payment,$time);
        }

    }

      public function get_traffic($domain,$Payment,$time){


  try{

   $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.dataforseo.com/v3/traffic_analytics/similarweb/live",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "gzip",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS =>"[\n    {\n        \"target\": \"".$domain."\"\n    }\n]",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".base64_encode('admin@tytonmedia.com:688a960db43100a9'),
        "Content-Type: application/json"
      ),
    ));

    $response = curl_exec($curl);
 $response = json_decode($response, true);
    curl_close($curl);

    $country_array = array();
    $similar_array = array();
    $estimated_array = array();
    $referral_array = array();
    $keyword_array = array();
    $ad_keywords = array();
    $top_socials = array();

            //add to database
        if($response['tasks'][0]['result'][0]['traffic']['countries'] ){
                foreach ($response['tasks'][0]['result'][0]['traffic']['countries'] as $key2 => $country) {
                    $country_array[] = array($country['country'], $country['value'], $country['percent']);
                 }
             }
        if($response['tasks'][0]['result'][0]['traffic']['sources']['referral_destination']['top_destinations']){
                foreach ($response['tasks'][0]['result'][0]['traffic']['sources']['referral_destination']['top_destinations'] as $key2 => $ref) {
                    $referral_array[] = array($ref['site']);
                 }
             }

                 if($response['tasks'][0]['result'][0]['traffic']['sources']['search_organic']['top_keywords']){
                foreach ($response['tasks'][0]['result'][0]['traffic']['sources']['search_organic']['top_keywords'] as $key2 => $keywords) {
                    $keyword_array[] = array($keywords['keyword'],$keywords['value'],$keywords['percent']);
                 }
             }
               if($response['tasks'][0]['result'][0]['traffic']['sources']['search_ad']['top_keywords']){
                foreach ($response['tasks'][0]['result'][0]['traffic']['sources']['search_ad']['top_keywords'] as $key2 => $ad_keyword) {
                    $ad_keywords[] = array($ad_keyword['keyword'],$ad_keyword['value'],$ad_keyword['percent']);
                 }
             }
               if($response['tasks'][0]['result'][0]['traffic']['sources']['social']['top_socials']){
                foreach ($response['tasks'][0]['result'][0]['traffic']['sources']['social']['top_socials'] as $key2 => $socials) {
                    $top_socials[] = array($socials['site'],$socials['value'],$socials['percent']);
                 }
             }
        if($response['tasks'][0]['result'][0]['traffic']['estimated']){
                foreach ($response['tasks'][0]['result'][0]['traffic']['estimated'] as $key2 => $val) {
                 $estimated_array[] = array($val['date'], $val['value']);
                     # code...
                 }
             }
        if($response['tasks'][0]['result'][0]['sites']['similar_sites']) {
                 foreach ($response['tasks'][0]['result'][0]['sites']['similar_sites'] as $key2 => $site) {
                     $similar_array[] = array($site['site'],$site['rank']);
                     # code...
                 }
             }

                if(count($estimated_array) > 0) {
                $estimated_values = array();
                foreach ($estimated_array as $key => $value) {
                  # code...
                  $estimated_values[] = $value[1];
                }
                $average_growth = $this->moving_average($estimated_values);
                } else {
                $average_growth = 0;
                }

                if(empty($estimated_array)){
                $estimated_array = null;
                }else{
                  $estimated_array = json_encode($estimated_array);
                }

                if(empty($country_array)){
                $country_array = null;
                }else{
                  $country_array = json_encode($country_array);
                }

                if(empty($keyword_array)){
                $keyword_array = null;
                }else{
                  $keyword_array = json_encode($keyword_array);
                }

                if(empty($referral_array)){
                $referral_array = null;
                }else{
                  $referral_array = json_encode($referral_array);
                }
                if(empty($similar_array)){
                $similar_array = null;
                }else{
                  $similar_array = json_encode($similar_array);
                }
                if(empty($ad_keywords)){
                $ad_keywords = null;
                }else{
                  $ad_keywords = json_encode($ad_keywords);
                }
                if(empty($top_socials)){
                $top_socials = null;
                }else{
                  $top_socials = json_encode($top_socials);
                }
                if(empty($estimated_values)){
                        return 'nodata';
                }else {
                $traffic = new Traffic;
                $traffic->user_id = auth()->user()->id;
                $traffic->payment_id = $Payment->id ?? 0;

                $traffic->domain = $domain;
                $traffic->description = $response['tasks'][0]['result'][0]['site_description'];
                $traffic->global_rank = $response['tasks'][0]['result'][0]['global_rank']['rank'];
                $traffic->cat_rank = $response['tasks'][0]['result'][0]['category_rank']['rank'];
                $traffic->country_rank = $response['tasks'][0]['result'][0]['country_rank']['rank'];
                $traffic->cat = $response['tasks'][0]['result'][0]['category_rank']['category'];
                $traffic->visits  = $response['tasks'][0]['result'][0]['audience']['visits'];
                $traffic->avg_time_site = $response['tasks'][0]['result'][0]['audience']['time_on_site_avg'];
                $traffic->avg_page_views = $response['tasks'][0]['result'][0]['audience']['page_views_avg'];
                $traffic->bounce_rate = $response['tasks'][0]['result'][0]['audience']['bounce_rate'];
                $traffic->traffic_value = $response['tasks'][0]['result'][0]['traffic']['value'];
                $traffic->direct_value = $response['tasks'][0]['result'][0]['traffic']['sources']['direct']['value'];
                $traffic->direct_percent = $response['tasks'][0]['result'][0]['traffic']['sources']['direct']['percent'];
                $traffic->organic_value = $response['tasks'][0]['result'][0]['traffic']['sources']['search_organic']['value'];
                $traffic->organic_percent = $response['tasks'][0]['result'][0]['traffic']['sources']['search_organic']['percent'];
                $traffic->search_value = $response['tasks'][0]['result'][0]['traffic']['sources']['search_ad']['value'];
                $traffic->search_percent = $response['tasks'][0]['result'][0]['traffic']['sources']['search_ad']['percent'];
                $traffic->referring_value = $response['tasks'][0]['result'][0]['traffic']['sources']['referring']['value'];
                $traffic->referring_percent = $response['tasks'][0]['result'][0]['traffic']['sources']['referring']['percent'];
                $traffic->social_value = $response['tasks'][0]['result'][0]['traffic']['sources']['social']['value'];
                $traffic->social_percent = $response['tasks'][0]['result'][0]['traffic']['sources']['social']['percent'];
                $traffic->countries = $country_array;
                $traffic->estimated = $estimated_array;
                $traffic->similar = $similar_array;
                $traffic->keywords = $keyword_array;
                $traffic->destinations = $referral_array;
                $traffic->ad_keywords = $ad_keywords;
                $traffic->top_socials = $top_socials;
                $traffic->average_growth = $average_growth;
                $traffic->save();


                $traffic_results = new TrafficResults;
                $traffic_results->user_id = auth()->user()->id;
                $traffic_results->payment_id = $Payment->id ?? 0;
                $traffic_results->traffic_id = $traffic->id;
                $traffic_results->domain = $domain;
                $traffic_results->traffic = $response['tasks'][0]['result'][0]['audience']['visits'];
                $traffic_results->save();


             $data = json_encode(array(
                                    'id' => $traffic_results->id,
                                    'domain' => $domain,
                                    'traffic' => $response['tasks'][0]['result'][0]['audience']['visits'],
                                    'updated_at' => date('F j, Y, g:i a', time())
                                ));

                        return $data;
                      }

        } catch(Exception $e){
           Log::error($e);
        }

}

public function moving_average($array) {
 $result = array();
for ($i = 1; $i < sizeof($array); $i++) {
    $result[] = $array[$i] - $array[$i-1];
}
return array_sum($result)/count($result);
}


 public function traffic_details($id){
        $traffic_id = TrafficResults::where('id', $id)->pluck('traffic_id');
        $traffic_id = $traffic_id[0];
        $traffic_details = Traffic::all()->where('id', $traffic_id)->toArray();
        $traffic_details = current($traffic_details);
   
         $user = User::where('id',auth()->user()->id)->first()->toArray();
          $white_label=WhiteLabel::where('user_id',auth()->user()->id)->first();
        if($white_label) {
            $white_label = $white_label->image_path;
        } else{
            $white_label = 0;
        }

        if($traffic_details['user_id'] == $user['id']){
                 return view('dashboard/traffic_result',compact('traffic_details','white_label'));
        } else{
                return redirect::to('/');
        }

     //   print_r($backlink_details);
      
           }

 public function delete_traffic_report($id){
   try {
     $domain = TrafficResults::select('domain')->where('id',$id)->where('user_id',auth()->user()->id)->pluck('domain');
     $traffic_id = TrafficResults::select('traffic_id')->where('id',$id)->where('user_id',auth()->user()->id)->pluck('traffic_id');
        TrafficResults::where('id', $id)->delete();
        Traffic::where('id', $traffic_id)->delete();
        //delete all backlinks with matching site url
        return $id;

        } catch(Exception $e) {
          return $e;
        }

           }
		
 public function email_traffic_report(Request $request){
  
            $send_to = $request->input('send_to');
             $url = $request->input('url');
             $id = $request->input('id');
             $id = $request->input('message');

                // send seo report email
        $ranking_details = KeywordResults::select('site_url','created_at')->where('id', $id)->get()->toArray();
            $ranking_details = current($ranking_details);
            
          // print_r($seo_audit_details);
           
                Mail::send('emails/ranking_report', compact('ranking_details', 'send_to', 'message'), function ($message) use ($send_to, $seo_audit_details, $url)
                        {
                            $message->from('admin@ninjareports.com', 'Ninja Reports');
                            $message->to($send_to);
                            $message->subject('SEO Analysis of '.$url);
                });
                      // check for failures
               if (Mail::failures()) {
                 // return response showing failed emails
                  return 0;
                     } else {
                      return 1;
                     }
           }

public static function thousandsCurrencyFormat($num) {

  if($num>1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;

  }

  return $num;
}

}