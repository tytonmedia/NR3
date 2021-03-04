@extends('layouts.master')
@section('title', 'Traffic Report')
@section('content')
<?php  
use App\Http\Controllers\trafficController;
              $traffic_source = array();
              $traffic_percent = array();
              $traffic_name = array();
              $traffic_name[] = array('Direct','Organic','Search Ads','Referring','Social');
              $traffic_name = current($traffic_name);
              $traffic_source[] = array($traffic_details['direct_value'],$traffic_details['organic_value'],$traffic_details['search_value'],$traffic_details['referring_value'],$traffic_details['social_value']);
              $traffic_source = current($traffic_source);
              $traffic_percent[] = array($traffic_details['direct_percent'],$traffic_details['organic_percent'],$traffic_details['search_percent'],$traffic_details['referring_percent'],$traffic_details['social_percent'] );
              $traffic_percent = current($traffic_percent);
              
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha1/html2canvas.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.1/dist/circle-progress.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/>
<script>
     $(document).ready(function($) {
 $('.table').DataTable({
          dom: 'Bfrtip',
          buttons: [],
            "autoWidth": true,
            "lengthChange": false,
            "paging": false,
            "searching": false,
             "bFilter": false,
        "bInfo": false
        });

                 $("#download_report").click(function(e){
                    e.preventDefault();
                    html2canvas($(".traffic-container .inner"), {
                    onrendered: function(canvas) {        
                    var imgData = canvas.toDataURL('image/png');
                        var imgWidth = 210; 
                    var pageHeight = 295;  
                   var imgHeight = canvas.height * imgWidth / canvas.width;
                   var heightLeft = imgHeight;
                   var doc = new jsPDF('p', 'mm');
                       var position = 0;

                  doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                 heightLeft -= pageHeight;

                 while (heightLeft >= 0) {
                   position = heightLeft - imgHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                 }
                 doc.save( 'traffic_report.pdf'); 

                        }

                      });
                  });

  });
    </script>
    <div class="col-md-10 overview traffic-container">
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
        <div class="col-md-8 text-right" style="padding-right:0;">
          <a class="btn btn-sm btn-info" target="_blank" id="download_report" href="#"><i class="fa fa-download" aria-hidden="true"></i> DOWNLOAD PDF</a>
      </div>
    </div>
  <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding-left:0">
          
            <h5 id="url"><STRONG>Traffic Report:</STRONG> {{$traffic_details['domain']}}</h5>
            <p>{{$traffic_details['description']}}</p>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding-right:0">
            <h6 style="font-size:16px;color:#999;font-weight:normal">Report Date: {{ date('F j, Y, g:i a', strtotime($traffic_details['created_at'])) }}</h6>
        </div>
    </div>
    
    <section id="traffic" class="traffic-page">
      <div class="row">
          <div class="col-md-12" style="padding:0;margin-bottom:15px;">
              <h2>Traffic Overview</h2>
          </div>  
      </div>
        <div class="row four-cols" style="margin-bottom:15px;">
            <div class="col-md-3">
                Monthly Visits <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title=""><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ trafficController::thousandsCurrencyFormat($traffic_details['visits']) }}
                      @if($traffic_details['estimated'] != null)
                      @php 

                      $traffic_history_value = array();
                      $estimated_traffic = json_decode($traffic_details['estimated']);
                        foreach($estimated_traffic as $key => $value){
                          $traffic_history_value[] = $value[1];
                        }
            
                        @endphp
                      @if($traffic_history_value[4] > $traffic_history_value[5])
                      <i class="fa fa-caret-down" style="color:red;font-size:18px;" aria-hidden="true"><span style="font-size:18px;">{{number_format(($traffic_history_value[5] - $traffic_history_value[4]) / $traffic_history_value[5] *100,0 )}}%</span></i>
                       @else
                       <i class="fa fa-caret-up" style="color:green;font-size:18px;" aria-hidden="true"> <span style="font-size:18px;">{{number_format(($traffic_history_value[5] - $traffic_history_value[4]) / $traffic_history_value[5] *100,0 )}}%</span></i>
                            @endif
                    @endif
                    </h2>
            </div>
            <div class="col-md-3">
                    Avg. Time On Site <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The average time, in minutes, that the average user stays on the website."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{$traffic_details['avg_time_site']}}</h2>
            </div>
            <div class="col-md-3">
                     Avg. Page Views <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Average page views is the number of pages visitors browse on average."><i class="fa fa-info-circle" ></i></a>
                        <h2>{{number_format($traffic_details['avg_page_views'],2)}}</h2>
              </div>

            <div class="col-md-3">
                         Bounce Rate <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The percentage of visitors to your website who navigate away from the site after viewing only one page."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{$traffic_details['bounce_rate']}}%</h2>
                      
                </div>
              
                   
       </div>
        <div class="row four-cols" style="margin-bottom:15px;">
            <div class="col-md-3">
                Global Rank <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Your total global rank amongst all other websites on the internet."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{number_format($traffic_details['global_rank'])}}</h2>
            </div>
            <div class="col-md-3">
                    Category Rank <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Your website ranking amongst other websites in the same category."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{$traffic_details['cat_rank']}}</h2>
                    @php
                  $category = substr($traffic_details['cat'], strpos($traffic_details['cat'], "/") + 1);  
                    @endphp
                    <span style="color:#999;font-size:12px;">
                    {{$category}}
                  </span>
            </div>
            <div class="col-md-3">
                     Country Rank <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Your website ranking amongst all other websites in your country."><i class="fa fa-info-circle" ></i></a>
                        <h2>{{number_format($traffic_details['country_rank'])}}</h2>

              </div>

 <div class="col-md-3">
                     Avg. Rank <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Your average rank between global, category and country rank."><i class="fa fa-info-circle" ></i></a>
                        <h2>{{number_format(($traffic_details['country_rank'] + $traffic_details['country_rank'] + $traffic_details['global_rank']) / 3 )}}</h2>
              </div>

                   
       </div>

       <div class="row">
          <div class="col-md-6">
              Traffic History

              @if($traffic_details['estimated'] == null) 
              <div class="text-center">
                <h3>No Data</h3>
                <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
              </div>
              @else
             <div style="width:100%;height:200px;margin-top:15px;position:relative">
            <canvas id="estimatedChart" style="width:100%"></canvas>
         </div>
              @php
              $months = array();
              $traffics = array();
               foreach(json_decode($traffic_details['estimated']) as $key => $value)
               {
                      $months[] = $value[0];
                      $traffics[] = $value[1];
               }
                                 
              @endphp
<script>

  var estimated = <?php echo '["' . implode('", "', $traffics) . '"]' ?>;
  var months = <?php echo '["' . implode('", "', $months) . '"]' ?>;
         var domainsData = {
        labels: months,
        datasets: [{
            label: 'Visits',
            borderColor: "rgba(0, 123, 255, 1)",
            backgroundColor: 'rgba(0, 123, 255, .4)',
            fill: true,
            fillOpacity: .3,
            data: estimated
        }]
    };
          var ctx = document.getElementById("estimatedChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: domainsData,
            options: {
               tooltips: {
      callbacks: {
          label: function(tooltipItem, data) {
              return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
      }
  },
               maintainAspectRatio: false,
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgba(0, 123, 255, 1)',
                    }
                },
                responsive: true,
                title: {
                    display: false,
                    text: 'Traffic History '
                }
            }
        });
      </script>
            @endif

          </div>
              <div class="col-md-6">
                  Traffic Sources
                  @php $tmp = array_filter($traffic_source); @endphp
                  @if(empty($tmp))
                    <div class="text-center">
                      <h3>No Data</h3>
                      <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                    </div>
                  @else
                  <div style="width:100%;height:200px;margin-top:15px;position:relative">
                  <canvas id="bar-chart" style="width:100%"></canvas>
                </div>
                   <script>
                     var traffic_source = <?php echo '["' . implode('", "', $traffic_source) . '"]' ?>;
                     var traffic_name = <?php echo '["' . implode('", "', $traffic_name) . '"]' ?>;
            
                      new Chart(document.getElementById("bar-chart"), {
                      type: 'bar',
                      data: {
                        labels: traffic_name,
                        datasets: [
                          {
                            
                            backgroundColor: ["rgba(0, 123, 255, .5)", "rgba(37, 193, 45, .5)", "rgba(219, 33, 33, .5)", "rgba(216, 114, 30, .5)", "rgba(51, 51, 51, .5)"],
                            borderWidth: 1,
                            borderColor: ["rgba(0, 123, 255, 1)", "rgba(37, 193, 45, 1)","rgba(219, 33, 33, 1)","rgba(216, 114, 30, 1)","rgba(51, 51, 51, 1)"],
                            data: traffic_source
                          }
                        ]
                      },
                      options: {

                        maintainAspectRatio: false,
                        tooltips: {
                             enabled: true,
                             callbacks: {
          label: function(tooltipItem, data) {
              return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
      }
                        },
                         legend: {
                display: false,
                labels: {boxWidth: 10}
                    },
                        responsive: true,
                        title: {
                          display: false,
                          text: 'Traffic By Source'
                        }
                      }
                  });
                  </script>
              @endif

           
          </div>
       </div>
       <br/>
       <div class="row" style="margin-bottom:15px">
        <div class="col-md-12" >
          <h2>Keyword Traffic</h2>
        </div>
       </div>
       <div class="row">
<div class="col-md-6">
            <div style="clear:both;padding-bottom:10px;">
           <h5>Top Organic Keywords to {{$traffic_details['domain']}}</h5>
           <p><strong style="color:#206ee0;font-size:21px">{{$traffic_details['organic_percent']}}%</strong> of {{$traffic_details['domain']}}'s traffic comes from organic search.</p>
         </div>
               @php 
                if($traffic_details['keywords'] !== null){
                $tmp = array_filter(json_decode($traffic_details['keywords']));
              } else{
                 $tmp = '';
            }
               @endphp
                  @if(empty($tmp))
                <div class="text-center">
                    <h3>No Data</h3>
                    <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                  </div>

                  @else
           <table class="table table-striped" style="margin-top:15px;">
            <thead>
            <tr>
                <th>Keyword</th>
                <th>Visits</th>
                <th>%</th>
            </tr>
          </thead>
          <tbody>
                   @foreach(json_decode($traffic_details['keywords']) as $key => $value)
                   <tr>
                <td><a href="https://www.google.com/search?q={{$value[0]}}" target="_blank">{{$value[0]}} <i class="fa fa-external-link" aria-hidden="true"></i>
</a></td><td>{{number_format($value[1])}}</td><td>{{$value[2]}}%</td>
              </tr>
              @endforeach
            </tbody>
            </table>
            @endif
          </div>

<div class="col-md-6">
            <div style="clear:both;padding-bottom:10px;">
           <h5>Top Advertising Keywords to {{$traffic_details['domain']}}</h5>
           <p><strong style="color:#206ee0;font-size:21px">{{$traffic_details['search_percent']}}%</strong> of {{$traffic_details['domain']}}'s traffic comes from search advertising.</p>
         </div>
               @php 
                if($traffic_details['ad_keywords'] !== null){
                $tmp = array_filter(json_decode($traffic_details['ad_keywords']));
              } else{
                 $tmp = '';
            }
               @endphp
                  @if(empty($tmp))
                <div class="text-center">
                    <h3>No Data</h3>
                    <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                  </div>

                  @else
           <table class="table table-striped" style="margin-top:15px;">
            <thead>
            <tr>
                <th>Keyword</th>
                <th>Visits</th>
                <th>%</th>
            </tr>
          </thead>
          <tbody>
                   @foreach(json_decode($traffic_details['ad_keywords']) as $key => $value)
                   <tr>
                <td>{{$value[0]}}</td><td>{{number_format($value[1])}}</td><td>{{$value[2]}}%</td>
              </tr>
              @endforeach
            </tbody>
            </table>
            @endif
          </div>
        </div>
        <br/>
               <div class="row">
        <div class="col-md-12">
          <h2>Social Traffic</h2>
        </div>
       </div>
       <div class="row" style="margin-top:15px;">
          <div class="col-md-8">
              <h5>Top Social Traffic to {{$traffic_details['domain']}}</h5>
           <p><strong style="color:#206ee0;font-size:21px">{{$traffic_details['social_percent']}}%</strong> of {{$traffic_details['domain']}}'s traffic comes from social media.</p>
      
                @php 
                if($traffic_details['top_socials'] !== null) 
                { 
                  $tmp = array_filter(json_decode($traffic_details['top_socials'])); 
                } else { 
                  $tmp = ''; 
                } 
              @endphp
                  @if(empty($tmp))
                  <div class="text-center">
                    <h3>No Data</h3>
                    <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                  </div>
                  @else
                    <div style="width:100%;height:200px;margin-top:15px;position:relative">
                  <canvas id="social-chart" width="100%"></canvas>
                </div>
                   @php
              $social_names = array();
              $social_traffic = array();
                  foreach(json_decode($traffic_details['top_socials']) as $key => $value) {
              $social_names[] =  $value[0];
              $social_traffic[] = $value[1];

              }
              @endphp
                        <script>
                  
                     var social_name = <?php echo '["' . implode('", "', $social_names) . '"]' ?>;
                     var social_value = <?php echo '["' . implode('", "', $social_traffic) . '"]' ?>;
                          new Chart(document.getElementById("social-chart"), {
                      type: 'horizontalBar',
                      data: {
                        labels: social_name,
                        datasets: [
                          {
                            backgroundColor: ["rgba(0, 123, 255, .5)", "rgba(37, 193, 45, .5)", "rgba(219, 33, 33, .5)", "rgba(216, 114, 30, .5)", "rgba(51, 51, 51, .5)"],
                            borderWidth: 1,
                            borderColor: ["rgba(0, 123, 255, 1)", "rgba(37, 193, 45, 1)","rgba(219, 33, 33, 1)","rgba(216, 114, 30, 1)","rgba(51, 51, 51, 1)"],
                            data: social_value
                          }
                        ]
                      },
                      options: {
                           maintainAspectRatio: false,
                        tooltips: {
                             enabled: true,
          },
                         legend: {
                display: false,
               labels: {
              boxWidth: 10
          }
                    },
                        responsive: true,
                        title: {
                          display: false,
                          text: 'Traffic By Social'
                        }
                      }
                  });
                  </script> 
               @endif
          </div>
          <div class="col-md-4" style="margin-top:50px;">
            @if($traffic_details['top_socials'] != null)
            <ul class="top-socials">
 @foreach(json_decode($traffic_details['top_socials']) as $key => $value)
                <li>
                @switch($value[0])
                  @case('Youtube')
                      <i class="fa fa-youtube-play" aria-hidden="true" style="color:#c4302b;font-size:19px"></i>
                      @break
                  @case('Facebook')
                     <i class="fa fa-facebook-square" aria-hidden="true" style="color:#4267B2;font-size:19px"></i>
                      @break
                      @case('Stack Overflow')
                  <i class="fa fa-stack-overflow" aria-hidden="true" style="color:#4267B2;font-size:19px"></i>
                      @break
                      @case('WhatsApp Webapp')
                  <i class="fa fa-whatsapp" aria-hidden="true" style="color:#25D366;font-size:19px"></i>
                      @break
                      @case('Reddit')
                      <i class="fa fa-reddit-alien" aria-hidden="true" style="color:#FF4500;font-size:19px"></i>
                      @break
                      @case('Pinterest')
                      <i class="fa fa-pinterest-square" aria-hidden="true" style="color:#c8232c;font-size:19px"></i>
                      @break
                      @case('Instagram')
                      <i class="fa fa-instagram" aria-hidden="true" style="color:#8a3ab9;font-size:19px"></i>
                      @break
                      @case('Linkedin')
                      <i class="fa fa-linkedin-square" aria-hidden="true" style="color:#2867B2;font-size:19px"></i>
                      @break
                       @case('Twitter')
                      <i class="fa fa-twitter-square" aria-hidden="true" style="color:#1DA1F2;font-size:19px"></i>
                      @break
              @endswitch

                <strong>{{$value[0]}}</strong>: {{trafficController::thousandsCurrencyFormat($value[1])}} visits <strong style="color:#777;font-size:16px">({{number_format($value[2])}}%)</strong></li>
              @endforeach
            </ul>
            @else
          
            @endif
          </div>
       </div>
        <br/>
              <div class="row" style="margin-bottom:5px">
        <div class="col-md-12" >


         
          <h2 >Traffic By Country</h2>

        </div>
       </div>
        <div class="row">
      @php
         $country_names = array();
              $country_traffic = array();
                  foreach(json_decode($traffic_details['countries']) as $key => $value) {
              $country_names[] =  $value[0];
              $country_traffic[] = $value[1];
            }
$tmp = array_filter($country_traffic); @endphp
                  @if(empty($tmp))
                  <div class="text-center col-md-12">
                      <h3>No Data</h3>
                     <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                  </div>
                  @else
                  <div class="row">
                    @php

  $country_array = json_decode($traffic_details['countries']);
  $top_percent = array();

  $sum = array_sum(array_map(function ($a) { return $a[1]; }, $country_array));

  foreach ($country_array as $info) {
       $top_percent[] = round(($info[1]/$sum)*100);
  }      
@endphp

                      <div class="col-md-12">
                         <h5 style="margin-top:15px">Top visitors by Country to {{$traffic_details['domain']}}</h5>
           <p><strong style="color:#206ee0;font-size:21px">{{ $top_percent[0] }}%</strong> of {{$traffic_details['domain']}}'s visitors are from {{ $country_array[0][0] }}.</p>
                      </div>
                  </div>
    <div class="col-md-3">
    
   <ul class="top-socials" style="padding-left:10px;">
@foreach(json_decode($traffic_details['countries']) as $key => $value)
  <li><strong>{{ $value[0] }}</strong>: {{ trafficController::thousandsCurrencyFormat($value[1]) }} visits</li>
 @endforeach
</ul>
  </div>
                      <div class="col-md-9">
    
           <div style="width:100%;height:200px;margin-top:15px;position:relative">
                  <canvas id="country-chart" width="100%"></canvas>
                </div>
                            <script>
  
                     var country_name = <?php echo '["' . implode('", "', $country_names) . '"]' ?>;
                     var country_traffic = <?php echo '["' . implode('", "', $country_traffic) . '"]' ?>;

                       new Chart(document.getElementById("country-chart"), {
                      type: 'horizontalBar',
                      data: {
                        labels: country_name,
                        datasets: [
                          {
                            backgroundColor: ["rgba(0, 123, 255, .5)", "rgba(37, 193, 45, .5)", "rgba(219, 33, 33, .5)", "rgba(216, 114, 30, .5)", "rgba(51, 51, 51, .5)", "rgba(0, 20, 255, .5)"],
                            borderWidth: 1,
                            borderColor: ["rgba(0, 123, 255, 1)", "rgba(37, 193, 45, 1)","rgba(219, 33, 33, 1)","rgba(216, 114, 30, 1)","rgba(51, 51, 51, 1)", "rgba(0, 20, 255, 1)"],
                            data: country_traffic
                          }
                        ]
                      },
                      options: {
                           maintainAspectRatio: false,
                        tooltips: {
                             enabled: true,
          },
                         legend: {
                display: false,
               labels: {
              boxWidth: 10
          }
                    },
                        responsive: true,
                        title: {
                          display: false,
                          text: 'Traffic By Social'
                        }
                      }
                  });
                  </script>
                         
</div>
@endif 
</div>
<div calss="row">
  <div class="col-md-12">
    <h2>Competitors</h2>
       @php 
            if($traffic_details['similar'] !== null){
                $tmp = array_filter(json_decode($traffic_details['similar']));
                $compet = $tmp[0][0];
                @endphp
                  <h5 style="margin-top:15px">Top competitors by Global Rank</h5>
           <p><strong style="color:#206ee0;font-size:21px">{{ $compet }}</strong> is {{$traffic_details['domain']}}'s biggest competitor.</p>
           @php
              } else{
                 $tmp = '';
                 $compet = '';
            }
            @endphp
    
    </div>
  </div>
<div calss="row">
             <div class="col-md-12">
         
                  @if(empty($tmp))
                <div class="text-center">
                    <h3>No Data</h3>
                    <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                  </div>
                  @else
              <ul class="comps">
                @php
                $i = 0;
                @endphp
              @foreach(json_decode($traffic_details['similar']) as $key => $value)
               @if($i == 2)
              </ul><ul class="comps">
                @php $i = 0; @endphp
                @endif
                <li><a href="https://{{$value[0]}}" target="_blank">{{$value[0]}} </a> <span>({{ $value[1] }} rank)</span></li>
                  @php $i++; @endphp
              @endforeach
            </ul>
                @endif
          </div>

   
       </div>
       <div class="row">
        <div class="col-md-12">
        <h2>Top Destinations</h2>
      </div>
      </div>
       <div class="row">
               <div class="col-md-12">
      
                @php if($traffic_details['destinations'] !== null) { $tmp = array_filter(json_decode($traffic_details['destinations'])); } else { $tmp = ''; } @endphp
                  @if(empty($tmp))
                  <div class="text-center">
                    <h3>No Data</h3>
                    <img src="{{asset('images/ninja-icon-gray.png')}}" alt="ninja"/>
                  </div>
                  @else
                     <ul class="comps">
                @php
                $i = 0;
                @endphp
              @foreach(json_decode($traffic_details['destinations']) as $key => $value)
               @if($i == 1)
              </ul><ul class="comps">
                @php $i = 0; @endphp
                @endif
                <li><a href="https://{{$value[0]}}" target="_blank">{{$value[0]}} </a></li>
                  @php $i++; @endphp
              @endforeach
            </ul>
               @endif
            </div>

       </div>
    </section>

   </div>
  </div>
  
@endsection