<?php
namespace App\Http\Controllers;

use Exception;
use Goutte\Client;
use Illuminate\Http\Request;
use App\User;
use App\Payment;
use App\Keyword;
use App\KeywordResults;
use App\SerpFeatures;
use App\Competitor;
use Log;
use Spatie\Browsershot\Browsershot;
use Session;
use GuzzleHttp\Client as guzzler;

class rankingsController extends Controller
{

    public function get_rankings_results(Request $request)
    {
        $url = $request->input('url');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $a = curl_exec($ch);
        if(preg_match('#Location: (.*)#', $a, $r)) {
        $url = trim($r[1]);

        }
        $time = date('F d Y, h:i:s A');

		        try{
            
            $Payment = Payment::withCount('analysis')->where('user_id',auth()->user()->id)->where('status',1)->first();
            
        }catch(Exception $e){}

        if(empty($Payment)){
               return view("partials/upgrade_rankings", compact('time','url'));
        }else if($Payment->status == 0){
            return 'notsuccessful';
        }else if ($Payment->plan_id== 1 && $Payment->no_allowed_analysis <= $Payment->analysis_count ){
            return 'exceeded';
        }
        else
        {
           return  $this->get_rankings($url,$Payment,$time);
        }

    }

      public function get_rankings($url,$Payment,$time){


        
        $parse = parse_url($url);
        $domain_name = $parse['host']; // prints 'google.com'

         $has_competitor_data = Competitor::where('site_url',$url)->first();
            if(empty($has_competitor_data)){
            //get competitor list
         try{
              if(env('APP_ENV', 'production')){
                $display_limit=5;
            } else{
                $display_limit=1;
            }

             $competitor_array = array();
            $semrush = "https://api.semrush.com/?type=domain_organic_organic&key=247c8d4143eff74adb96fb2f0b3f3d8a&display_limit=".$display_limit."&export_columns=Dn,Np,Or,Ot,Oc,Ad&domain=".$domain_name."&database=us";

            $curl = curl_init($semrush);
            curl_setopt($curl, CURLOPT_URL, $semrush);
            curl_setopt($curl, CURLOPT_POST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);

            if (strpos($resp,'ERROR ')===0) {
                    //error
               // dd('error: '.$resp);
                return view("dashboard/no_results", compact('resp'));
  
  
                } else {
                // no error
                 $competitor_array = explode("\n", $resp);
           
                 $competitor_array = preg_split('/\r*\n+|\r+/', $resp);

                 array_pop($competitor_array);
                
                    foreach ($competitor_array as $key => $value) {
                    $competitor_array[$key] = explode(';', $value);    
                    }
                    array_shift($competitor_array);
                  
                   // save keywords to database
                    foreach ($competitor_array as $key => $value) {
                        $check_competitor = Competitor::where('domain', $value[0])->where('site_url', $url)->first();
                                if(empty($check_competitor)){
                                $comp = new Competitor;
                                $comp->user_id = auth()->user()->id;
                                $comp->payment_id = $Payment->id ?? 0;
                                $comp->site_url = $url;
                                $comp->domain = $value[0];
                                $comp->common_keywords = $value[1];
                                $comp->organic_keywords = $value[2] ?? '';
                                $comp->organic_traffic = $value[3];
                                $comp->cost  = $value[4];
                                $comp->adwords_keywords = $value[5];
                                $comp->save();
                            }
                    }
                    
           }

        } catch(Exception $e){
            Log::error($e);
        }
    } else {

        $competitor_array = Competitor::select('domain','common_keywords','organic_keywords','organic_traffic','cost','adwords_keywords')->where('site_url', $url)->orderBy('common_keywords', 'desc')->get()->toArray();

    }
 	      $has_keyword_data = Keyword::where('site_url',$url)->first();
            if(empty($has_keyword_data)) {
            //no keywords in db, pull SEMrush data and save to DB
                if(env('APP_ENV', 'production')){
                $display_limit=999;
            } else{
                $display_limit=2;
            }
        try{
            $semrush = "https://api.semrush.com/?type=url_organic&key=247c8d4143eff74adb96fb2f0b3f3d8a&display_limit=2&export_columns=Ph,Kd,Po,Nq,Cp,Co,Tr,Tc,Nr,Fk,Td&url=".$url."&database=us";

            $curl = curl_init($semrush);
            curl_setopt($curl, CURLOPT_URL, $semrush);
            curl_setopt($curl, CURLOPT_POST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);


            if (strpos($resp,'ERROR ')===0) {
                    //error
              //  dd('error: '.$resp);
              if (strpos($resp,'ERROR 50')===0) {
                $response = 'Sorry, there are no results for that URL. Please try another URL';
                return view("dashboard/no_results", compact('response'));
                } else {
                $response = $resp;
                return view("dashboard/no_results", compact('response'));

                }


                
                } else {
                // no error
                 $keyword_array = explode("\n", $resp);
           
                 $keyword_array = preg_split('/\r*\n+|\r+/', $resp);

                 array_pop($keyword_array);
                
                    foreach ($keyword_array as $key => $value) {
                    $keyword_array[$key] = explode(';', $value);    
                    }

                    array_shift($keyword_array);


                    $positions = array();
                    $traffic_volume = array();
                    $traffic_vals = array();
                    $traffic_share = array();
                    $trend_array = array();
                    $features_array = array();
                    // save keywords to database

                    $top_keyword = $keyword_array[0][0];


                    foreach ($keyword_array as $key => $value) {
                        

                                    //check if keyeword is already in database
                                 $check_keyword = Keyword::where('keyword', $value[0])->where('site_url', $url)->first();

                                if(empty($check_keyword)){
                                    //add keyword to database
                                $keyword = new Keyword;
                                $keyword->user_id = auth()->user()->id;
                                $keyword->payment_id = $Payment->id ?? 0;
                                $keyword->site_url = $url;
                                $keyword->keyword = $value[0];
                                $keyword->position = $value[2];
                                $keyword->kd = $value[1];
                                $keyword->volume = $value[3] ?? '';
                                $keyword->cpc = $value[4];
                                $keyword->competition  = $value[5];
                                $keyword->traffic_per = $value[6];
                                $keyword->traffic_cost = $value[7];
                                $keyword->results = $value[8] ?? '';
                                $keyword->trend = json_encode(explode(',',$value[10]));
                                $keyword->features = json_encode(explode(',',$value[9]));
                                $keyword->save();
                             }

                                //build array values

                                $keyword_array[$key][10] = explode(',',$value[10]);
                                $keyword_array[$key][9] = explode(',',$value[9]);

                                $positions[] = $value[2];
                                $traffic_volume[] = $value[3] ?? 0;
                                $traffic_vals[] = $value[7] ?? 0;
                                $traffic_share[] = $value[6] ?? 0;
                                $trend_array[] = json_encode(explode(',',$value[10]));
                                $features_array[] = json_encode(explode(',',$value[9]));


                    }


                                foreach ($trend_array as $key => $value) {
                                  $trend_array[$key] = explode(",", $value);
                                }
                                foreach ($features_array as $key => $value) {
                                  $features_array[$key] = explode(",", $value);
                                }

 
        }
           

        } catch(Exception $e){
            Log::error($e);
        }

            }else{
                //db has keyword data for this site url, pull from db and display data

                $keyword_array = Keyword::select('keyword','kd','position','volume','cpc','competition','traffic_per','traffic_cost','results','features','trend')->where('site_url', $url)->get()->toArray();
                           $trend_array = array();
                       foreach ($keyword_array as $key => $value) {
                                    
                                if($key == 0) {
                                     $top_keyword = $value['keyword'];
                                    }
                                    $positions[] = $value['position'];
                      
                                    $traffic_volume[] = $value['volume'];
                          
                                    $traffic_vals[] = $value['traffic_cost'];

                                    $traffic_share[] = $value['traffic_per'];

                                    $keyword_array[$key]['trend'] = json_decode($value['trend']);

                                    $trend_array[] = json_decode($value['trend']);

                                    $keyword_array[$key]['features'] = json_decode($value['features']);

                                    $features_array[] = json_decode($value['features']);
                                    }

                                    
                                    //run through again and remove column names to match SEMRush data
                                    $newarray = array();
                                    foreach ($keyword_array as $key => $value) {
                                                $newarray = array_merge($newarray,array(array_values($value)));
                                    }
                                                        
                                   $keyword_array = $newarray;

                       }
                   
           //   dd($keyword_array);

                   $trend_count = count($trend_array);
                   $t_array = array();
                   $tempval = 0;
                        foreach ($trend_array as $key => $value) {
                            foreach ($value as $key2 => $val) {
                              if($trend_count == $key){
                                      $t_array[$key2] = $val / $trend_count;
                              } else {
                                $t_array[$key2] = $val + $tempval;
                              }
                               $tempval = $val;
                            }

                        }

                    $serp_array = array();

                    foreach ($features_array as $key => $value) {
                      foreach ($value as $key2 => $val) {
                        # code...
                        $serp_array[] = $val;
                      }
                    }

                    $serp_array = array_count_values($serp_array);

                     $count1 = $count2 = $count3 = $count4 = 0;
                       for ($i = 0; $i < sizeof($positions); $i++) {
                           if($positions[$i] >= 1 && $positions[$i] <= 10 ) {
                               $count1++;
                           }
                           if($positions[$i] >= 11 && $positions[$i] <= 20 ) {
                               $count2++;
                           }
                           if($positions[$i] >= 21 && $positions[$i] <= 50 ) {
                               $count3++;
                           }
                           if($positions[$i] >= 21 && $positions[$i] <= 50 ) {
                               $count3++;
                           }
                           if($positions[$i] >= 51 && $positions[$i] <= 100 ) {
                               $count4++;
                           }
                           if($positions[$i] >= 100) {
                               $count4++;
                           }
                       } // end for loop

                      // print_r($trend_array);

                       $position_array = array();
                       $position_array = ["1-10" => $count1, "11-20" => $count2, "21-50" => $count3, "51-100" => $count3, "100+" => $count4];
                       $traffic_share = array_sum($traffic_share);

                       if(count($positions) > 0){
                    $avg_position = array_sum($positions)/count($positions); 
                  } else{
                    $avg_position = 0;
                  }
      
                     $traffic_value  = array_sum($traffic_vals);
                     $volume_total = array_sum($traffic_volume);
                     $num_keywords = sizeof($keyword_array);
                    
                 //add backlink results row to save the data for next time
                    $create_keyword_results = new KeywordResults;
                    $create_keyword_results->user_id = auth()->user()->id;
                    $create_keyword_results->site_url = $url;
                    $create_keyword_results->payment_id = $Payment->id ?? 0;
                    $create_keyword_results->save();

                    Session::put('time', $time);
                    Session::put('url', $url);
                    Session::put('keyword_array', $keyword_array);
                    Session::put('num_keywords', $num_keywords);
                    Session::put('avg_position', $avg_position);
                    Session::put('traffic_value', $traffic_value);
                    Session::put('top_keyword', $top_keyword);
                    Session::put('volume_total', $volume_total);
                    Session::put('position_array', $position_array);
                    Session::put('t_array', $t_array);
                    Session::put('competitor_array', $competitor_array);
                    Session::put('traffic_share', $traffic_share);
                    Session::put('features_array', $features_array);
                    Session::put('serp_array', $serp_array);


        	 return view("dashboard/ranking_result", compact('time','url','keyword_array','num_keywords','avg_position','traffic_value','top_keyword','volume_total','position_array','t_array','competitor_array','traffic_share','features_array', 'serp_array'));

		}


    public function pdf_create_rankings()
    {
        $time = Session::get('time');
        $url = Session::get('url');
        $keyword_array = Session::get('keyword_array');
        $num_keywords = Session::get('num_keywords');
        $avg_position = Session::get('avg_position');
        $traffic_value = Session::get('traffic_value');
        $top_keyword = Session::get('top_keyword');
        $volume_total = Session::get('volume_total');
        $position_array = Session::get('position_array');
        $t_array = Session::get('t_array');
        $competitor_array = Session::get('competitor_array');
        $traffic_share = Session::get('traffic_share');
        $features_array = Session::get('features_array');
        $serp_array = Session::get('serp_array');

        $content = view("dashboard/ranking_result", compact('time','url','keyword_array','num_keywords','avg_position','traffic_value','top_keyword','volume_total','position_array','t_array','competitor_array','traffic_share','features_array', 'serp_array'));
  
         return Browsershot::html($content)
        ->margins(18, 18, 24, 18)
        ->format('A4')
        ->setNodeBinary("C:\Programs\\nodejs\\node.exe")
        ->showBackground()
        ->pdf();
    }
		

        public static function get_serp_feature($id)
    {
            $get_serp_data = SerpFeatures::select('name')->where('id', $id)->first();
            return $get_serp_data->name;
    }
            public static function get_serp_feature_desc($id)
    {
            $get_serp_data = SerpFeatures::select('description')->where('id', $id)->first();
            return $get_serp_data->description;
    }


            public static function get_serp_feature_icon($id)
    {
            if($id == 0) {
              return "fa fa-question-circle-o";
            }elseif($id == 1){
              return "fa fa-lightbulb-o";
            }elseif($id == 2){
              return "fa fa-question-circle-o";
            }elseif($id == 3){
              return "fa fa-map-o";
            }elseif($id == 4){
              return "fa fa-newspaper-o";
            }elseif($id == 5){
              return "fa fa-picture-o";
            }elseif($id == 6){
              return "fa fa-link";
            }elseif($id == 7){
              return "fa fa-star";
            }elseif($id == 8){
              return "fa fa-twitter";
            }elseif($id == 9){
              return "fa fa-video-camera";
            }elseif($id == 10){
              return "fa fa-question-circle-o";
            }elseif($id == 11){
              return "fa fa-quote-right";
            }elseif($id == 12){
              return "fa fa-question-circle-o";
            }elseif($id == 13){
              return "fa fa-picture-o";
            }elseif($id == 14){
              return "fa fa-google";
            }elseif($id == 15){
              return "fa fa-google";
            }elseif($id == 16){
              return "fa fa-shopping-basket";
            }elseif($id == 17){
              return "fa fa-question-circle-o";
            }elseif($id == 18){
              return "fa fa-question-circle-o";
            }elseif($id == 19){
              return "fa fa-question-circle-o";
            } elseif($id == 20){
              return "fa fa-camera";
            } elseif($id == 21){
              return "fa fa-users";
             } elseif($id == 22){
              return "fa fa-comments-o";
            }


    }
}