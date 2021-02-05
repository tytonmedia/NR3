@extends('layouts.master')
@section('title', 'Backlink Report')
@section('content')

<?php   
use \App\Http\Controllers\rankingsController; 

//$keyword_array = current($keyword_array);
$trend_array = array();
$features_array = array();
$positions = array();
$traffic_volume = array();
$traffic_vals = array();
$traffic_share = array();
$i = 0;
//dd($keyword_array);
       foreach ($keyword_array as $key => $value) {
                                 foreach ($value as $key2 => $val) {
                                   # code...
                                 
                                if($key2 == 'keyword' && $i == 0) {
                                     $top_keyword = $val;
                                  } else if($key2 == 'position'){
                                      $positions[] = $val;
                                  }else if($key2 == 'volume'){
                                      $traffic_volume[] = $val;
                                  }else if($key2 == 'traffic_cost'){
                                      $traffic_vals[] = $val;
                                  }else if($key2 == 'traffic_per'){
                                      $traffic_share[] = $val;
                                  }else if($key2 == 'trend'){
                                    $keyword_array[$key]['trend'] = json_decode($val);
                                    $trend_array[] = json_decode($val);
                                  }else if($key2 == 'features'){
                                    $keyword_array[$key]['features'] = json_decode($val);
                                    $features_array[] = json_decode($val);
                                  } 
                                 }
                                    $i++;
                                    }
              //  dd($features_array);
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
                 //   print_r($positions);
                     $count1 = $count2 = $count3 = $count4 = $count5 = 0;
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
                           if($positions[$i] >= 51 && $positions[$i] <= 100 ) {
                               $count4++;
                           }
                           if($positions[$i] >= 100) {
                               $count5++;
                           }
                       } // end for loop

                      // print_r($trend_array);

                       $position_array = array();
                       $position_array = ["1-10" => $count1, "11-20" => $count2, "21-50" => $count3, "51-100" => $count4, "100+" => $count5];
                       $position_array_numeric = array_values($position_array);
                       $traffic_share = array_sum($traffic_share);

                       if(count($positions) > 0){
                    $avg_position = array_sum($positions)/count($positions); 
                  } else{
                    $avg_position = 0;
                  }
      
                     $traffic_value  = array_sum($traffic_vals);
                     $volume_total = array_sum($traffic_volume);
                     $num_keywords = sizeof($keyword_array);

 ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.1/dist/circle-progress.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
                <script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>

                   <script src="//cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
              
                 <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/>
    

<script>
     $(document).ready(function($) {
 $('.table').DataTable({
          dom: 'Bfrtip',
          columnDefs: [
           
            { "width": "18%", "targets": 9 }],

            buttons: [{ extend: 'copyHtml5', className: 'btn btn-copy' },
            { extend: 'excelHtml5', className: 'btn btn-excel' },
            { extend: 'csvHtml5', className: 'btn btn-csv' }],
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 15
        });

$(".serp-icon").mouseenter(function(){
$(this).siblings(".serp-feature").show();
});
$(".serp-icon").mouseleave(function(){
$(this).siblings(".serp-feature").hide();
});

$(".competitor-info").mouseenter(function(){
$(this).children(".competitor-details").show();
});
$(".competitor-info").mouseleave(function(){
$(this).children(".competitor-details").hide();
 });
  });
    </script>
    <div class="col-md-10 overview analysis-container">
    <div class="inner">
          <div class="row report-header">
        <div class="col-md-4">
        <span class="logo">
            @if($white_label != '0')
            <img style="" src="/{{ $white_label }}" alt="logo">
            @else
                <img style="" src="{{asset('images/ninja reports gray.png')}}" alt="logo">
            @endif
            
        </span>
        </div>
        <div class="col-md-8 text-right" style="display:none;padding-right:0">
          <a class="btn btn-sm btn-success" href="#">DOWNLOAD</a>
          <a class="btn btn-sm btn-disabled" href="#" disabled="disabled">RE-CRAWL</a>
          <a class="btn btn-sm btn-warning" href="#">EMAIL</a>
      </div>
    </div>
        <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding-left:0">
            <h5 id="url"><STRONG>Keyword Report:</STRONG> {{$ranking_details['site_url']}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding-right:0">
            <h5 class="updated_at">Last Crawled: {{ date('F j, Y, g:i a', time($ranking_details['updated_at'])) }}</h5>
        </div>
    </div>

    <section id="rankings" class="rankings-page">
       <div class="row five-cols top">
            <div class="col-md-2">
                Total Keywords <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This is the total number of organic keywords your URL is ranked for in search engines."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ $num_keywords }}</h2>
            </div>
            <div class="col-md-2">
                    Average Position <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The average position is the average position each of your keywords are in search engines."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ number_format($avg_position, 2) }}</h2>
            </div>
            <div class="col-md-2">
                         Traffic Value <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Traffic is the moentary value of the current traffic coming to your website."><i class="fa fa-info-circle" ></i></a>
                        <h2>${{ $traffic_value }}</h2>
            </div>
            <div class="col-md-2">
                Traffic Potential <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Traffic potential refers to the amount of volume you could potentially get from your ranked keywords."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ number_format($volume_total) }}</h2>
            </div>
              <div class="col-md-2">
                Traffic Share <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Traffic potential refers to the amount of volume you could potentially get from your ranked keywords."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ number_format($traffic_share) }}%</h2>
            </div>
               <div class="col-md-2">
                SERP Features <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Traffic potential refers to the amount of volume you could potentially get from your ranked keywords."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ number_format(count($serp_array)) }}</h2>
            </div>   
         </div>
         <div class="row five-cols">
            <div class="col-md-2">
                   1-10 Positions <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ $position_array_numeric[0] }}<span class="outof">/{{ $num_keywords }}</span></h2>
            </div>
             <div class="col-md-2">
                   11-20 Positions <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ $position_array_numeric[1] }}<span class="outof">/{{ $num_keywords }}</span></h2>
            </div>
             <div class="col-md-2">
                   21-50 Positions <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ $position_array_numeric[3] }}<span class="outof">/{{ $num_keywords }}</span></h2>
            </div>
        <div class="col-md-2">
                   51-100 Positions <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ $position_array_numeric[4] }}<span class="outof">/{{ $num_keywords }}</span></h2>
            </div>
              <div class="col-md-4">
                Top Keyword <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h3>{{ $top_keyword ?? 'n/a' }}</h3>
            </div>
          </div>

<div class="row sub-cols">
    <div class="col-md-4">
    <h5>Keyword Trends (Last 12 Mo.)</h5>
        <canvas id="trendChart" style=" width:100%;height:200px;"></canvas>
    <script>
        var trend_array = <?php echo json_encode($t_array) ;?>;
          var trend_graph = trend_array.toString();
        var months = ['1','2','3','4','5','6','7','8','9','10','11','12'];

    var backlinksData = {
        labels: months,
        datasets: [{
            label: 'Avg. Trends',
            borderColor: "#0e6eea",
            fill: false,
            data: trend_array
        }]
    };

        var ctx = document.getElementById("trendChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: backlinksData,

             labels: months,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(5, 50, 92)',
                    }
                },
                responsive: true,
                title: {
                    display: false,
                    text: 'Referring URLs by Month'
                }
            }
        });

    </script>

  </div>
<div class="col-md-2" style="display:none;">
     <h5>Position Distribution <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This shows you how many keywords your URL is ranked for in each of the ranges below."><i class="fa fa-info-circle" ></i></a></h5>
    <ul class="pos-dist">
    @foreach($position_array as $key => $value)
    <?php
        if($key == '1-10'){
            $iconcolor = '#2fdb0d'; 
        }elseif($key == '11-20') {
            $iconcolor = '#BDE282';
        }elseif($key == '21-50'){
            $iconcolor = '#EBED99';
        }elseif($key == '51-100'){
            $iconcolor = '#ff6600';
        }elseif($key == '100+'){
            $iconcolor = '#ff0000';
        }
    ?>
           <li><i style="color:<?php echo $iconcolor;?>" class="fa fa-circle" aria-hidden="true"></i> {{$key}}: {{$value}}</li> 
              
    @endforeach
</ul>
</div>
  <div class="col-md-3">
    <h5>Competitors</h5>
<ul class="competitor-list">
      @foreach($competitor_array as $key => $value)
     
             @foreach($value as $key2 => $val)
                @if($key2 == 'domain')
                <li class="competitor-info"><i class="circle-num">{{$key+1}}</i> {{ $val }} <i class="fa fa-caret-down" aria-hidden="true"></i>
                @elseif($key2 == 'common_keywords')
                <div class="competitor-details competitor-details-{{$key}}" style="display:none;">
                    <ul><li><strong>Common Keywords:</strong> {{ number_format($val) }}</li>
                @elseif($key2 == 'organic_keywords')
                <li><strong>Organic Keywords:</strong> {{number_format($val) }}</li>
                @elseif($key2 == 'organic_traffic')
                <li><strong>Organic Traffic:</strong> {{number_format($val) }}</li>
                @elseif($key2 == 'cost')
                <li><strong>Organic Value:</strong> ${{number_format($val)}}</li>
                @elseif($key2 == 'adwords_keywords')
                <li><strong>Adwords Keywords:</strong> {{ number_format($val) }}</li></ul></div>
              </li>        
                @endif
              
                @endforeach
           
      @endforeach
 </ul>
  </div>
    <div class="col-md-5">
         <h5>Serp Features</h5>
         <div class="row">
            <div class="col-md-6" style="padding-left:0">
                  <ul class="serp-features-list">
            @php
            $labels = array();
            $serpdatas = array();
            @endphp
            @foreach($serp_array as $key => $value)
            <li><i style="margin-right:5px;" class="{{ rankingsController::get_serp_feature_icon($key) }}"></i> {{ rankingsController::get_serp_feature($key) }}: {{$value}}</li>

            @php
            $labels[] =  rankingsController::get_serp_feature($key);
            $serpdatas[] = $value;
            @endphp
         @endforeach
     </ul>
            </div>
            <div class="col-md-6">
              <canvas id="serpChart" style=" width:100%;height:150px;"></canvas>
            </div>
         </div>
               

    
    </div>
    <script>

    var serpData = {
        labels: <?php echo json_encode($labels);?>,
        datasets: [{
          label: 'SERP Features',
          data: <?php echo json_encode($serpdatas);?>,
          backgroundColor: [
            '#0E6EEA',
            '#2776dc',
            '#3e7ccc',
            '#4374b4',
            '#1058b5',
            '#2c5994',
            '#24497a',
            '#1b3b64',
            '#458dea',
            '#6ba4ee',
          ],
          hoverOffset: 4
        }]
    };

        var ctx = document.getElementById("serpChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'doughnut',
            data: serpData,
            options: {
                legend: {
                    labels: {
                         boxWidth: 5
                     },
            display: false,
            position: 'right',
        },
                elements: {
                },
                responsive: true,
            }
        });

    </script>

</div>
<div class="row">
<div class="col-md-4">
</div>
</div>
       <div class="row" style="margin-top:25px;">
            <div class="col-md-12">
                <h3>Organic Keywords</h3>
                            <table class="table rankings-table">

                                <thead class="thead-light">
                                <tr>
                                 <th>Keyword</th>
                                 <th>KD</th>
                                 <th>Position</th>
                                 <th>Volume</th>
                                 <th>CPC</th>
                                <th>Comp.</th>
                                <th>Traffic %</th>
                                 <th>Value</th>
                                 <th>Results</th>
                                  <th>SERP Features</th>
                                 <th>Trend</th>
                                
                               
                                    </tr>
                            </thead>
                            @php
                           // dd($keyword_array);
                            @endphp
                            <tbody>
                            @foreach($keyword_array as $key => $value)
                            <tr>
                                    @foreach($value as $key2 => $val)
                                    @if($key2 == 'keyword')
                                    <td><a href="https://www.google.com/search?q={{$val}}" target="_blank">{{ $val }}  <i class="fa fa-external-link"></i></a></td>
                                        @elseif($key2 == 'position' || $key2 == 'kd' || $key2 == 'volume' || $key2 == 'results')
                                                    <td> {{ number_format($val) }}  </td>
                                        @elseif($key2 == 'traffic_per')
                                        <td> {{ number_format($val, 2) }}  </td>
                                        @elseif($key2 == 'cpc' || $key2 == 'traffic_cost')
                                        <td> ${{ number_format($val, 2) }}  </td>
                                        @elseif($key2 == 'trend')
                                                        <td>
                                            <canvas id="keywordchart-{{$key}}" style=" width:150px;height:40px;"></canvas>
                                        </td>
                                        @elseif($key2 == 'features')
                                        <td>
                                            <ul class="serp_features">
                                           
                                        @foreach($val as $key3 => $v)
                                        <li>
                                            <i class="serp-icon {{ rankingsController::get_serp_feature_icon($v) }}"></i> 
                                        <div class="serp-feature" style="display:none">
                                            <h6>{{ rankingsController::get_serp_feature($v) }}</h6>
                                            {{ rankingsController::get_serp_feature_desc($v) }}
                                        </div>
                                        </li> 
                                            @endforeach
                                         
                                        </ul></td>
                                        @else 
                                        <td>{{$val}}</td>
                                        @endif
                   


                                    @endforeach
                            </tr>
                            @endforeach
                              </tbody>
                    
                            </table>
            </div>
       </div>
    </section>

      <script>
        <?php foreach($trend_array as $key => $value){ ?>
                                                 var ctx = document.getElementById("keywordchart-{{ $key }}").getContext("2d");
                                                  window.myBar = new Chart(ctx, {
                                             type: 'line',
                                             data: {
                                        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,11, 12],
                                             datasets: [
                                                 {
                                             backgroundColor: "#cdddf1",
                                             borderColor: "#0E6EEA",
                                             pointBackgroundColor: "#0E6EEA",
                                      data: <?php echo json_encode($value);?>
                                                    }
                                                     ]
                                            },
                                             options: {
                                                layout: {
                                                padding: {
                                                    left: 5,
                                                    right: 5,
                                                    top: 5,
                                                    bottom: 5
                                                }
                                            },
                                                scaleShowLabels : false,
                                                scales:{
                                            xAxes: [{
                                                        display: false //this will remove all the x-axis grid lines
                                            }],
                                                    yAxes: [{
                                                        display: false //this will remove all the x-axis grid lines
                                                    }]
                                                     },
                                                legend: {
                                                display: false
                                                    },
                                                   elements: {
                                                    point:{
                        radius: 0
                    },
                                                rectangle: {
                                                 borderWidth: 1,
                                                 borderColor: '#333333',
                                             }
                                                 },
                                            responsive: false,
                                         title: {
                                              display: false,
                                            }
                                     }
                                 });
                                                  <?php } ?>
                                     </script>

</div>
</div>
  <div class="modal" id="emailReport" tabindex="-1" role="dialog" aria-labelledby="emailReport" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
           <form id='seo_email_form'>
            <input type="hidden" id="report_url" name="report_url" value="{{ $ranking_details['site_url'] }}">
            <input type="hidden" id="report_id" name="report_id" value="{{ $ranking_details['id'] }}">
          <!-- Modal Header -->
          <div class="modal-header">
               <h4>Send SEO Report</h4>
            <button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body" style="padding:20px;">
         
            <p>Send this SEO report to an email.</p>

                    
                         <div class="row" style="margin-bottom:15px;">
    <div class="col">
      <input type="text" class="form-control" placeholder="Email Address" id="send_to">
    </div>
    <div class="col">
      <input type="text" class="form-control" placeholder="Message" id="message">
    </div>
  </div>

                    
          </div>

          <!-- Modal footer -->
          <div class="modal-footer" style="margin:auto;">
          <a class="btn-warning btn-md" href="{{route('email_ranking_report')}}" id='send_email_report' style='padding:7px;text-decoration:none;'>SEND REPORT</a>
          </div>
 </form>
        </div>
      </div>
    </div>
@endsection