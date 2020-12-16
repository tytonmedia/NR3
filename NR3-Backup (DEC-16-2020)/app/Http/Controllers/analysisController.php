<?php
namespace App\Http\Controllers;

use Exception;
use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use App\Analysis;
use App\User;
use App\Payment;
use App\Audit;
class analysisController extends Controller
{

    public function get_seo_result(Request $request)
    {
        ini_set("allow_url_fopen", 1);
        $url = $request->input('url');
        $time = date('F d Y, h:i:s A');
        
        try{
            
            $Payment = Payment::withCount('analysis')->where('user_id',auth()->user()->id)->where('status',1)->first();
            
        }catch(Exception $e){}

        if(empty($Payment)){
            $analysis = Analysis::where('user_id',auth()->user()->id)->whereDay('created_at', '=', date('d'))->latest()->first();
            if(empty($analysis)){
                return  $this->get_seo($url,$Payment,$time);
            }else{
                return 'upgrade';
            }
        }
        else if($Payment->status == 0){
            return 'notsuccessful';
        }else if ($Payment->plan_id== 1 && $Payment->no_allowed_analysis <= $Payment->analysis_count ){
            return 'exceeded';
        }
        else
        {
           return  $this->get_seo($url,$Payment,$time);
        }
    }
    public function get_audit_result(Request $request)
    {
        
        $url = $request->input('url');
        $time = date('F d Y, h:i:s A');

        try{
            $Payment = Payment::withCount('audit')->where('user_id',auth()->user()->id)->where('status',1)->first();
        }catch(Exception $e){}
      
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
        }
        else
        {
            $client = new Client();
            $crawler = $client->request('GET', $url);

            $create_analysis = new Audit;
            $create_analysis->user_id =auth()->user()->id;
            $create_analysis->site_url = $url;
            $create_analysis->payment_id = $Payment->id;
            $create_analysis->save();

            //get internal links
            try{
                    foreach ($crawler->filter('a') as $a) {
                        $a_links[] = $a->getAttribute('href');
                    }

                    //extract Domain
                    $domain = parse_url($url, PHP_URL_HOST);
                    $domain_url = str_replace('www.', '', $domain);
                    //dd($domain_url);
                    foreach ($a_links as $lnk) {
                        if (strpos($lnk, $domain_url) !== false) {
                            $internal_link[] = $lnk;
                        } else {
                            $external_link[] = $lnk;
                        }
                    }
                    $pages_link = array_unique(array_filter($internal_link));
            
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
                }catch(Exception $e){}
                $internal_page = array_unique($pages);
               
                
            try {
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

                    foreach ($h1 as $data) {
                        if (strlen($data) > 60) {
                            $h1_greater[] = $val;
                        } elseif (strlen($data) < 5) {
                            $h1_short[] = $val;
                        }
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
                    $graph_data[] = array_push($b, $graph_type, $graph_title, $graph_description, $graph_image, $graph_name, $graph_url);

                    $title = $crawler->filter('title')->html();
                    if (!empty($title)) {
                        $page_with_title[] = $val;
                    } else {
                        $page_miss_title[] = $val;
                    
                    }
                    if (strlen($title) < 45) {
                        $short_title[] = $val;
                    } elseif (strlen($title) > 60) {
                        $long_title[] = $val;
                    }
                    
                    $total_title[] = $title;

                    if(strlen($val)>115){
                        $url_length[] = $val;
                    }

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
                    if ($page_text_ratio < 25) {
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
                            if (strlen($meta) < 70) {
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
                        $status200[] = $matches[0];
                        $link_200[] = $val;
                    }elseif($matches[0] == 301) {
                        $status301[] = $matches[0];
                        $link_301[] = $val;
                    } elseif ($matches[0] == 302) {
                        $status302[] = $matches[0];
                        $link_302[] = $val;
                    } elseif ($matches[0] == 404) {
                        $status404[] = $matches[0];
                        $link_404[] = $val;
                    
                    } elseif ($matches[0] == 500) {
                        $status500[] = $matches[0];
                        $link_500[] = $val;
                        
                    }
                    $pages[] = $val;

                }
            } catch (Exception $e) {}

            //H1 Tags Length
            try {
                $page_h1_greater = array_unique($h1_greater);
                $page_h1_less = array_unique($h1_short);
            } catch (Exception $e) {}
            
            //meta duplicate
            try {
                $arr = array_combine($linkss,$total_meta);
                $counts = array_count_values($arr);
                $duplicate_meta_description  = array_filter($arr, function ($value) use ($counts) {
                    return $counts[$value] > 1;
                });

            } catch(Exception $e) {}

            //Robot.txt
            try {
                $get_robot = file_get_contents($url . "/robots.txt");
                $robots = explode(" ", (str_replace("\r\n", " ", $get_robot)));
                $robot_txt = array_chunk($robots, 1);
                foreach ($robot_txt as $dat) {
                    $rob = $dat;
                    foreach ($rob as $data) {
                        $robot[] = $data;
                    }
                }
            } catch (Exception $e) {}
            
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
            }catch(Exception $e){}
            
            //duplicate title
            try{
                $array = array_combine($page_with_title,$total_title);
                $counts = array_count_values($array);
                $duplicate_title  = array_filter($array, function ($value) use ($counts) {
                    return $counts[$value] > 1;
                });
                
            }catch(Exception $e){}
           
            //Notices Score Count
            try{
            
                if(!empty($page_h1_greater)){
                    $h1_count_greater = count($page_h1_greater);
                    }else{
                    $h1_count_greater = 0;
                }

                if(!empty($page_h1_less)){
                    $h1_count_less = count($page_h1_less);
                }else{
                    $h1_count_less = 0;
                }

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

                $notices = $h1_count_greater+$h1_count_less+$h1_count_more+$twitter_count+
                            $graph_count+
                            $url_length_count+$robot_count+$page_incomplete_graph_count;
            }catch(Exception $e){
                if(empty($notices)){
                    $notices=0;
                }
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
                $data = count(array_unique($page_with_errors))/count($pages);
                $health_score = (1-($data))*100;
                $pages = count($pages);
                $passed_pages = $pages - count($page_with_errors);
            }catch(Exception $e){}
            return view("dashboard/audit_result",
            compact('url', 'time', 'page_h1_greater', 'page_h1_less', 'long_title', 'short_title','url_length',
                'graph_data', 'links_more_h1', 'less_code_ratio', 'short_meta_description',
                'long_meta_description', 'robot', 'less_page_words', 'links_empty_h1', 'duplicate_h1',
                'page_miss_meta', 'duplicate_meta_description', 'page_incomplete_card', 'page_incomplete_graph', 'status301',
                'status302', 'status404', 'status500', 'page_miss_title', 'duplicate_title','twitter',
                'link_302','link_301','link_404','link_500','page_without_canonical','notices','warning','errors','passed_pages'
                ,'health_score','pages'
            ));
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
        preg_match('/http:\/\//',$Link,$ExternalLinkCheck);
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

        $create_analysis = new Analysis;
        $create_analysis->user_id = auth()->user()->id;
        $create_analysis->site_url = $url;
        $create_analysis->payment_id = $Payment->id ?? Null;
        $create_analysis->save();
        
       
        //Mobile Friendly test
        try{
            $urls = "https://searchconsole.googleapis.com/v1/urlTestingTools/mobileFriendlyTest:run?key=AIzaSyBoWi8UVeIzrhXxxDhPm4G9OQT3lJuy1fc";

            $curl = curl_init($urls);
            curl_setopt($curl, CURLOPT_URL, $urls);
            curl_setopt($curl, CURLOPT_POST, true);;
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
        
        }catch(Exception $e){}
        
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
            $domain = parse_url($url, PHP_URL_HOST);
            $domain_url = str_replace('www.', '', $domain);

            foreach ($a_links as $lnk) {
                if (strpos($lnk, $domain_url) !== false || strpos($lnk, "/") == '0') {
                    $internal_link[] = $lnk;
                } else {
                    $external_link[] = $lnk;
                }
            }
        } catch (Exception $e) {
        }
        //Link To Social Media Page
        try {
            $social_link = array('facebook', 'linkedin', 'twitter', 'youtube', 'instagram');
        
           
            foreach ($external_link as $ext) {
                
                foreach ($social_link as $social) {
                    if (strpos($ext, $social)) {
                        $link_to_social[] = $ext;
                        $social_media_link = 'Links to social media profiles found';
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
                    $link_https = '';
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
                $sitemap = "found";
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
                    $status301[] = $matches[0];
                } elseif ($matches[0] == 302) {
                    $status302[] = $matches[0];
                } elseif ($matches[0] == 404) {
                    $status404[] = $matches[0];
                }
            }
        } catch (Exception $e) {}

        //Browser Cache Checking
        try {
            $headers = get_headers($url);
            foreach ($headers as $header) {
                if (stripos($header, "Cache-Control") !== false) {
                    $cache = $header;
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

        //page word count
        $str = file_get_contents($url);

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
            foreach ($crawler->filter('link') as $a) {
                if(strpos($a->getAttribute('href'), 'favicon') !== false){
                $favicon = $a->getAttribute('href');
                }elseif(strpos($a->getAttribute('rel'), 'shortcut') !== false){
                    $favicon = $a->getAttribute('href');
                }
                else{
                    $favicon = $crawler->filterXpath('//link[@rel="icon"]')->attr('href');
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
                $img_without_alt[] = $img->getAttribute('src');
            }

            $img_alt = count($all_img_src) - count($img_without_alt);
            if (empty($img_without_alt)) {
                $img_miss_alt = '';
            } else {
                $img_miss_alt = count($img_without_alt);
            }

        } catch (Exception $e) {
        }
       
        
        //Page Score Passed
        try {
            if ($title_length > 30 && $title_length < 61) {
                $val1_pass = 3.7;
            } else {
                $val1_pass = 0;
            }
            if ($meta_length > 50 && $meta_length < 160) {
                $val2_pass = 3.7;
            } else {
                $val2_pass = 0;
            }
            if (!empty($canonical)) {
                $val3_pass = 3.7;
            } else {
                $val3_pass = 0;
            }
            if (!empty($schema_tags)) {
                $val4_pass = 3.7;
            } else {
                $val4_pass = 0;
            }
            if (empty($img_miss_alt)) {
                $val5_pass = 3.7;
            } else {
                $val5_pass = 0;
            }
            if ($url_seo_friendly == "Seo Friendly") {
                $val6_pass = 3.7;
            } else {
                $val6_pass = 0;
            }
            if (!empty($iframe)) {
                $val7_pass = 0;
            } else {
                $val7_pass = 3.7;
            }
            if ($h1_tags > 0) {
                $val8_pass = 3.7;
            } else {
                $val8_pass = 0;
            }
            if ($h2_tags > 0) {
                $val9_pass = 3.7;
            } else {
                $val9_pass = 0;
            }
            if ($h3_tags > 0) {
                $val10_pass = 3.7;
            } else {
                $val10_pass = 0;
            }
            if (!empty($word_count)) {
                $val11_pass = 3.7;
            } else {
                $val11_pass = 0;
            }
            if ($page_words > 300) {
                $val12_pass = 3.7;
            } else {
                $val12_pass = 0;
            }
            if (!empty($cache)) {
                $val13_pass = 3.7;
            } else {
                $val13_pass = 0;
            }
            if (!empty($status404)) {
                $val14_pass = 0;
            } else {
                $val14_pass = 3.7;
            }
            if ($page_https == "Page using HTTPS") {
                $val15_pass = 3.7;
            } else {
                $val15_pass = 0;
            }
            if (!empty($a_https) && !empty($link_https) && !empty($script_https)) {
                $val16_pass = 0;
            } else {
                $val16_pass = 3.7;
            }
            if (!empty($social_media_link)) {
                $val17_pass = 3.7;
            } else {
                $val17_pass = 0;
            }
            if (!empty($social_schema)) {
                $val18_pass = 3.7;
            } else {
                $val18_pass = 0;
            }
            if (!empty($sitemap)) {
                $val20_pass = 3.7;
            } else {
                $val20_pass = 0;
            }
            if ($h1_tags > 0) {
                $val21_pass = 3.7;
            } else {
                $val21_pass = 0;
            }
            if ($h2_tags > 0) {
                $val22_pass = 3.7;
            } else {
                $val22_pass = 0;
            }
            if ($h3_tags > 0) {
                $val23_pass = 3.7;
            } else {
                $val23_pass = 0;
            }
            if (!empty($img_data)) {
                $val24_pass = 3.7;
            } else {
                $val24_pass = 0;
            }
            if (!empty($favicon)) {
                $val25_pass = 3;
            } else {
                $val25_pass = 0;
            }
            if($mobile_friendly === 'MOBILE_FRIENDLY'){
                $val26_pass = 3;
            }elseif($mobile_friendly === 'NOT_MOBILE_FRIENDLY'){
                $val26_pass = 0;
            }else{
                $val26_pass = 0;
            }
            if(!empty($internal_link)){ $val27_pass = 3;}else{ $val27_pass = 0;}
            if($page_text_ratio > 10){$val28_pass = 3;}else{$val28_pass = 0;}   
            $text_html_ration = 3.7;
            $http_rquest = 3.7;
            $passed_score = $val1_pass + $val2_pass + $val3_pass + $val4_pass + $val5_pass + $val6_pass + $val7_pass + $val8_pass + $val9_pass
                + $val10_pass + $val11_pass + $val12_pass + $val13_pass + $val14_pass + $val15_pass + $val16_pass + $val17_pass
                + $val18_pass + $val20_pass + $val21_pass + $val22_pass + $val23_pass + $val24_pass +
                $text_html_ration + $http_rquest +$val25_pass+$val26_pass+$val27_pass+$val28_pass;
        } catch (Exception $e) {}
    
        //Page Score Warning
        try {
            if (empty($canonical)) {$val3_warning = 3.7;} else {$val3_warning = 0;}
            if (!empty($img_miss_alt)) {$val5_warning = 3.7;} else {$val5_warning = 0;}
            if ($url_seo_friendly == "Seo Friendly") {$val6_warning = 0;} else {$val6_warning = 3.7;}
            if (empty($word_count)) {$val8_warning = 3.7;} else {$val8_warning = 0;}
            if ($page_words) {$val9_warning = 0;} else {$val9_warning = 3.7;}
            if (!empty($cache)) {
                $val10_warning = 0;
            } else {
                $val10_warning = 3.7;
            }
            if (!empty($robot)) {
                $val18_warning = 0;
            } else {
                $val18_warning = 3.7;
            }
            if (!empty($sitemap)) {$val19_warning = 0;} else {$val19_warning = 3.7;}

            if (!empty($favicon)) {$val20_pass = 0;} else {$val20_pass = 3;}
            if ($page_words > 300) {$val12_pass = 0;} else {$val12_pass = 3.7;}

            if(!empty($internal_link)){ $val13_pass = 0;}else{ $val13_pass = 3.7;}

            if($page_text_ratio > 10){$val14_pass = 0;}else{$val14_pass = 3.7;}

            $warning_score = $val3_warning + $val5_warning + $val6_warning
                + $val8_warning + $val9_warning + $val10_warning + $val18_warning + $val19_warning+$val20_pass+$val13_pass+$val14_pass;
        } catch (Exception $e) {}

        //Page Score Error
        try {
            if (!empty($status404)) {
                $val1_error = 3.7;
            } else {
                $val1_error = 0;
            }
            if ($page_https == "Page using HTTPS") {
                $val2_error = 0;
            } else {
                $val2_error = 3.7;
            }
            if (!empty($a_https) && !empty($link_https)  && !empty($script_https)) {
                $val3_error = 3.7;
            } else {
                $val3_error = 0;
            }
            if ($h1_tags > 0) {
                $val4_error = 0;
            } else {
                $val4_error = 3.7;
            }
            if ($h2_tags > 0) {
                $val5_error = 0;
            } else {
                $val5_error = 3.7;
            }
            if ($h3_tags > 0) {
                $val6_error = 0;
            } else {
                $val6_error = 3.7;
            }
            if ($title_length < 30 || $title_length > 60) {$val7_error = 3.7;} else {$val7_error = 0;}
            if ($meta_length < 50 || $meta_length > 160) {$val8_error = 3.7;} else {$val8_error = 0;}
            if (empty($iframe)) {$val9_error = 0;} else {$val9_error = 3.7;}
            if($mobile_friendly === 'MOBILE_FRIENDLY'){$val10_error = 0;}elseif($mobile_friendly === 'NOT_MOBILE_FRIENDLY'){$val10_error = 3;}
            $error_score = $val1_error + $val2_error + $val3_error + $val4_error + $val5_error
                            + $val6_error + $val7_error +$val8_error +$val9_error +$val10_error;
        } catch (Exception $e) {}
       
        //page Notices
        try{
            if (!empty($img_data)) {
                $val1_notice = 0;
            } else {
                $val1_notice = 3.7;
            }
            if (!empty($schema_tags)) {
                $val2_notice = 0;
            } else {
                $val2_notice = 3.7;
            }
            if (!empty($robot)) {
                $val3_notice = 3.7;
            } else {
                $val3_notice = 0;
            }
            if (!empty($social_media_link)) {
                $val4_notice = 0;
            } else {
                $val4_notice = 3.7;
            }
            if (!empty($social_schema)) {
                $val5_notice = 0;
            } else {
                $val5_notice = 3.7;
            }
            if(empty($all_img_src)){
                $val6_notice = 3.7;
            }else{
                $val6_notice = 0;
            }
            $notice_score = $val1_notice+$val2_notice+$val3_notice+$val4_notice+$val5_notice+$val6_notice;

            if($passed_score > 80){
            $score_description = "Your page SEO is good!";
            } elseif ($passed_score > 60) {
            $score_description = "Your page SEO needs work!";
            } else {
              $score_description = "Your page SEO is weak!";  
            }
        }catch(Exception $e){}
        //dd($notice_score);
        $view = view("dashboard/seo_result", compact(
            'url', 'title', 'title_length', 'meta',
            'meta_length', 'img_alt', 'img_miss_alt',
            'iframe', 'all_img_src', 'canonical',
            'time', 'img_without_alt', 'url_seo_friendly',
            'h1', 'h1_tags', 'h2', 'h2_tags', 'h3', 'h3_tags',
            'word_count', 'numWords', 'density_message',
            'keyword_title', 'page_words', 'page_size', 'page_text_ratio',
            'page_words_size', 'http', 'cache', 'page_https',
            'status404', 'internal_link', 'a_https', 'link_https', 'script_https',
            'social_media_link', 'robot', 'sitemap', 'schema',
            'social_schema', 'passed_score', 'warning_score', 'error_score',
            'img_data','favicon','mobile_friendly','ssl_certificate','notice_score',
            'image','score_description','word'

        ));
        return $view;
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
