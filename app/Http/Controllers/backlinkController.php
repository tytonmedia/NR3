<?php
namespace App\Http\Controllers;

use Exception;
use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use App\BacklinkResults;
use App\Backlink;
use App\User;
use App\Payment;
use App;
use Carbon\Carbon;
use GuzzleHttp\Client as guzzler;

ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

class backlinkController extends Controller
{


    public function get_backlink_results(Request $request)
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
            
        $Payment = Payment::withCount('backlink_results')->where('user_id',auth()->user()->id)->where('status',1)->first();
           // dd($Payment);
        }catch(Exception $e){
           // dd($e);
        }

        if(empty($Payment)){
               return view("partials/upgrade_backlinks", compact('url'));
        }else if($Payment->status == 0){
            return 'notsuccessful';
        }else if ($Payment->plan_id == 1 && $Payment->no_allowed_backlinks <= $Payment->backlink_results_count){
            return 'exceeded';
        }else if ($Payment->plan_id == 2 && $Payment->no_allowed_backlinks <= $Payment->backlink_results_count){
            return 'exceeded';
        }else if ($Payment->plan_id == 3 && $Payment->no_allowed_backlinks <= $Payment->backlink_results_count){
            return 'exceeded';
        }
        else
        {
           return  $this->get_backlinks($url,$Payment,$time);
        }

    }

      public function get_backlinks($url,$Payment,$time){
 
        $parse = parse_url($url);
        $domain_name = $parse['host']; // prints 'google.com'

        //if URL is already in backlinks_results table, show data instead of using API.
        $has_backlink_data = Backlink::where('target_url',$url)->where('created_at', '>=', Carbon::now()->subDays(30)->toDateTimeString())->first();

        if(empty($has_backlink_data)) {
        	// get SEMRush data and save to database

        	//if prod get all backlinks, else get 20 to save API credits
        	if(env('APP_ENV', 'production')){
        		$display_limit=5;
        	} else{
        		$display_limit=999;
        	}
        	 try{
      
      
            $semrush = "https://api.semrush.com/analytics/v1/?key=247c8d4143eff74adb96fb2f0b3f3d8a&type=backlinks&target=".$url."&target_type=url&export_columns=source_url,target_url,anchor,page_ascore,external_num,internal_num,last_seen,first_seen,nofollow&display_limit=".$display_limit;

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
            	if (strpos($resp, 'NOTHING FOUND') === 0) {
            		$response = 'There are no links pointing to this URL. Please try another URL.';
                    return view("dashboard/no_results", compact('response'));
            	} else{
                    $response = $resp;
                    return view("dashboard/no_results", compact('response'));
                }

            	
                } else {
                // no error
  				 $backlink_array = explode("\n", $resp);
           
            	 $backlink_array = preg_split('/\r*\n+|\r+/', $resp);
                 array_pop($backlink_array);
                
                    foreach ($backlink_array as $key => $value) {
                    		if($key == 0){
                    				$backlink_array[$key] = explode(';', $value);
                    		}elseif($key == 6 || $key == 7){
                        		    $backlink_array[$key] = explode(';', $value);
                      
                    		}else {
 								    $backlink_array[$key] = explode(';', $value);
                    		}
                    }
                    array_shift($backlink_array);
                    	//print_r($backlink_array);
                    foreach ($backlink_array as $key => $value) {
                    		//loop through all links and save to db
                    	$check_url = Backlink::where('source_url', $value[0])->first();
                    	if($key > 0 && empty($check_url)){
                    	    	$backlink = new Backlink;
   								$backlink->user_id = auth()->user()->id;
   								$backlink->payment_id = $Payment->id ?? 0;
   								$backlink->source_url = $value[0];
					   			$backlink->target_url = $value[1];
					   			$backlink->anchor = $value[2] ?? '';
					   			$backlink->page_ascore = $value[3] ?? 0;
					   			$backlink->internal_num = $value[5];
					   			$backlink->external_num = $value[3] ;
					   			$backlink->nofollow = $value[8];
					   			$backlink->first_seen = date('Y-m-d H:i:s',$value[7]);
					   			$backlink->last_seen = date('Y-m-d H:i:s',$value[6]);
					        	$backlink->save();
					        }

		
           	 		}
        }
           

        } catch(Exception $e){
        	dd($e);
        }


        } else {
        		// pull data from database
			//$backlink_array = Backlink::where('target_url', 'LIKE', '%'.$url.'%')->get()->toArray();
			$backlink_array = Backlink::select('source_url','target_url','anchor','page_ascore','internal_num','external_num','last_seen','first_seen','nofollow')->where('target_url', $url)->get()->toArray();
			
				 $newarray = array();
                                    foreach ($backlink_array as $key => $value) {
                                                $newarray = array_merge($newarray,array(array_values($value)));
                                    }
                              $backlink_array = $newarray;   
        }

         $has_backlink_results_data = BacklinkResults::where('site_url',$url)->where('created_at', '>=', Carbon::now()->subDays(14)->toDateTimeString())->first();
         if(empty($has_backlink_results_data)) {
        	// get SEMRush data and save to database
        	// get backlink counts
         try{

            $semrush = "https://api.semrush.com/analytics/v1/?key=247c8d4143eff74adb96fb2f0b3f3d8a&type=backlinks_overview&target=".$url."&target_type=url&export_columns=domains_num,urls_num";

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
                 $domains_num = 'empty';
                 $urls_num = 'empty';
                } else {
                // no error
                // split names & values
                $backlink_counts = preg_split('/\r*\n+|\r+/', $resp);
                array_pop($backlink_counts);
                
                    foreach ($backlink_counts as $key => $value) {
                        $backlink_counts[$key] = explode(';', $value);
                    }
                $domains_num = $backlink_counts[1][0];
                $urls_num = $backlink_counts[1][1];

            }
           

        } catch(Exception $e){}

    } else {
    		//already have data, get from
    	$backlink_count_array = BacklinkResults::select('domains_num','backlinks_num')->where('site_url', $url)->where('user_id',auth()->user()->id)->get()->toArray();
    //	print_r($backlink_count_array);
    			$domains_num = $backlink_count_array[0]['domains_num'];
               $urls_num = $backlink_count_array[0]['backlinks_num'];
    }
    	if(env('APP_ENV', 'production')){
        		$display_limit=1;
        	} else{
        		$display_limit=6;
        	}
        			
                       try{

            $semrush = "https://api.semrush.com/analytics/v1/?key=247c8d4143eff74adb96fb2f0b3f3d8a&type=backlinks_historical&target=".$url."&target_type=url&export_columns=date,backlinks_num,backlinks_new_num,backlinks_lost_num,domains_num,domains_new_num,domains_lost_num&timespan=months&display_limit=".$display_limit;

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
            	dd($resp);
            	
                } else {
                // no error
  				 $historical_array = explode("\n", $resp);
           
            	 $historical_array = preg_split('/\r*\n+|\r+/', $resp);
                 array_pop($historical_array);
                
                    foreach ($historical_array as $key => $value) {
                        $historical_array[$key] = explode(';', $value);
                    }
          				$historical_array = array_reverse($historical_array);

            		}
            		array_pop($historical_array);

        } catch(Exception $e){}

        	//manipulate data // 
        	////////////////////

        	$nofollow_array = array();
        	foreach ($backlink_array as $key => $value) {
        				foreach ($value as $key2 => $val) {
        					if($key2 == 8){
        						$nofollow_array[] = $val;
        					}
        				}
        	}

        	$nofollow_array = array_count_values($nofollow_array);
        		$linkpower_array = array();
        		//	print_r($historical_array);
        	foreach ($historical_array as $key => $value) {
        		//build historical linkpower array
        		foreach ($value as $key2 => $val) {
        			# code...
        			if($key2 == 4){
        				//domains
        		 if($val == 0){
						$linkpower_array[] = '0';
				}elseif($val > 0 && $val < 10) {
						$linkpower_array[] = '10';
				}elseif ($val > 10 && $val < 25) {
						$linkpower_array[] = '20';
				}elseif ($val > 25 && $val < 50) {
						$linkpower_array[] = '30';
				}elseif ($val > 50 && $val < 100) {
						$linkpower_array[] = '40';
				}elseif ($val > 100 && $val < 150) {
						$linkpower_array[] = '50';
				}elseif ($val > 150 && $val < 200) {
						$linkpower_array[] = '60';
				}elseif ($val > 200 && $val < 250) {
						$linkpower_array[] = '80';
				}elseif ($val > 250 && $val < 300) {
						$linkpower_array[] = '70';
				}elseif ($val > 300 && $val < 350) {
						$linkpower_array[] = '80';
				}elseif ($val > 350 && $val < 500) {
						$linkpower_array[] = '90';
				}elseif ($val > 500) {
						$linkpower_array[] = '100';
				}else{
						$linkpower_array[] = '0';
				}	
        		}
        		}

        	}

       if($domains_num != 'empty' && $urls_num != 'empty'){
       	//if not empty
        		if($domains_num == 0){
						$linkpower = '0';
				}elseif($domains_num > 0 && $domains_num < 10) {
						$linkpower = '10';
				}elseif ($domains_num > 10 && $domains_num < 25) {
						$linkpower = '20';
				}elseif ($domains_num > 25 && $domains_num < 50) {
						$linkpower = '30';
				}elseif ($domains_num > 50 && $domains_num < 100) {
						$linkpower = '40';
				}elseif ($domains_num > 100 && $domains_num < 150) {
						$linkpower = '50';
				}elseif ($domains_num > 150 && $domains_num < 200) {
						$linkpower = '60';
				}elseif ($domains_num > 200 && $domains_num < 250) {
						$linkpower = '80';
				}elseif ($domains_num > 250 && $domains_num < 300) {
						$linkpower = '70';
				}elseif ($domains_num > 300 && $domains_num < 350) {
						$linkpower = '80';
				}elseif ($domains_num > 350 && $domains_num < 500) {
						$linkpower = '90';
				}elseif ($domains_num > 500) {
						$linkpower = '100';
				}else{
						$linkpower = 'N/A';
				}			
        	}		
      			$linktoxicity = 0;
      			$anchor_array = array();
      			$tld_array = array();
        		foreach ($backlink_array as $key => $value) {
        			foreach ($value as $key2 => $val) {
        				# code...
        				if($key2 == 4) {
        					if($val > 250){
        							$linktoxicity += 1; 
        						}
        				}
        				if($key2 == 2) {

        							$anchor_array[] = $val; 
        				}
        				if($key2 == 0) {

        							$tld_array[] = $val; 
        				}

        				}

        			}
        				

        				foreach ($tld_array as $key => $value) {
        					# code...
        					$splits = explode(".", parse_url($value, PHP_URL_HOST));
        					$tlds[] = end($splits);
        				}


        			$tld_array = array_count_values($tlds);
 
        			$tld_count = array_sum($tld_array);
        			foreach ($tld_array as $key => $value) {
        						if($tld_count > 0){
      						$tld_array[$key] = ($value/$tld_count)*100;
      					}
        			}
        			$tld_array = array_slice($tld_array, 0, 5, true);

        			$anchor_array = array_count_values($anchor_array);
        			//build toxicity score
        			if(!empty($backlink_array) && $linktoxicity > 0){
        					$linktoxicity = number_format(count($backlink_array)/$linktoxicity);
        			} else{
        					$linktoxicity = 0;
        			}


        	                //add backlink results row to save the data for next time
					$create_backlink_results = new BacklinkResults;
       				$create_backlink_results->user_id = auth()->user()->id;
        			$create_backlink_results->site_url = $url;
        			$create_backlink_results->payment_id = $Payment->id ?? 0;
        			$create_backlink_results->domains_num = (float)$domains_num;
        			$create_backlink_results->backlinks_num = (float)$urls_num;
					$create_backlink_results->save();


        	 return view("dashboard/backlink_result", compact('data',
        	 													'url',
        	 													'time',
        	 													'domains_num',
        	 													'urls_num',
        	 													'backlink_array',
        	 													'linkpower',
        	 													'historical_array',
        	 													'linktoxicity',
        	 													'anchor_array',
        	 													'tld_array',
        	 													'linkpower_array',
        	 													'nofollow_array'));

		}

public function getTimeAgo($carbonObject) {
    return str_ireplace(
        [' seconds', ' second', ' minutes', ' minute', ' hours', ' hour', ' days', ' day', ' weeks', ' week'], 
        ['s', 's', 'm', 'm', 'h', 'h', 'd', 'd', 'w', 'w'], 
        $carbonObject->diffForHumans()
    );
}


  public static function get_linkpower_color($linkpower)
    {
            if($linkpower == 0 || $linkpower < 25){
                            return "#ff0000";
            }elseif($linkpower > 25 && $linkpower < 50) {
                                return "#ff6600";
            }elseif($linkpower > 50 && $linkpower < 75) {
                         return "#0d67db";
            }elseif($linkpower > 75 && $linkpower == 100) {
                        return "green";
            } else{
                  return "#333333";
            }
    }

  public static function get_toxicity_color($linktoxicity)
    {
            if($linktoxicity >= 0 && $linktoxicity < 25){
                            return "green";
            }elseif($linktoxicity > 25 && $linktoxicity < 50) {
                                return "yellow";
            }elseif($linktoxicity > 50 && $linktoxicity < 75) {
                         return "#ff6600";
            }elseif($linktoxicity > 75 && $linktoxicity == 100) {
                        return "#ff0000";
            } else{
                  return "#333333";
            }
    }

function get_final_url( $url_string ){

    while( 1 ){

        //validate URL
            $url = new \Altumo\String\Url( $url_string );

        //get the Location response header of the URL
            $client = new \Altumo\Http\OutgoingHttpRequest( $url_string );
            $response = $client->sendAndGetResponseMessage();
            $location = $response->getHeader( 'Location' );

        //return the URL if no Location header was found, else continue
            if( is_null($location) ){
                return $url_string;
            }else{
                $url_string = $location;
            }

    }

}
}