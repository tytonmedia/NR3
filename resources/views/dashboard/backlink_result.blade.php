@extends('layouts.master')
@section('title', 'Backlink Report')
@section('content')

<?php   
use \App\Http\Controllers\backlinkController; 

$backlink_details = current($backlink_details);
$historical_array = json_decode($backlink_details['historical']);
$linktoxicity = 0;
            $anchor_array = array();
            $tld_array = array();
            $tlds = array();
          
            foreach ($backlink_array as $key => $value) {
              foreach ($value as $key2 => $val) {
                # code...
                if($key2 == 'external_num') {
                  if($val > 250){
                      $linktoxicity += 1; 
                    }
                }
                if($key2 == 'anchor') {

                      $anchor_array[] = $val; 
                }
                if($key2 == 'source_url') {

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
                      $tldcalc = number_format(($value/$tld_count)*100,2);
                  $tld_array[$key] = ceil($tldcalc);
                }
              }
              $tld_array = array_slice($tld_array, 0, 5, true);
                    arsort($tld_array);

              $anchor_array = array_count_values($anchor_array);
              //build toxicity score
              if(!empty($backlink_array) && $linktoxicity > 0){
                  $linktoxicity = number_format(count($backlink_array)/$linktoxicity);
              } else{
                  $linktoxicity = 0;
              }

  if($backlink_details['domains_num'] != 'empty' && $backlink_details['backlinks_num'] != 'empty'){
        //if not empty
            if($backlink_details['domains_num'] == 0){
            $linkpower = '0';
        }elseif($backlink_details['domains_num'] > 0 && $backlink_details['domains_num'] < 10) {
            $linkpower = '10';
        }elseif ($backlink_details['domains_num'] > 10 && $backlink_details['domains_num'] < 25) {
            $linkpower = '20';
        }elseif ($backlink_details['domains_num'] > 25 && $backlink_details['domains_num'] < 50) {
            $linkpower = '30';
        }elseif ($backlink_details['domains_num'] > 50 && $backlink_details['domains_num'] < 100) {
            $linkpower = '40';
        }elseif ($backlink_details['domains_num'] > 100 && $backlink_details['domains_num'] < 150) {
            $linkpower = '50';
        }elseif ($backlink_details['domains_num'] > 150 && $backlink_details['domains_num'] < 200) {
            $linkpower = '60';
        }elseif ($backlink_details['domains_num'] > 200 && $backlink_details['domains_num'] < 250) {
            $linkpower = '80';
        }elseif ($backlink_details['domains_num'] > 250 && $backlink_details['domains_num'] < 300) {
            $linkpower = '70';
        }elseif ($backlink_details['domains_num'] > 300 && $backlink_details['domains_num'] < 350) {
            $linkpower = '80';
        }elseif ($backlink_details['domains_num'] > 350 && $backlink_details['domains_num'] < 500) {
            $linkpower = '90';
        }elseif ($backlink_details['domains_num'] > 500) {
            $linkpower = '100';
        }else{
            $linkpower = 'N/A';
        }     
          } 
$nofollow_array = array();
          foreach ($backlink_array as $key => $value) {
                foreach ($value as $key2 => $val) {
                  if($key2 == 'nofollow'){
                    $nofollow_array[] = $val;
                  }
                }
          }
  $nofollow_array = array_count_values($nofollow_array);
  
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
?>
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
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/>
    

<script>
     $(document).ready(function($) {
 $('.table').DataTable({
          dom: 'Bfrtip',
            buttons: [{ extend: 'copyHtml5', className: 'btn btn-copy' },
            { extend: 'excelHtml5', className: 'btn btn-excel' },
            { extend: 'csvHtml5', className: 'btn btn-csv' }],
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 15
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
        <div class="col-md-8 text-right">
          <a class="btn btn-sm btn-success" href="{{ url('download_backlink_report', $backlink_details['id'])}}"><i class="fa fa-download" aria-hidden="true"></i> DOWNLOAD PDF</a>
          <a class="btn btn-sm btn-disabled" href="#" disabled="disabled"><i class="fa fa-refresh" aria-hidden="true"></i> RE-CRAWL</a>
          <a class="btn btn-sm btn-warning" href="#" id="emailreportlink" data-id="{{$backlink_details['id']}}"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> EMAIL</a>
      </div>
    </div>
 <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding:0">
            <h5 id="url"><STRONG>Backlink Report:</STRONG> {{$backlink_details['site_url']}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding:0">
            <h5 class="updated_at">Last Updated: {{ date('F j, Y, g:i a', time($backlink_details['updated_at'])) }}</h5>
        </div>
    </div>
    
    <section id="analysis" class="analysis-page">
       <div class="row four-cols" style="margin-bottom:15px;">
            <div class="col-md-3">
                Total Backlinks <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Total backlinks is the total number of domains that point to your URL."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ number_format($backlink_details['backlinks_num']) }}</h2>
            </div>
            <div class="col-md-3">
                    Referring Domains <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Referring domains is the total number of domains that point to your URL."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ number_format($backlink_details['domains_num']) }}</h2>
            </div>
            <div class="col-md-3">
              <div class="row">
                  <div class="col-md-7">
                     Link Toxicity <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Link toxicity score is calculated by the number of toxic domains pointing to your URL. The more toxic backlinks pointing to your URL will result in a higher toxicity score."><i class="fa fa-info-circle" ></i></a>
                        <h2 style="color:{{ backlinkController::get_toxicity_color($linktoxicity) }}">{{ $linktoxicity }}%</h2>
                  </div>
                  <div class="col-md-4">
                    <canvas id="doughnut-chart" width="75" height="75"></canvas>
                  </div>
                </div>
              </div>
                   <script>
                    var opp = <?php echo 100 - $linktoxicity;?>;
                          new Chart(document.getElementById("doughnut-chart"), {
                      type: 'doughnut',
                      data: {
                        labels: ["Toxicity"],
                        datasets: [
                          {
                            label: "",
                            backgroundColor: ["<?php echo backlinkController::get_toxicity_color($linktoxicity)?>", "#eee"],
                            data: [<?php echo $linktoxicity;?>,opp]
                          }
                        ]
                      },
                      options: {
                        tooltips: {
                             enabled: false
                        },
                         legend: {
                              display: false
                           },
                        responsive: false,
                        title: {
                          display: false,
                          text: 'Link Toxicity'
                        }
                      }
                  });
                  </script>
            <div class="col-md-3">
              <div class="row">
                  <div class="col-md-7">
                         Link Power <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The link power is calculated by the number of backlinks and backlink quality. The higher the link power the better your website will rank."><i class="fa fa-info-circle" ></i></a>
                 <h2 style="color:{{ backlinkController::get_linkpower_color($linkpower) }}">{{ $linkpower }}%</h2>
                      
                 </div>
                  <div class="col-md-4">
                    <canvas id="linkpower-doughnut-chart" width="75" height="75"></canvas>
                  </div>
                </div>
              </div>
                   
       </div>

<div class="row">
<div class="col-md-3">
    <h5>Referring Backlinks History</h5>
    <canvas id="backlinksChart" style=" width:100%;height:175px;"></canvas>
</div>
<div class="col-md-3">
    <h5>Referring Domains History</h5>
    <canvas id="domainsChart" style=" width:100%;height:175px;"></canvas>
</div>
<div class="col-md-3">
    <h5>Link Profile</h5>
     <canvas id="nofollowChart" style=" width:100%;height:175px;"></canvas>
    </div>
    <div class="col-md-2">
        <h5>Referring TLDs</h5>
        <div>
                @foreach($tld_array as $key => $value)
                <div class="d-flex bd-highlight">
                <div class="p-2 flex-shrink-1 bd-highlight" style="font-size:13px;width:40px">{{$key}}</div>
                <div class="p-2 w-100 bd-highlight"> <div class="progress">
                        <div class="progress-bar" role="progressbar" style="min-width:15%;width: <?php echo $value;?>%" aria-valuenow="0" aria-valuemin="25" aria-valuemax="100"><?php echo number_format($value,0);?>%</div>
                        </div></div>
                </div>

                   
                @endforeach
        </div>
    </div>
</div>
    
<script>
     <?php 
     $months_array = array();
     $backlinks_num_array= array();
     $domains_num_array= array();
        if(!empty($historical_array)){
            foreach($historical_array as $key => $val){
                    //only display data, not header
              // date,backlinks_num,backlinks_new_num,backlinks_lost_num,domains_num,domains_new_num,domains_lost_num
                if($key != 6) {
                    foreach($val as $key => $bod) {
                        if($key == 0) {
                                $months_array[] = date("M",$bod);
                        }
                        if($key == 1) {
                                $backlinks_num_array[] = $bod;
                        }
                        if($key == 4) {
                                $domains_num_array[] = $bod;
                        }
                    }

                }
             }
           }

?>
    var months = <?php echo '["' . implode('", "', $months_array) . '"]' ?>; 
    var domains = <?php echo '["' . implode('", "', $domains_num_array) . '"]' ?>;
    var backlinks = <?php echo '["' . implode('", "', $backlinks_num_array) . '"]' ?>;


var ctx = document.getElementById("nofollowChart");
var myChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['NOFOLLOW', 'FOLLOW'],
    datasets: [{
      label: 'Follow/No Follow',
      data: <?php echo '["' . implode('", "', $nofollow_array) . '"]' ?>,
      height:200,
      backgroundColor: [
      '#ff6600',
        '#0E6EEA',
        

      ],
      borderColor: [
        '#cccccc',
        '#cccccc',

      ],
      borderWidth: 1
    }]
  },
  options: {
    cutoutPercentage: 60,
    responsive: true,
    legend: {
      position: 'right',
      align: 'center',
      labels: {
               boxWidth: 10
            }
   }

  }
});


    var backlinksData = {
        labels: months,
        datasets: [{
            label: 'Backlinks',
            borderColor: "#0e6eea",
            fill: false,
            data: backlinks
        }]
    };

       var domainsData = {
        labels: months,
        datasets: [{
            label: 'Domains',
            borderColor: "#ff6600",
            fill: false,
            data: domains
        }]
    };


        var ctx = document.getElementById("backlinksChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: backlinksData,
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

        var ctx = document.getElementById("domainsChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: domainsData,
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
                    text: 'Referring Domains by Month'
                }
            }
        });

</script>

       <br/>
       <div class="row">
            <div class="col-md-12">
                <h3>Backlinks</h3>
                            <table class="table backlinks-table">
                                <thead class="thead-light">
                                <tr>
                                 <th>Source URL</th>
                            
                                 <th>Anchor</th>
                                 <th>AScore</th>
                                <th>Ext. Links</th>
                                <th>Int. Links</th>
                                 <th>Last Seen</th>
                                 <th>First Seen</th>
                               <th>Nofollow</th>
                                    </tr>
                            </thead>
                        @if(!empty($backlink_array))
                        <tbody>
                             @foreach($backlink_array as $key => $value)
                                <tr>
                                    @foreach($value as $key2 => $val)
                                    @if($key2 == 'source_url')
                                        <td><a href="{{ $val }}" target="_blank">{{$val}} <i class="fa fa-external-link"></i></a></td>
                                    @elseif($key2 == 'target_url')

                                    @elseif($key2 == 'anchor')
                                        <td>
                                                @if($val == '')
                                                    <span style="color:#ccc">no anchor</span>
                                                @else
                                                {{$val}}
                                                @endif
                                        </td>
                                        @elseif($key2 =='last_seen' || $key2 == 'first_seen')
                                        <td>{{ date('M j \'y',strtotime($val)) }}</td>
                                    @else
                                        <td>{{$val ?? 'N/A'}}</td>
                                    @endif
                                    
  
                                    @endforeach
                                </tr>
         
                            @endforeach
                      @endif
                      </tbody>
                            </table>
            </div>
       </div>
    </section>
</div>
</div>
<script>
                    var opp = <?php echo 100 - $linkpower;?>;
                          new Chart(document.getElementById("linkpower-doughnut-chart"), {
                      type: 'doughnut',
                      data: {
                        labels: ["Link Power"],
                        datasets: [
                          {
                            label: "Population (millions)",
                            backgroundColor: ["<?php echo backlinkController::get_linkpower_color($linkpower)?>", "#eee"],
                            data: [<?php echo $linkpower;?>,opp]
                          }
                        ]
                      },
                      options: {
                        tooltips: {
                             enabled: false
                        },
                         legend: {
                              display: false
                           },
                        responsive: false,
                        title: {
                          display: false,
                          text: 'Link Power'
                        }
                      }
                  });
                  </script>

                    <div class="modal" id="emailReport" tabindex="-1" role="dialog" aria-labelledby="emailReport" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
           <form id='seo_email_form'>
            <input type="hidden" id="report_url" name="report_url" value="{{ $backlink_details['site_url'] }}">
            <input type="hidden" id="report_id" name="report_id" value="{{ $backlink_details['id'] }}">
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
          <a class="btn-warning btn-md" href="{{route('email_backlink_report')}}" id='send_email_report' style='padding:7px;text-decoration:none;'>SEND REPORT</a>
          </div>
 </form>
        </div>
      </div>
    </div>
@endsection