<?php
namespace App\Http\Controllers;

use Exception;
use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;
use Redirect;
use App\Analysis;
use App\User;
use App\Payment;
use App\Audit;
use App\AuditResults;
use App\SeoResult;
use VerumConsilium\Browsershot\Facades\PDF;
use HeadlessChromium\BrowserFactory;
use App\WhiteLabel;
use Mail;
use GuzzleHttp\Client as guzzler;

ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

class analysisController extends Controller
{

    public function get_seo_result(Request $request)
    {
        ini_set("allow_url_fopen", 1);
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
            $already_ran = SeoResult::where('url',$url)->where('user_id',auth()->user()->id)->first();

        }catch(Exception $e){
             Log::error($e);
        }

        if(empty($Payment)){
            $analysis = Analysis::where('user_id',auth()->user()->id)->whereDay('created_at', '=', date('d'))->latest()->first();
            if(empty($analysis)){
                 if(!empty($already_ran)) {
                return 'duplicate';
                } else{
                return  $this->get_seo($url,$Payment,$time);
                }
            }else{
                return 'upgrade';
            }
        }
        else if($Payment->status == 0){
            return 'notsuccessful';
        }else if ($Payment->plan_id== 1 && $Payment->no_allowed_analysis <= $Payment->analysis_count ){
            return 'exceeded';
        }else if(!empty($already_ran)) {
                return 'duplicate';
        }
        else
        {
           return  $this->get_seo($url,$Payment,$time);
        }
    }
    public function get_audit_result(Request $request)
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
            $Payment = Payment::withCount('audit')->where('user_id',auth()->user()->id)->where('status',1)->first();
            $already_ran = AuditResults::where('site_url',$url)->where('user_id',auth()->user()->id)->first();
        }catch(Exception $e){
             Log::error($e);
        }
      
        if(empty($Payment) || $Payment->status == 0){
            return 'notsuccessful';
        }else if ($Payment->plan_id == 1 && $Payment->no_allowed_audits <= $Payment->audit_count ){
            return 'exceeded';
        }
        else if ($Payment->plan_id == 2 && $Payment->no_allowed_audits <= $Payment->audit_count ){
            return 'exceeded';
        }
        else if ($Payment->plan_id== 3 && $Payment->no_allowed_audits <= $Payment->audit_count ){
            return 'exceeded';
        }else if(!empty($already_ran)) {
                return 'duplicate';
        }
        else
        {
            $client = new Client();
            $crawler = $client->request('GET', $url);

            //get internal links
            $external_link = array();
            $internal_link = array();
            try{
                    foreach ($crawler->filter('a') as $a) {
                        $a_links[] = $a->getAttribute('href');
                    }
                       //  dd($a_links);
                    //extract Domain
                    $domain = parse_url($url, PHP_URL_HOST);

                    $domain_url = str_replace('www.', '', $domain);
                    
                    foreach ($a_links as $lnk) {
                 
                        if (strpos($lnk, $domain_url) !== false) {
                            $internal_link[] = $lnk;
                        } else {
                            $external_link[] = $lnk;
                            
                        }
                    }
                       //filter out hashtag links ?query parameters and maito, tel links
                        foreach ($internal_link as $key => $value) {
                            # code...
                            if(strpos($value, '#') === false || strpos($value, 'mailto') === false || strpos($value, '?') === false){
                                    $internal_link_filter[] = $value;
                            }
                        }
                           // dd($internal_link_filter);
                    $pages_link = array_unique(array_filter($internal_link_filter));
                

                    foreach ($pages_link as $val) {
                        if (parse_url($val, PHP_URL_SCHEME) === 'https' || parse_url($val, PHP_URL_SCHEME) === 'http') {
                            $internal_pages[] = $val;
                        }
                    }
                    if(empty($internal_pages)){
                        $links = $this->get_a_href($url);
                        $internal_pages = array_unique($links['InternalLinks']);
                    }
            }catch(Exception $e){
               $internal_pages[] = $url;
              // dd($e);
            }

                try{
                    $a = '/';
                    $pages = array();
                    foreach ($internal_pages as $val){
                        
                        if(strpos($val,"facebook") == false && strpos($val,"twitter") == false && strpos($val,"linkedin") == false && strpos($val,"instagram") == false && strpos($val, '#') == false && strpos(parse_url($val)['host'],$domain_url) !== false){
                            if(empty(parse_url($val)['path'])){
                            array_push($pages, $val .= $a);
                                }else{
                                array_push($pages,$val);
                                }
                        }
                    }
                }catch(Exception $e){
                    Log::error($e);
                }
                $internal_page = array_unique($pages);
               
                
            try {
                    // $short_meta_description = array();
                    // $long_meta_description = array();
                    // $page_link_description = array();
                    // $page_null_description = array();
                    // $status301 = array();
                    // $status302 = array();
                    // $status404 = array();
                    // $status500 = array();
                    // $link_301 = array();
                    // $link_302 = array();
                    // $link_404 = array();
                    // $link_500 = array();
                    // $total_meta = array();
                    // $links_more_h1 = array();
                    // $duplicate_h1 = array();
                    // $links_empty_h1 = array();
                    // $long_title = array();
                    // $url_length = array();
                    // $less_page_words = array();
                    // $graph_data = array();
                    // $less_code_ratio = array();
                    // $page_miss_meta = array();
                    // $page_incomplete_card = array();
                    // $page_incomplete_graph = array();
                    // $page_miss_title = array();
                    // $duplicate_title = array();
                    // $twitter = array();
                    // $passed_pages = array();
                    // $page_without_canonical = array();
                //$duplicate_meta_description = array();
                foreach ($internal_page as $val) {
                    
                    $crawler = $client->request('GET', $val);
                    
                    $h1 = $crawler->filter('h1')->each(function ($node) {
                        return $node->text();
                    });
           
                    if (count($h1) > 1) {
                        $links_more_h1[] = $val;
                    }elseif (count($h1) < 1 && strpos($val,"twitter") == false && strpos($val,"facebook") == false && strpos($val,"linkedin") == false && strpos($val,"instagram") == false ) {
                        $links_empty_h1[] = $val;
                    }
                    if(count(array_unique($h1)) < count($h1)){
                        $duplicate_h1[] = $val;
                    }
                    
                    $card = $crawler->filter('meta[name="twitter:card"]')->each(function ($node) {
                        return $node->attr('content');
                    });

                    $site = $crawler->filter('meta[name="twitter:site"]')->each(function ($node) {
                        return $node->attr('content');
                    });

                    $title_twitter = $crawler->filter('meta[name="twitter:title"]')->each(function ($node) {
                        return $node->attr('content');
                    });

                    $twitter_description = $crawler->filter('meta[name="twitter:description"]')->each(function ($node) {
                        return $node->attr('content');
                    });
                    

                    $image_twitter = $crawler->filter('meta[name="twitter:image"]')->each(function ($node) {
                        return $node->attr('content');
                    });

                    $creator_twitter = $crawler->filter('meta[name="twitter:creator"]')->each(function ($node) {
                        return $node->attr('content');
                    });

                    if(empty($card) || empty($site) || empty($title_twitter) || empty($twitter_description) || empty($image_twitter) || empty($creator_twitter)){
                        $page_incomplete_card[] = $val;
                    }

                    $a = array();
                    $twitter[] = array_push($a, $card, $site, $title_twitter, $twitter_description, $image_twitter,$creator_twitter);

                    
                    $graph_type = $crawler->filter('meta[property="og:type"]')->each(function ($node) {
                        return $node->attr('content');
                    });
                    $graph_title = $crawler->filter('meta[property="og:title"]')->each(function ($node) {
                        return $node->attr('content');
                    });
                    
                    $graph_description = $crawler->filter('meta[property="og:description"]')->each(function ($node) {
                        return $node->attr('content');
                    });
                    $graph_image = $crawler->filter('meta[property="og:image"]')->each(function ($node) {
                        return $node->attr('content');
                    });
                    $graph_name = $crawler->filter('meta[property="og:site_name"]')->each(function ($node) {
                        return $node->attr('content');
                    });
                    $graph_url = $crawler->filter('meta[property="og:url"]')->each(function ($node) {
                        return $node->attr('content');
                    });

                    if(empty($graph_type) || empty($graph_title) || empty($graph_description) || empty($graph_image) || empty($graph_url)){
                        $page_incomplete_graph[]  = $val;
                    }

                    $b = array();
         
                   // $graph_data[] = null;

                    $title = $crawler->filter('title')->html();
                    if (!empty($title)) {
                        $page_with_title[] = $val;
                    } else {
                        $page_miss_title[] = $val;
                    
                    }
                   
                    if (strlen($title) < 50) {
                        $short_title[] = $val;
                    } elseif (strlen($title) > 60) {
                        $long_title[] = $val;
                    }
                    
                    $total_title[] = $title;

                    if(strlen($val)>115){
                        $url_length[] = $val;
                    }
                       // dd($val);
                    //page word count
                    $page = strip_tags($crawler->html());
                    $exp = explode(" ", $page);
                    $page_words = count($exp);
                
                   
                    if ($page_words < 600) {
                        $less_page_words[] = $val;
                    }

                    //Text-HTML ratio
                    $size = strlen($crawler->html());
                    $page_size = round(strlen($crawler->html()) / 1024, 4);
                    $page_text_ratio = $page_words / $size * 100;
                    $page_words_size = round($page_words / 1024, 4);
                    if ($page_text_ratio < 10) {
                        $less_code_ratio[] = $val;
                    }
                    //$less_page[] = $page_words;
                    $page_html = preg_replace('#<[^>]+>#', ' ', $crawler->html());
                    $html_page = explode("\t\t\t", $page_html);
                    $html_values = preg_replace('/\s+/', ' ', $html_page);
                    $unique = array_unique($html_values);
                    $duplicates = array_diff_assoc($html_values, $unique);

                    foreach (array_map('trim', $duplicates) as $value) {
                        if ($value === $title) {
                            $duplicate_title[] = $val;
                        }
                    }
                    $m_can = array();
                    foreach ($crawler->filter('link[rel="canonical"]') as $can) {
                        if (!empty($can->getAttribute('href'))) {
                            $link_canonical[] = $val;
                            $canonical[] = $can->getAttribute('href');
                        }else{
                            array_push($m_can,$val);
                        }
                        
                    }
                    
                    //meta description
                    foreach ($crawler->filter('meta[name="description"]') as $desc) {
                        $meta = $desc->getAttribute('content');
                      
                        if (!empty($meta)) {
                            $linkss[] = $val;
                            if (strlen($meta) < 120) {
                                $short_meta_description[] = $val;
                            } elseif (strlen($meta) > 160) {
                                $long_meta_description[] = $val;
                            }
                            $page_link_description[] = $val;
                        }else{
                            $page_null_description[] = $val;
                        
                        }
                        $total_meta[] = $meta;
                    }

                    
                    $redirect_links = get_headers($val);
                    preg_match('/\s(\d+)\s/', $redirect_links[0], $matches);
                    if($matches[0] == 200){
                        $status_200[] = $matches[0];
                        $link_200[] = $val;
                    }elseif($matches[0] == 301) {
                        $status_301[] = $matches[0];
                        $link_301[] = $val;
                    } elseif ($matches[0] == 302) {
                        $status_302[] = $matches[0];
                        $link_302[] = $val;
                    } elseif ($matches[0] == 404) {
                        $status_404[] = $matches[0];
                        $link_404[] = $val;
                    
                    } elseif ($matches[0] == 500) {
                        $status_500[] = $matches[0];
                        $link_500[] = $val;
                        
                    }
                    $pages[] = $val;


                }

            } catch (Exception $e) {
              Log::error($e);
            }
        
            //meta duplicate
            try {
    
                $arr = array_combine($linkss,$total_meta);
                $counts = array_count_values($arr);
                $duplicate_meta_description  = array_filter($arr, function ($value) use ($counts) {
                    return $counts[$value] > 1;
                });

            } catch(Exception $e) {
                // Log::error($e);
            }

            //Robot.txt
            try {
                $get_robot = file_get_contents($url . "/robots.txt");
                $robots = explode(" ", (str_replace("\r\n", " ", $get_robot)));
                $robot_txt = array_chunk($robots, 1);
                $robot= array();
                foreach ($robot_txt as $dat) {
                    $rob = $dat;
                    foreach ($rob as $data) {
                        $robot[] = $data;
                    }
                }
            } catch (Exception $e) {
                // Log::error($e);
            }
            
            // miss canonical and page miss meta
            try{
                $miss =  array_diff(array_unique($pages),$linkss);
                $page_miss_meta = array();
                foreach($miss as $d){
                    if(strpos($d,"facebook") == false && strpos($d,"twitter") == false && strpos($d,"linkedin") == false && strpos($d,"instagram") == false){
                        array_push($page_miss_meta,$d);
                    }
                }

                $can = array_diff(array_unique($pages),$link_canonical);
                $page_without_canonical = array();
                foreach($can as $d){
                    if(strpos($d,"facebook") == false && strpos($d,"twitter") == false && strpos($d,"linkedin") == false && strpos($d,"instagram") == false){
                        array_push($page_without_canonical,$d);
                    }
                }
            }catch(Exception $e){
                 Log::error($e);
            }
            
            //duplicate title
            try{
                $array = array_combine($page_with_title,$total_title);
                $counts = array_count_values($array);
                $duplicate_title  = array_filter($array, function ($value) use ($counts) {
                    return $counts[$value] > 1;
                });
                
            }catch(Exception $e){
                 Log::error($e);
            }
           
            //Notices Score Count
            try{
        
                if(!empty($links_more_h1)){
                    $h1_count_more = count($links_more_h1);
                }else{
                    $h1_count_more = 0;
                }
               
                if(empty($twitter)){
                    $twitter_count = 1;
                }else{
                    $twitter_count = 0;
                }

                if(empty($graph_data)){
                    $graph_count = 1;
                }else{
                    $graph_count = 0;
                }


                if(!empty($url_length)){
                    $url_length_count = count($url_length);
                }else{
                    $url_length_count = 0;
                }
                if(empty($robot)){
                    $robot_count = 1;
                }else{
                    $robot_count = 0;
                }
                if(!empty($page_incomplete_graph)){
                    $page_incomplete_graph_count = count($page_incomplete_graph);
                }else{
                    $page_incomplete_graph_count = 0;
                }

            $notices = $h1_count_more+$twitter_count+$graph_count+$url_length_count+$robot_count+$page_incomplete_graph_count;

            }catch(Exception $e){
                 Log::error($e);
            }
            //dd($robot_count);
            //Warning Score Count
            try{
                if(!empty($less_code_ratio)){
                    $less_code_ratio_count = count($less_code_ratio);
                }else{
                    $less_code_ratio_count = 0;
                }
                if(!empty($less_page_words)){
                    $less_page_words_count = count($less_page_words);
                }else{
                    $less_page_words_count = 0;
                }

                if(!empty($duplicate_h1)){
                    $duplicate_h1_count = count($duplicate_h1);
                }else{
                    $duplicate_h1_count = 0;
                }

                if(!empty($page_incomplete_card)){
                    $page_incomplete_card_count = count($page_incomplete_card);
                }else{
                    $page_incomplete_card_count = 0;
                }
                if(!empty($link_301)){
                    $link_301_count = count($link_301);
                }else{
                    $link_301_count = 0;
                }
                if(!empty($link_302)){
                    $link_302_count = count($link_302);
                }else{
                    $link_302_count = 0;
                }
                if(!empty($page_without_canonical)){
                    $page_without_canonical_count = count($page_without_canonical);
                   
                }else{
                    $page_without_canonical_count = 0;
                }

                
            }catch(Exception $e){
                 Log::error($e);
            }
           // dd($short_title_count);
            $warning = $less_page_words_count + $duplicate_h1_count + $page_incomplete_card_count
                            + $link_301_count + $link_302_count +
                         $page_without_canonical_count + $less_code_ratio_count;
          
            //Errors
            try{
                $health=array();
                if(!empty($link_404)){
                    $link_404_count = count($link_404);
                    array_push($health,$link_404);
                }else{
                    $link_404_count = 0;
                }

                if(!empty($link_500)){
                    $link_500_count = count($link_500);
                    array_push($health,$link_500);
                }else{
                    $link_500_count = 0;
                }

                if(!empty($duplicate_title)){
                    $duplicate_title_count = count($duplicate_title);
                    array_push($health,array_keys($duplicate_title));
                }else{
                    $duplicate_title_count = 0;
                }

               
                if(!empty($duplicate_meta_description)){
                    $duplicate_meta_description_count = count($duplicate_meta_description);
                    array_push($health,array_keys($duplicate_meta_description));
                }else{
                    $duplicate_meta_description_count = 0;
                }
                if(!empty($page_miss_meta)){
                    $page_miss_meta_count = count($page_miss_meta);
                    array_push($health,array_keys($page_miss_meta));
                }else{
                    $page_miss_meta_count = 0;
                }
                if(!empty($links_empty_h1)){
                    $links_empty_h1_count = count($links_empty_h1);
                    array_push($health,array_keys($links_empty_h1));
                }else{
                    $links_empty_h1_count = 0;
                }
                if(!empty($short_title)){
                    $short_title_count = count($short_title);
                    array_push($health,array_keys($short_title));
                }else{
                    $short_title_count = 0;
                    $short_title = 0;
                }

                if(!empty($long_title)){
                    $long_title_count = count($long_title);
                    array_push($health,array_keys($long_title));
                }else{
                    $long_title_count = 0;
                }
                
                if(!empty($short_meta_description)){
                    $short_meta_description_count = count($short_meta_description);
                    array_push($health,array_keys($short_meta_description));
                }else{
                    $short_meta_description_count = 0;
                }

                if(!empty($long_meta_description)){
                    $long_meta_description_count = count($long_meta_description);
                    array_push($health,array_keys($long_meta_description));
                }else{
                    $long_meta_description_count = 0;
                }

                $errors = $link_404_count+$link_500_count+$duplicate_title_count+
                $duplicate_meta_description_count+$page_miss_meta_count+
                $links_empty_h1_count+$short_title_count+$long_title_count+$short_meta_description_count+$long_meta_description_count;
            }catch(Exception $e){
                if(empty($errors)){
                    $errors = 0;
                }
                 Log::error($e);
            }
            try{
                $page_with_errors = []; 
                foreach ($health as $childArray) 
                { 
                    foreach ($childArray as $value) 
                    { 
                    $page_with_errors[] = $value; 
                    } 
                }
                        if(count($pages) > 0){
                $data = count(array_unique($page_with_errors))/count($pages);
             } else {
                $data = 0;
             }
                $health_score = (1-($data))*100;
                $pages = count($pages);
                $passed_pages = $pages - count($page_with_errors);
                 if($health_score > 80){
            $audit_description = "Your website SEO is good!";
            } elseif ($health_score > 60) {
            $audit_description = "Your website SEO needs work!";
            } else {
              $audit_description = "Your website SEO is weak!";  
            }
            }catch(Exception $e){
             //   dd($e);
                 Log::error($e);
            }

                if(empty($link_302)){
                    $link_302 = null;
                } else {
                    $link_302 = json_encode($link_302);
                }
                if(empty($link_301)){
                    $link_301 = null;
                } else {
                    $link_301 = json_encode($link_301);
                }
                if(empty($link_404)){
                    $link_404 = null;
                } else {
                    $link_404 = json_encode($link_404);
                }
                if(empty($link_500)){
                    $link_500 = null;
                } else {
                    $link_500 = json_encode($link_500);
                }
                if(empty($status_302)){
                    $status_302 = null;
                } else {
                    $status_302 = json_encode($status_302);
                }
                if(empty($status_301)){
                    $status_301 = null;
                } else {
                    $status_301 = json_encode($status_301);
                }
                if(empty($status_404)){
                    $status_404 = null;
                } else {
                    $status_404 = json_encode($status_404);
                }
                if(empty($status_500)){
                    $status_500 = null;
                } else {
                    $status_500 = json_encode($status_500);
                }
                 if(empty($less_page_words)){
                    $less_page_words = null;
                } else {
                    $less_page_words = json_encode($less_page_words);
                }
                 if(empty($duplicate_h1)){
                    $duplicate_h1 = null;
                } else {
                    $duplicate_h1 = json_encode($duplicate_h1);
                }

                 if(empty($long_meta_description)){
                    $long_meta_description = null;
                } else {
                    $long_meta_description = json_encode($long_meta_description);
                }

                 if(empty($short_meta_description)){
                    $short_meta_description = null;
                } else {
                    $short_meta_description = json_encode($short_meta_description);
                }

                 if(empty($long_title)){
                    $long_title = null;
                } else {
                    $long_title = json_encode($long_title);
                }
             
                 if(empty($short_title)){
                    $short_title = null;
                } else {
                    $short_title = json_encode($short_title);
                }

                  if(empty($links_more_h1)){
                    $links_more_h1 = null;
                } else {
                    $links_more_h1 = json_encode($links_more_h1);
                }
                   if(empty($links_empty_h1)){
                    $links_empty_h1 = null;
                } else {
                    $links_empty_h1 = json_encode($links_empty_h1);
                }
                if(empty($graph_data)){
                    $graph_data = null;
                } else {
                    $graph_data = json_encode($graph_data);
                }
                if(empty($less_code_ratio)){
                    $less_code_ratio = null;
                } else {
                    $less_code_ratio = json_encode($less_code_ratio);
                }
                if(empty($robot)){
                    $robot = null;
                } else {
                    $robot = json_encode($robot);
                }
                 if(empty($url_length)){
                    $url_length = null;
                } else {
                    $url_length = json_encode($url_length);
                }
                   if(empty($page_miss_meta)){
                    $page_miss_meta = null;
                } else {
                    $page_miss_meta = json_encode($page_miss_meta);
                }
                   if(empty($duplicate_meta_description)){
                    $duplicate_meta_description = null;
                } else {
                    $duplicate_meta_description = json_encode($duplicate_meta_description);
                }
                   if(empty($page_incomplete_card)){
                    $page_incomplete_card = null;
                } else {
                    $page_incomplete_card = json_encode($page_incomplete_card);
                }
                   if(empty($page_incomplete_graph)){
                    $page_incomplete_graph = null;
                } else {
                    $page_incomplete_graph = json_encode($page_incomplete_graph);
                }
                   if(empty($page_miss_title)){
                    $page_miss_title = null;
                } else {
                    $page_miss_title = json_encode($page_miss_title);
                }
                   if(empty($duplicate_title)){
                    $duplicate_title = null;
                } else {
                    $duplicate_title = json_encode($duplicate_title);
                }
                   if(empty($twitter)){
                    $twitter = null;
                } else {
                    $twitter = json_encode($twitter);
                }
                   if(empty($page_without_canonical)){
                    $page_without_canonical = null;
                } else {
                    $page_without_canonical = json_encode($page_without_canonical);
                }
            // return view("dashboard/audit_result",
            // compact('url', 'time', 'page_h1_greater', 'page_h1_less', 'long_title', 'short_title','url_length',
            //     'graph_data', 'links_more_h1', 'less_code_ratio', 'short_meta_description',
            //     'long_meta_description', 'robot', 'less_page_words', 'links_empty_h1', 'duplicate_h1',
            //     'page_miss_meta', 'duplicate_meta_description', 'page_incomplete_card', 'page_incomplete_graph', 'status301',
            //     'status302', 'status404', 'status500', 'page_miss_title', 'duplicate_title','twitter',
            //     'link_302','link_301','link_404','link_500','page_without_canonical','notices','warning','errors','passed_pages'
            //     ,'health_score','pages','audit_description'
            // ));
$errors = $link_404_count+$link_500_count+$duplicate_title_count+
                $duplicate_meta_description_count+$page_miss_meta_count+
                $links_empty_h1_count+$short_title_count+$long_title_count+$short_meta_description_count+$long_meta_description_count;

try {
                             $seo_audit_data = new Audit;
                                $seo_audit_data->user_id = auth()->user()->id;
                                $seo_audit_data->payment_id = $Payment->id ?? 0;
                                $seo_audit_data->site_url = $url;
                                $seo_audit_data->long_title = $long_title;
                                $seo_audit_data->short_title = $short_title;
                                $seo_audit_data->url_length = $url_length;
                                $seo_audit_data->graph_data = $graph_data;
                                $seo_audit_data->links_more_h1 = $links_more_h1;
                                $seo_audit_data->less_code_ratio = $less_code_ratio;
                                $seo_audit_data->short_meta_description = $short_meta_description;
                                $seo_audit_data->long_meta_description = $long_meta_description;
                                $seo_audit_data->robot = $robot;
                                $seo_audit_data->less_page_words = $less_page_words;
                                $seo_audit_data->links_empty_h1 = $links_empty_h1;
                                $seo_audit_data->duplicate_h1 =$duplicate_h1;
                                $seo_audit_data->page_miss_meta = $page_miss_meta;
                                $seo_audit_data->duplicate_meta_description = $duplicate_meta_description;
                                $seo_audit_data->page_incomplete_card = $page_incomplete_card;
                                $seo_audit_data->page_incomplete_graph = $page_incomplete_graph;
                                $seo_audit_data->status_301 = $status_301;
                                $seo_audit_data->status_302 = $status_302;
                                $seo_audit_data->status_404 = $status_404;
                                $seo_audit_data->status_500 = $status_500;
                                $seo_audit_data->page_miss_title = $page_miss_title;
                                $seo_audit_data->duplicate_title = $duplicate_title;
                                $seo_audit_data->twitter = $twitter;
                                $seo_audit_data->link_302 = $link_302;
                                $seo_audit_data->link_301 = $link_301;
                                $seo_audit_data->link_404 = $link_404;
                                $seo_audit_data->link_500 = $link_500;
                                $seo_audit_data->page_without_canonical = $page_without_canonical;
                                $seo_audit_data->notices = $notices;
                                $seo_audit_data->warning = $warning;
                                $seo_audit_data->errors = $errors;
                                $seo_audit_data->passed_pages = $passed_pages;
                                $seo_audit_data->health_score = $health_score;
                                $seo_audit_data->pages = $pages;
                                $seo_audit_data->audit_description = $audit_description;
                                $seo_audit_data->save();


                                $create_audit = new AuditResults;
                                $create_audit->user_id =auth()->user()->id;
                                $create_audit->site_url = $url;
                                $create_audit->audit_id = $seo_audit_data->id;
                                $create_audit->payment_id = $Payment->id;
                                $create_audit->save();


                }catch(Exception $e){
                    Log::error($e);
                        }


                       $data = json_encode(array(
                                    'id' => $seo_audit_data->id,
                                    'url' => $url,
                                    'errors' => $errors,
                                    'updated_at' => date('F j, Y, g:i a', time())
                   
                                ));

            return $data;

        }
    }

    public function seo_audit_details($id){
        $audit_results = AuditResults::all()->where('id', $id)->toArray();
           // dd($audit_results);
            $audit_results = current($audit_results); 
        $audit_details = Audit::all()->where('id', $audit_results['audit_id'])->toArray();
        $audit_details = current($audit_details);     
       // dd($audit_details);  
        $user = User::where('id',auth()->user()->id)->first()->toArray();
        $white_label=WhiteLabel::where('user_id',auth()->user()->id)->first();

        //dd($audit_results);
        if($white_label) {
            $white_label = $white_label->image_path;
        } else{
            $white_label = 0;
        }

        if($audit_details['user_id'] == $user['id']){
                return view('dashboard/audit_result',compact('audit_details','white_label'));
        } else{
                return redirect::to('/');
        }
        
           }

 public function delete_audit_report($id){
        Audit::where('id', $id)->delete();
        AuditResults::where('audit_id', $id)->delete();
        return $id;
           }

    public function download_audit_report($id){
             
                // replace default 'chrome' with 'chromium-browser'
                   // $browserFactory = new BrowserFactory('C:\Programs\\Google\\Chrome\\Application\\chrome.exe');
                 //   $browser = $browserFactory->createBrowser();

        $audit_details = AuditResults::all()->where('id', $id)->toArray();
        $audit_details = current($audit_details);
         $white_label=WhiteLabel::where('user_id',auth()->user()->id)->first();
        if(!empty($white_label)) {
            $white_label = $white_label->image_path;
        } else{
            $white_label = 0;
        }
       // $html = \View::make('dashboard/seo_result', compact('seo_audit_details'))->render();
        $html = view('dashboard/audit_result', compact('audit_details', 'white_label'))->render();
        
        Browsershot::html($html)->setNodeBinary('C:\wamp64\bin\nodejs\node.exe')->setNodeModulePath("C:\wamp64\bin\nodejs\node_modules")->setChromePath("C:\Programs\\Google\\Chrome\\Application\\chrome.exe")->setIncludePath('C:\wamp64\bin')->noSandbox()->pdf();

         return 'done';


           }

    public function email_audit_report(Request $request){
  
        $send_to = $request->input('send_to');
             $url = $request->input('url');
             $id = $request->input('id');

                // send seo report email
        $audit_details = AuditResults::select('site_url','audit_id')->where('id', $id)->get()->toArray();
         $audit_details = current($audit_details);
                        Audit::select('site_url','notices','warning','errors','passed_pages','health_score','pages','audit_description','created_at')->where('id', $audit_details['audit_id'])->get()->toArray();
           
            
          // print_r($seo_audit_details);
           
                Mail::send('emails/audit_report', compact('audit_details', 'send_to', 'message'), function ($message) use ($send_to, $audit_details, $url)
                        {
                            $message->from('admin@ninjareports.com', 'Ninja Reports');
                            $message->to($send_to);
                            $message->subject('SEO Audit of '.$url);
                });
                      // check for failures
               if (Mail::failures()) {
                 // return response showing failed emails
                  return 0;
                     } else {
                      return 1;
                     }
           }

    public function get_a_href($url){
        $url = htmlentities(strip_tags($url));
        $ExplodeUrlInArray = explode('/',$url);
        $DomainName = $ExplodeUrlInArray[2];
        $file = @file_get_contents($url);
        $h1count = preg_match_all('/(href=["|\'])(.*?)(["|\'])/i',$file,$patterns);
        $linksInArray = $patterns[2];
        $CountOfLinks = count($linksInArray);
        $InternalLinkCount = 0;
        $ExternalLinkCount = 0;
        $ExternalDomainsInArray = array();
        $InternalDomainsInArray = array();
        for($Counter=0;$Counter<$CountOfLinks;$Counter++){
         if($linksInArray[$Counter] == "" || $linksInArray[$Counter] == "#")
          continue;
        preg_match('/javascript:/', $linksInArray[$Counter],$CheckJavascriptLink);
        if($CheckJavascriptLink != NULL)
        continue;
        $Link = $linksInArray[$Counter];
        preg_match('/\?/', $linksInArray[$Counter],$CheckForArgumentsInUrl);
        if($CheckForArgumentsInUrl != NULL)
        {
        $ExplodeLink = explode('?',$linksInArray[$Counter]);
        $Link = $ExplodeLink[0];
        }
        preg_match('/'.$DomainName.'/',$Link,$Check);
        if($Check == NULL)
        {
        preg_match('/(http|https):\/\//',$Link,$ExternalLinkCheck);
        if($ExternalLinkCheck == NULL)
        {
        $InternalDomainsInArray[$InternalLinkCount] = $Link;
        $InternalLinkCount++;
        }
        else
        {
        $ExternalDomainsInArray[$ExternalLinkCount] = $Link;
        $ExternalLinkCount++;
        }
        }
        else
        {
        $InternalDomainsInArray[$InternalLinkCount] = $Link;
        $InternalLinkCount++;
        }
        }
        $LinksResultsInArray = array(
        'ExternalLinks'=>$ExternalDomainsInArray,
        'InternalLinks'=>$InternalDomainsInArray
        );
        return $LinksResultsInArray;
    }

    public function get_seo($url,$Payment,$time){
        $client = new Client();
        $crawler = $client->request('GET', $url);
              
        //Mobile Friendly test
        try{
            $urls = "https://searchconsole.googleapis.com/v1/urlTestingTools/mobileFriendlyTest:run?key=AIzaSyAHRm6Jkj3mkwZkpvUK1H4haBgGT7_mj8k";

            $curl = curl_init($urls);
            curl_setopt($curl, CURLOPT_URL, $urls);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $headers = array();
            
            $headers = [
                'Accept:application/json',
                'Content-Type:application/json',
            ];
            
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = '{url: "'.$url.'"}';
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);
            $mobile = json_decode($resp, true);
            $mobile_friendly = $mobile['mobileFriendliness'];
            if(empty($mobile_friendly))   {
                $mobile_friendly = 'Error';
            }     
        }catch(Exception $e){}


          //pagespeed test
        try{

            $url_req = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url='.$url.'&screenshot=true&key=AIzaSyAHRm6Jkj3mkwZkpvUK1H4haBgGT7_mj8k';  

                if (function_exists('file_get_contents')) {    
                    $result = @file_get_contents($url_req);
                  }    
                  if ($result == '') {    
                  $ch = curl_init();    
                  $timeout = 60;    
                  curl_setopt($ch, CURLOPT_URL, $url_req);    
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
                  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
                  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
                  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);    
                  $result = curl_exec($ch);    
                  curl_close($ch);    
                 }    

                $pagespeed = json_decode($result, true);

                // Grab the Screenshot data.
               // $screenshot = $pagespeed['screenshot']['data'];

            $screenshot = str_replace('_','/',$pagespeed['lighthouseResult']['audits']['final-screenshot']['details']['data']);
            $performance_score = $pagespeed['lighthouseResult']['categories']['performance']['score'];
            $loadtime = $pagespeed['lighthouseResult']['audits']['speed-index']['displayValue'];
            $fcp = $pagespeed['lighthouseResult']['audits']['first-contentful-paint']['displayValue'];
            $lcp = $pagespeed['lighthouseResult']['audits']['largest-contentful-paint']['displayValue'];
            $cls = $pagespeed['lighthouseResult']['audits']['cumulative-layout-shift']['displayValue'];
            $responsive_images = $pagespeed['lighthouseResult']['audits']['uses-responsive-images']['displayValue'] ?? null;
            $css_min = $pagespeed['lighthouseResult']['audits']['unminified-css']['displayValue'] ?? null;
            $css_min_score = $pagespeed['lighthouseResult']['audits']['unminified-css']['score'] ?? null; 
            $css_min_bytes = $pagespeed['lighthouseResult']['audits']['unminified-css']['details']['items'][1]['wastedBytes'] ?? null;
            $js_min = $pagespeed['lighthouseResult']['audits']['unminified-javascript']['displayValue'] ?? null;
            $js_min_score = $pagespeed['lighthouseResult']['audits']['unminified-javascript']['score'] ?? null;
            $js_min_bytes = $pagespeed['lighthouseResult']['audits']['unminified-javascript']['details']['items'][1]['wastedBytes'] ?? null;
            $gzip_compression = $pagespeed['lighthouseResult']['audits']['uses-text-compression']['details']['items'][1]['wastedBytes'] ?? null;
            
        }catch(Exception $e){
            //dd($e);
        }
        
       //backlink count
        if($Payment != NULL) {
            // if is a paid user, show the backlink counts
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
        //if not paid user, show upgrade teasers
    } else {
            $domains_num =  'payme';
            $urls_num = 'payme';

        } // end if

     //backlink data
        if($Payment != NULL) {
            // if is a paid user, show the backlink data
        try{
            $semrush_backlinks = "https://api.semrush.com/analytics/v1/?key=247c8d4143eff74adb96fb2f0b3f3d8a&type=backlinks&target=".$url."&target_type=url&export_columns=source_url,anchor,external_num,internal_num&display_limit=3";

            $curl = curl_init($semrush_backlinks);
            curl_setopt($curl, CURLOPT_URL, $semrush_backlinks);
            curl_setopt($curl, CURLOPT_POST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);
             if (strpos($resp,'ERROR ')===0) {
                //error
                $semrush_links = 'empty';
                } else {
                // no error
                $semrush_links = preg_split('/\r*\n+|\r+/', $resp);
                array_pop($semrush_links);
                foreach ($semrush_links as $key => $value) {

                    $semrush_links[$key] = explode(';', $value);
            
                }
            } 
        }catch(Exception $e){}

         } else {
                $semrush_links = 'payme';

        } // end if

         //top keywords
        if($Payment != NULL) {
            // if is a paid user, show the organic keywords
        try{
            $semrush_keywords = "https://api.semrush.com/?key=247c8d4143eff74adb96fb2f0b3f3d8a&type=url_organic&database=us&url=".$url."&display_limit=5&export_columns=Ph,Po,Nq,Co,Kd,Tg";

            $curl = curl_init($semrush_keywords);
            curl_setopt($curl, CURLOPT_URL, $semrush_keywords);
            curl_setopt($curl, CURLOPT_POST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);

             if (strpos($resp,'ERROR ')===0) {
                //error
                $keyword_list = NULL;
                }
                 else {
                // no error
                $keyword_list = preg_split('/\r*\n+|\r+/', $resp);
                array_pop($keyword_list);
                foreach ($keyword_list as $key => $value) {

                    $keyword_list[$key] = explode(';', $value);
            
                }
            } 

        }catch(Exception $e){}

         } else {
                $keyword_list = 'payme';

        } // end if

        //Image Size
        try {
            foreach ($crawler->filter('img') as $img) {
                if (filter_var($img->getAttribute('src'), FILTER_VALIDATE_URL)) {
                    $header_response = get_headers($img->getAttribute('src'), 1);
                    if (strpos($header_response[0], "200") !== false) {
                        $location[] = $img->getAttribute('src');
                        $bytes = $header_response["Content-Length"];
                        $image_size[] = $bytes / 1024;
                    }
                } else {
                    $pat_error[] = $img->getAttribute('src');
                }
            }
            $img_data = array_combine($location, $image_size);
        } catch (Exception $e) {}
       
        //Schema
        try {
            $schema = $crawler->filterXpath('//script[@type="application/ld+json"]')->text();
            $schema_org = json_decode($schema, true);
            $schema_types = array();
            foreach ($schema_org['@graph'] as $key => $value) {
                $schema_types[$key] = $value['@type'];
            } 

            if (empty($schema_org['@graph'][0]['@type'])) {
                $org_schema = $schema_org['@type'];
                $name_schema = $schema_org['name'];
                $social_schema = $schema_org['sameAs'];
            } else {
                $org_schema = $schema_org['@graph'][0]['@type'];
                $name_schema = $schema_org['@graph'][0]['name'];
                $social_schema = $schema_org['@graph'][0]['sameAs'];
            }
        } catch (Exception $e) {}
        
        //As page on HTTPS
        try {
            if (parse_url($url, PHP_URL_SCHEME) === 'https') {
                $page_https = 'Page using HTTPS';
            } else {
                $page_https = 'Page not on HTTPS';

            }
        } catch (Exception $e) {}
        //SSL Checker
        try {
            $orignal_parse = parse_url($url, PHP_URL_HOST);
            $get = stream_context_create(array("ssl" => array("capture_peer_cert" => true)));
            $read = stream_socket_client("ssl://" . $orignal_parse . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
            $cert = stream_context_get_params($read);
            $ssl_certificate = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

        } catch (Exception $e) {}

        //Get Internal External links
        try {
            foreach ($crawler->filter('a') as $a) {
                $a_links[] = $a->getAttribute('href');
            }
            //extract Domain
            $internal_link = array();
            $external_link = array();


                    $domain = parse_url($url, PHP_URL_HOST);

                    $domain_url = str_replace('www.', '', $domain);
                    
                    foreach ($a_links as $lnk) {
                 
                        if (strpos($lnk, $domain_url) !== false) {
                            $internal_link[] = $lnk;
                        } else {
                            $external_link[] = $lnk;
                            
                        }
                    }

                        $external_link = array_unique(array_filter($external_link));
                        $internal_link = array_unique(array_filter($internal_link));

                            if(empty($internal_link)){
                        $links = $this->get_a_href($url);
                        $internal_link = array_unique($links['InternalLinks']);
                            }

                       
        } catch (Exception $e) {}

        //Link To Social Media Page
        try {
            $social_link = array('facebook', 'linkedin', 'twitter', 'youtube', 'instagram');
        
           
            foreach ($external_link as $ext) {
                
                foreach ($social_link as $social) {
                    if (strpos($ext, $social)) {
                        $link_to_social[] = $ext;
                        $social_media_link = 'Links to social media profiles found.';
                    }
                }
            }
            //dd($link_to_social,$external_link);
            // foreach ($link_to_social as $val) {
            //     $path = parse_url($val, PHP_URL_PATH);
            //     if (strpos($path, pathinfo($domain_url, PATHINFO_FILENAME))) {
            //         $social_media_link = 'Link to social media profiles found';
            //     }
            // }

        } catch (Exception $e) {}

        //link pointing non https pages
        try {
            foreach ($crawler->filter('a') as $a) {
                $a_link[] = $a->getAttribute('href');

            }
            $count = 0;
            foreach ($a_link as $val) {
                if (parse_url($val, PHP_URL_SCHEME) !== 'https') {
                    $a_https = $count + 1;
                }
                $count++;
            }

            foreach ($crawler->filter('link') as $a) {
                $link[] = $a->getAttribute('href');

            }

            $count = 0;
            foreach ($link as $val) {
                if (parse_url($val, PHP_URL_SCHEME) !== 'https') {
                    $link_https = $count + 1;
                } else {
                    $link_https = 0;
                }
                $count++;
            }

            foreach ($crawler->filter('script') as $a) {
                $script[] = $a->getAttribute('src');

            }
            $count = 0;
            foreach ($script as $val) {
                if (parse_url($val, PHP_URL_SCHEME) !== 'https') {
                    $script_https = $count + 1;
                }
                $count++;
            }
        } catch (Exception $e) {}

        //Robot.txt Checking
        try {
            $newUrl = $this->stripUrlPath($url);
            $get_robot = file_get_contents($newUrl . "/robots.txt");
            $robots = explode(" ", (preg_replace("/\r|\n/", " ", $get_robot)));
            $robot_txt = array_chunk($robots, 1);
            if (in_array("Sitemap:", $robots)) {
                $sitemap = 1;
            }
            foreach ($robot_txt as $val) {
                $rob = $val;
                foreach ($val as $data) {
                    $robot[] = $data;
                }
            }
        } catch (Exception $e) {}
        //dd();
        //Check Broken Links
        try {
            foreach ($external as $ext) {
                $headers = get_headers($ext);
                preg_match('/\s(\d+)\s/', $headers[0], $matches);
                if ($matches[0] == 301) {
                    $status_301[] = $matches[0];
                } elseif ($matches[0] == 302) {
                    $status_302[] = $matches[0];
                } elseif ($matches[0] == 404) {
                    $status_404[] = $matches[0];
                }
            }
        } catch (Exception $e) {}

        //Browser Cache Checking
        try {
            $headers = get_headers($url);
            foreach ($headers as $header) {
                if (stripos($header, "Cache-Control") !== false) {
                    $cache = $header;
                } else{
                    $cache = null;
                }
            }
        } catch (Exception $e) {
        }

        //HTTP Request & Content Breakdown
        try {
            foreach ($headers as $header) {
                if (strpos($header, "HTTP") !== false) {
                    $http[] = $header;
                }
            }
        } catch (Exception $e) {
        }
        //dd($url);
        //page word count
        
        $str = $crawler->html();
        
        $search = array('@<script[^>]*?>.*?</script>@si',  
        '@<head>.*?</head>@siU',            
        '@<style[^>]*?>.*?</style>@siU',    
        '@<![\s\S]*?--[ \t\n\r]*>@'         
        );

        $contents = preg_replace($search," ", $str);

        $word_count = $this->extractKeyWords(strip_tags($contents))[0];
        $words = str_word_count(strtolower($contents),1);
        $t =preg_replace("/[^A-Za-z0-9 ]/", '', strip_tags($contents));
        $page_word = array_unique(array_filter(explode(" ",strtolower($t))));
        $page_w = array_filter(explode(" ",strtolower($t)));
        $stopWords = array('the','of','and','a','to','in','is','you'
        ,'that','it','he','was','for','on','are','as','with','his','they',
        'i','at','be','this','have','from','or','one','had','by','word','but','not','what',
        'all','where','we','when','your','can','said','there','use','an','each','which','she','do','how'
        ,'their','if','will','up','other','about','out','many','then','them','these','so'
        ,'some','her','would','make','like','him','into','time','has','look','two',
        'more','write','go','see','number','no','way','could','people','my','than','first',
        'water','been','call','who','oil','its','now','find','long','down','day','did','get','come','made','may','part');
        $item = array_diff($page_word,$stopWords);
        $result  = array_filter($item, function ($value) use ($item) {
            return !is_numeric($value);
        });
        $word = count($result);


        $word_page  = array_filter($page_w, function ($value) use ($item) {
            return !is_numeric($value);
        });
        $page_words = count($word_page);

        //Text-HTML ratio
        $size = strlen(implode(' ', array_unique(explode(' ', $contents))));
        $page_size = round(strlen($crawler->html()) / 1024, 4);
        $page_text_ratio = $page_words / $size * 100;
        $page_words_size = round($page_words / 1024, 4);
        //URL Seo Result
        $url_len = strlen($url);
        if (preg_match("/[A-Z_]/", $url, $matches)) {
            $url_seo_friendly = "Unfriendly SEO URLs";
        } else if ($url_len > 75) {
            $url_seo_friendly = "Long URL";
        } else {
            $url_seo_friendly = "SEO-Friendly";
        }

        //title & title length
        $titl = $crawler->filter('title')->html();
        if (strpos($titl, '&amp;') !== false) {
            $title = str_replace("&amp;","&",$titl);
        }else{
            $title = $titl;
        }
        
        $title_length = strlen($title);

        //canonical Link
        try {
            $canonical = $crawler->filterXpath('//link[@rel="canonical"]')->attr('href');
        } catch (Exception $e) {
        }

        //meta description & length
        try {
            $meta = $crawler->filterXpath('//meta[@name="description"]')->attr('content');
        } catch (Exception $e) {
            $meta = '';
        }
        $meta_length = strlen($meta);

        //Favicon
        try {
            $newUrl = $this->stripUrlPath($url);
            $newUrl = rtrim($newUrl,"/");
            foreach ($crawler->filter('link') as $a) {
                if(strpos($a->getAttribute('href'), 'favicon') !== false){

                $favicon = $a->getAttribute('href');
                $parsed = parse_url($favicon);

                        if($parsed['scheme'] == 'https' || $parsed['scheme'] == 'http'){
                            
                             $favicon = $favicon;
                        } else{
                             $favicon =  $newUrl.$favicon;
                        }
                }elseif(strpos($a->getAttribute('rel'), 'shortcut') !== false){

                    $favicon = $a->getAttribute('href');
                    $parsed = parse_url($favicon);
                   if($parsed['scheme'] == 'https' || $parsed['scheme'] == 'http'){
                            $favicon = $favicon;
                        } else{
                           
                              $favicon =  $newUrl.$favicon;
                        }
                }
                else{
                    $favicon = $crawler->filterXpath('//link[@rel="icon"]')->attr('href');

                    $parsed = parse_url($favicon);
                   if($parsed['scheme'] == 'https' || $parsed['scheme'] == 'http'){
                             $favicon = $favicon;
                        } else{
                           
                             $favicon =  $newUrl.$favicon;
                        }
                }
            }
        }catch(Exception $e){}
        
        try{
            if(empty($favicon)){
                $favicon = $crawler->filterXpath('//link[@rel="shortcut icon"]')->attr('href');
            }
        }catch(Exception $e){}


        //iframe
        try {
            foreach ($crawler->filter('iframe') as $frame) {
                $iframe = $frame->getAttribute('src');
            }

        } catch (Exception $e) {}

        $h1 = $crawler->filter('h1')->each(function ($node) {
            return $node->text();
        });
        $h1_tags = count($h1);

        $h2 = $crawler->filter('h2')->each(function ($node) {
            return $node->text();
        });
        $h2_tags = count($h2);

        $h3 = $crawler->filter('h3')->each(function ($node) {
            return $node->text();
        });
        $h3_tags = count($h3);

        //img Alt tags checks
        try {
            foreach ($crawler->filter('img') as $img) {
                $total[] = $img->getAttribute('alt');
                $all_img_src[] = $img->getAttribute('src');
            }
            $img_without_alt = array();
            foreach ($crawler->filter('img[alt=""]') as $img) {
                //remove non http urls
               if(substr( $string_n, 0, 4 ) === "http")
               {
                $img_without_alt[] = $img->getAttribute('src');
            }
            }

            $img_alt = count($all_img_src) - count($img_without_alt);
            if (empty($img_without_alt)) {
                $img_miss_alt = null;
            } else {
                $img_miss_alt = count($img_without_alt);
            }

        } catch (Exception $e) {
        }

        //Page Score Passed
        try {
            if ($title_length > 50 && $title_length <= 60) {
                $val1_pass = 1;
            } else {
                $val1_pass = 0;
            }
            if ($meta_length >= 120 && $meta_length <= 160) {
                $val2_pass = 1;
            } else {
                $val2_pass = 0;
            }
            if (!empty($canonical)) {
                $val3_pass = 1;
            } else {
                $val3_pass = 0;
            }
            if (!empty($schema_tags)) {
                $val4_pass = 1;
            } else {
                $val4_pass = 0;
            }
            if (empty($img_miss_alt)) {
                $val5_pass = 1;
            } else {
                $val5_pass = 0;
            }
            if ($url_seo_friendly == "Seo Friendly") {
                $val6_pass = 1;
            } else {
                $val6_pass = 0;
            }
            if (!empty($iframe)) {
                $iframe = 0;
                $val7_pass = 0;
            } else {
                $val7_pass = 1;
                $iframe = 1;
            }
            if ($h1_tags > 0) {
                $val8_pass = 1;
            } else {
                $val8_pass = 0;
            }
            if ($h2_tags > 0) {
                $val9_pass = 1;
            } else {
                $val9_pass = 0;
            }
            if ($h3_tags > 0) {
                $val10_pass = 1;
            } else {
                $val10_pass = 0;
            }
            if (!empty($word_count)) {
                $val11_pass = 1;
            } else {
                $val11_pass = 0;
            }
            if ($page_words > 300) {
                $val12_pass = 1;
            } else {
                $val12_pass = 0;
            }
            if (!empty($cache)) {
                $val13_pass = 1;
            } else {
                $val13_pass = 0;
            }
            if (!empty($status404)) {
                $val14_pass = 0;
            } else {
                $val14_pass = 1;
            }
            if ($page_https == "Page using HTTPS") {
                $val15_pass = 1;
            } else {
                $val15_pass = 0;
            }
            if (!empty($a_https) && !empty($link_https) && !empty($script_https)) {
                $val16_pass = 0;
            } else {
                $val16_pass = 1;
            }
            if (!empty($social_media_link)) {
                $val17_pass = 1;
            } else {
                $val17_pass = 0;
            }
            if (!empty($social_schema)) {
                $val18_pass = 1;
            } else {
                $val18_pass = 0;
            }
            if (!empty($sitemap)) {
                $val20_pass = 1;
            } else {
                $val20_pass = 0;
                $sitemap = 0;
            }
            if ($h1_tags > 0) {
                $val21_pass = 1;
            } else {
                $val21_pass = 0;
            }
            if ($h2_tags > 0) {
                $val22_pass = 1;
            } else {
                $val22_pass = 0;
            }
            if ($h3_tags > 0) {
                $val23_pass = 1;
            } else {
                $val23_pass = 0;
            }
            if (!empty($img_data)) {
                $val24_pass = 1;
            } else {
                $val24_pass = 0;
            }
            if (!empty($favicon)) {
                $val25_pass = 1;
            } else {
                $val25_pass = 0;
            }
            if($mobile_friendly === 'MOBILE_FRIENDLY'){
                $val26_pass = 1;
            }elseif($mobile_friendly === 'NOT_MOBILE_FRIENDLY'){
                $val26_pass = 0;
            }else{
                $val26_pass = 0;
            }
            if(!empty($internal_link)){ 
                $val27_pass = 1;
            }else{ 
                $val27_pass = 0;
            }
            if($page_text_ratio > 10){
                $val28_pass = 1;
            }else{
                $val28_pass = 0;
            }   
         
            $http_rquest = 1;

            (int)$total_passed_score = $val1_pass + $val2_pass + $val3_pass + $val4_pass + $val5_pass + $val6_pass + $val7_pass + $val8_pass + $val9_pass  + $val10_pass + $val11_pass + $val12_pass + $val13_pass + $val14_pass + $val15_pass + $val16_pass + $val17_pass
                + $val18_pass + $val20_pass + $val21_pass + $val22_pass + $val23_pass + $val24_pass +  $http_rquest + $val25_pass + $val26_pass + $val27_pass + $val28_pass;
             $passed_score = round(((float)$total_passed_score/30)*100, 0);
            if($passed_score > 80 ){
            $score_description = "Your on-page SEO is good!";
            } elseif ($passed_score > 70) {
            $score_description = "Your on-page SEO could be better!";
            } elseif ($passed_score > 60) {
            $score_description = "Your on-page SEO needs work!";
            } else {
              $score_description = "Your on-page SEO is weak!";  
            }
        } catch (Exception $e) {}
    

        //Page Score Warning
        try {
            if (empty($canonical)) {$val3_warning = 1;} else {$val3_warning = 0;}
            if (!empty($img_miss_alt)) {$val5_warning = 1;} else {$val5_warning = 0;}
            if ($url_seo_friendly == "Seo Friendly") {$val6_warning = 0;} else {$val6_warning = 1;}
            if (empty($word_count)) {$val8_warning = 1;} else {$val8_warning = 0;}
            if ($page_words) {$val9_warning = 0;} else {$val9_warning = 1;}
            if (!empty($cache)) {
                $val10_warning = 0;
            } else {
                $val10_warning = 1;
            }
            if (!empty($robot)) {
                $val18_warning = 0;
            } else {
                $val18_warning = 1;
            }
            $cls_count = str_replace(' s', '', $cls);
            $fcp_count = str_replace(' s', '', $cls);
            $lcp_count = str_replace(' s', '', $lcp);
            if ($cls < 0.01) {$val21_warning = 0;} else {$val21_warning = 1;}
            if ($fcp < 2) {$val22_warning = 0;} else {$val22_warning = 1;}
            if ($lcp < 2.5) {$val23_warning = 0;} else {$val23_warning = 1;}

            if ($sitemap == 1) {$val19_warning = 0;} else {$val19_warning = 1;}

            if (!empty($favicon)) {$val20_pass = 0;} else {$val20_pass = 1;}
            if ($page_words > 300) {$val12_pass = 0;} else {$val12_pass = 1;}

            if(!empty($internal_link)){ $val13_pass = 0;}else{ $val13_pass = 1;}

            if($page_text_ratio > 10){$val14_pass = 0;}else{$val14_pass = 1;}

            (int)$total_warning_score = $val3_warning + $val5_warning + $val6_warning + $val8_warning + $val9_warning + $val10_warning + $val18_warning + $val19_warning + $val20_pass + $val13_pass + $val14_pass + $val21_warning + $val22_warning + $val23_warning;
             
            $warning_score = round(($total_warning_score/14)*100, 0);

               //dd($warning_score);

        } catch (Exception $e) {
            Log::error($e);
        }

        //Page Score Error
        try {

            if (!empty($status404)) {
                $val1_error = 1;
            } else {
                $val1_error = 0;
            }
            if ($page_https == "Page using HTTPS") {
                $val2_error = 0;
            } else {
                $val2_error = 1;
            }
            if (!empty($a_https) && !empty($link_https)  && !empty($script_https)) {
                $val3_error = 1;
            } else {
                $val3_error = 0;
            }
            if ($h1_tags > 0) {
                $val4_error = 0;
            } else {
                $val4_error = 1;
            }
            if ($h2_tags > 0) {
                $val5_error = 0;
            } else {
                $val5_error = 1;
            }
            if ($h3_tags > 0) {
                $val6_error = 0;
            } else {
                $val6_error = 1;
            }
            if ($title_length < 50 || $title_length > 60) {$val7_error = 1;} else {$val7_error = 0;}
            if ($meta_length < 120 || $meta_length > 160) {$val8_error = 1;} else {$val8_error = 0;}

            if($mobile_friendly === 'MOBILE_FRIENDLY'){$val10_error = 0;}elseif($mobile_friendly === 'NOT_MOBILE_FRIENDLY'){$val10_error = 1;}

            (int)$total_error_score = $val1_error + $val2_error + $val3_error + $val4_error + $val5_error + $val6_error + $val7_error + $val8_error + $iframe + $val10_error;

            $error_score = round(($total_error_score/10)*100);

        } catch (Exception $e) {
            Log::error($e);
        }
       
        //page Notices
        try{
            if (!empty($img_data)) {
                $val1_notice = 0;
            } else {
                $val1_notice = 1;
            }
            if (!empty($schema_tags)) {
                $val2_notice = 0;
            } else {
                $val2_notice = 1;
            }
            if (!empty($robot)) {
                $val3_notice = 1;
            } else {
                $val3_notice = 0;
            }
            if (!empty($social_media_link)) {
                $val4_notice = 0;
            } else {
                $val4_notice = 1;
            }
            if (!empty($social_schema)) {
                $val5_notice = 0;
            } else {
                $val5_notice = 1;
            }
            if(empty($all_img_src)){
                $val6_notice = 1;
            }else{
                $val6_notice = 0;
            }
            $notice_score = $val1_notice+$val2_notice+$val3_notice+$val4_notice+$val5_notice+$val6_notice;
            $notice_score = round($notice_score);
        }catch(Exception $e){}

        if(empty($schema_tags)){
            $schema_tags = null;
        }
        if(empty($schema_types)){
            $schema_types = null;
        } else{
            $schema_types = json_encode($schema_types);
        }
            if(empty($ssl_certificate)){
            $ssl_certificate = 0;
            } else {
                $ssl_certificate = 1;
            }
            if(empty($robot)){
            $robot = 0;
            } else {
                $robot = 1;
            }
            if(empty($img_without_alt)){
               $img_without_alt = null;
            } else{
                $img_without_alt = json_encode($img_without_alt);
            }
            if(empty($all_img_src)){
                $all_img_src = null;
            } else{
                $all_img_src = json_encode($all_img_src);
            }
            if(empty($internal_link)){
                $internal_link = null;
            } else{
                $internal_link = json_encode($internal_link);
            }
            if(empty($img_data)){
                $img_data = null;
            }
            if(empty($social_schema)){
                $social_schema = null;
            } else{
                $social_schema = json_encode($social_schema);
            }
            if($keyword_list != null){
                $keyword_list = json_encode($keyword_list);
            } 
                if($cache != null){
                $cache = 1;
            } else{
                $cache = 0;
            }
                if(empty($schema_org)){
                $schema_org = null;
            } else{
                $schema_org = json_encode($schema_org);
            }

               if(empty($warning_score)){
                $warning_score = 0;
            } else{
                $warning_score = $warning_score;
            }
             if(empty($error_score)){
                $error_score = 0;
            } else{
                $error_score = $error_score;
            }

            try{

                                $seo_result_data = new SeoResult;
                                $seo_result_data->user_id = auth()->user()->id;
                                $seo_result_data->payment_id = $Payment->id ?? 0;
                                $seo_result_data->url = $url;
                                $seo_result_data->title = $title;
                                $seo_result_data->title_length = $title_length;
                                $seo_result_data->meta = $meta;
                                $seo_result_data->meta_length = $meta_length;
                                $seo_result_data->img_alt = $img_alt ?? 0;
                                $seo_result_data->img_miss_alt = $img_miss_alt ?? 0;
                                $seo_result_data->iframe = $iframe;
                                $seo_result_data->all_img_src = $all_img_src ?? null;
                                $seo_result_data->canonical = $canonical ?? null;
                                $seo_result_data->img_without_alt = $img_without_alt;
                                $seo_result_data->url_seo_friendly = $url_seo_friendly;
                                $seo_result_data->h1 = json_encode($h1) ?? null;
                                $seo_result_data->h1_tags = $h1_tags;
                                $seo_result_data->h2 = json_encode($h2) ?? null;
                                $seo_result_data->h2_tags = $h2_tags;
                                $seo_result_data->h3 = json_encode($h3) ?? null;
                                $seo_result_data->h3_tags = $h3_tags;
                                $seo_result_data->word_count = json_encode($word_count) ?? null;
                                $seo_result_data->numWords = $numWords ?? 0;
                                $seo_result_data->external_links = json_encode($external_link) ?? null;
                                $seo_result_data->page_words = $page_words ?? '';
                                $seo_result_data->page_size = $page_size ?? '';
                                $seo_result_data->page_text_ratio = $page_text_ratio ?? '';
                                $seo_result_data->page_words_size = $page_words_size ?? '';
                                $seo_result_data->http = json_encode($http) ?? null;
                                $seo_result_data->cache = $cache ?? 0;
                                $seo_result_data->page_https = $page_https ?? '';
                                $seo_result_data->status404 = $status404 ?? null;
                                $seo_result_data->internal_link = $internal_link;
                                $seo_result_data->a_https = $a_https ?? 0;
                                $seo_result_data->link_https = $link_https ?? '';
                                $seo_result_data->script_https = $script_https ?? '';
                                $seo_result_data->social_media_link = $social_media_link ?? null;
                                $seo_result_data->robot = $robot;
                                $seo_result_data->sitemap = $sitemap;
                                $seo_result_data->schema_data = $schema_org;
                                $seo_result_data->social_schema = $social_schema;
                                $seo_result_data->passed_score = $passed_score ?? 0;
                                $seo_result_data->warning_score = $warning_score ?? null;
                                $seo_result_data->error_score = $error_score ?? 0;
                                $seo_result_data->img_data = json_encode($img_data) ?? null;
                                $seo_result_data->favicon = $favicon ?? '';
                                $seo_result_data->mobile_friendly = $mobile_friendly ?? '';
                                $seo_result_data->ssl_certificate = $ssl_certificate;
                                $seo_result_data->notice_score = $notice_score;
                                $seo_result_data->image = $screenshot ?? '';
                                $seo_result_data->score_description = $score_description ?? '';
                                $seo_result_data->word = $word ?? '';
                                $seo_result_data->domains_num = $domains_num ?? '';
                                $seo_result_data->urls_num = $urls_num ?? '';
                                $seo_result_data->keyword_list = $keyword_list;
                                $seo_result_data->schema_types = $schema_types;
                                $seo_result_data->semrush_links = json_encode($semrush_links) ?? null;
                                $seo_result_data->performance_score = $performance_score ?? '';
                                $seo_result_data->loadtime = $loadtime ?? '';
                                $seo_result_data->fcp = $fcp ?? null;
                                $seo_result_data->lcp = $lcp ?? null;
                                $seo_result_data->cls = $cls ?? null;
                                $seo_result_data->responsive_images = $responsive_images ?? null;
                                $seo_result_data->css_min = $css_min ?? null;
                                $seo_result_data->css_min_bytes = $css_min_bytes ??  null;
                                $seo_result_data->js_min = $js_min ?? null;
                                $seo_result_data->js_min_score = $js_min_score ?? null;
                                $seo_result_data->js_min_bytes = $js_min_bytes ?? null;
                                $seo_result_data->gzip_compression = $gzip_compression ?? null;
                                //print_r($seo_result_data);
                                $seo_result_data->save();

                                $create_analysis = new Analysis;
                                $create_analysis->user_id = auth()->user()->id;
                                $create_analysis->site_url = $url;
                                $create_analysis->payment_id = $Payment->id ?? Null;
                                $create_analysis->save();

                                $data = json_encode(array(
                                    'id' => $seo_result_data->id,
                                    'url' => $url,
                                    'passed_score' => $passed_score,
                                    'error_score' => $error_score,
                                    'updated_at' => date('F j, Y, g:i a', time())
                                ));

                                return $data;

                        }catch(Exception $e){
                        return $e;
                        }
    }

    public function download_seo_report($id){
             
                // replace default 'chrome' with 'chromium-browser'
                   // $browserFactory = new BrowserFactory('C:\Programs\\Google\\Chrome\\Application\\chrome.exe');
                 //   $browser = $browserFactory->createBrowser();

        $seo_audit_details = SeoResult::all()->where('id', $id)->toArray();
        $seo_audit_details = current($seo_audit_details);
         $white_label=WhiteLabel::where('user_id',auth()->user()->id)->first();
        if(!empty($white_label)) {
            $white_label = $white_label->image_path;
        } else{
            $white_label = 0;
        }
       // $html = \View::make('dashboard/seo_result', compact('seo_audit_details'))->render();
        $html = view('dashboard/seo_result', compact('seo_audit_details', 'white_label'))->render();
        
        Browsershot::html($html)->setNodeBinary('/usr/share/node')
    ->setNpmBinary('/usr/lib/node_modules/npm')
    ->setChromePath('/bin/chromium-browser')->pdf();

         return 'done';


           }

    public function seo_analysis_details($id){
        $seo_audit_details = SeoResult::all()->where('id', $id)->toArray();
        $seo_audit_details = current($seo_audit_details);
        $user = User::where('id',auth()->user()->id)->first()->toArray();
        $white_label=WhiteLabel::where('user_id',auth()->user()->id)->first();
        if($white_label) {
            $white_label = $white_label->image_path;
        } else{
            $white_label = 0;
        }

        if($seo_audit_details['user_id'] == $user['id']){
                return view('dashboard/seo_result',compact('seo_audit_details','white_label'));
        } else{
                return redirect::to('/');
        }
        
           }


    public function email_seo_report(Request $request){
  
        $send_to = $request->input('send_to');
             $url = $request->input('url');
             $id = $request->input('id');

                // send seo report email
        $seo_audit_details = SeoResult::select('url','title','meta','h1','h1_tags','h2','h2_tags','h3','h3_tags','word_count','http','cache','page_https','passed_score','warning_score','error_score','notice_score','image','mobile_friendly','ssl_certificate','score_description','domains_num','urls_num','loadtime','schema_types','fcp','lcp','cls','css_min','js_min','gzip_compression','created_at')->where('id', $id)->get()->toArray();
            $seo_audit_details = current($seo_audit_details);
            
          // print_r($seo_audit_details);
           
                Mail::send('emails/seo_report', compact('seo_audit_details', 'send_to', 'message'), function ($message) use ($send_to, $seo_audit_details, $url)
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


 public function delete_seo_report($id){
     try {
        $site_url = SeoResult::select('url')->where('id',$id)->where('user_id',auth()->user()->id)->pluck('url');
        SeoResult::where('id', $id)->delete();
        //delete all backlinks with matching site url
        Analysis::where('site_url', $site_url)->delete();

        return $id;

        } catch(Exception $e) {
           // return $e;
        }
       // return $id;
           }


    public function stripUrlPath($url){
        $urlParts = parse_url($url);
        $newUrl = $urlParts['scheme'] . "://" . $urlParts['host'] . "/";
        return $newUrl;
    }
 

    public function extractKeyWords($string) {
        $stopWords = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www','your');
 
        $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
        $string = trim($string); // trim the string
        $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
        $string = strtolower($string); // make it lowercase
        $string = preg_replace('/(?=[^ ]*[^A-Za-z \'-])([^ ]*)(?:\\s+|$)/', '', $string);
        //dd($string);
        preg_match_all('/\b.*?\b/i', $string, $matchWords);
        $matchWords = $matchWords[0];
   
        foreach ( $matchWords as $key=>$item ) {
            if ( $item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3 ) {
                unset($matchWords[$key]);
            }
        }   
        $wordCountArr = array();
        if ( is_array($matchWords) ) {
            foreach ( $matchWords as $key => $val ) {
                
                $val = strtolower($val);
                if ( isset($wordCountArr[$val]) ) {
                    $wordCountArr[$val]++;
                } else {
                    $wordCountArr[$val] = 1;
                }
            }
        }
        arsort($wordCountArr);
        $wordCountArr = array_slice($wordCountArr, 0, 10);
        return array($wordCountArr,$stopWords);
    }
    
    
}
