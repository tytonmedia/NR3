<?php  use \App\Http\Controllers\rankingsController; ?>
    <script type="text/javascript">
        $.noConflict();
         $('.table').DataTable({
            "autoWidth": false,
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
$(this).siblings(".competitor-details").show();
});
$(".competitor-info").mouseleave(function(){
$(this).siblings(".competitor-details").hide();
});

        </script>
        <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding-left:0">
            <h5 id="url"><STRONG>Keyword Report:</STRONG> {{$url}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding-right:0">
            <h5>Last Crawled: {{ $time }}</h5>
        </div>
    </div>

    <section id="rankings" class="rankings-page">
       <div class="row cols" style="margin-bottom:15px;">
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
                   Traffic Share <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ $traffic_share }}%</h2>
            </div>
              <div class="col-md-2">
                Top Keyword <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>{{ $top_keyword }}</h2>
                  
            </div>

       </div>
</div>
       </div>

<div class="row sub-cols">
    <div class="col-md-4">
    <h5>Keyword Trends (Last 12 Mo.)</h5>
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
    <canvas id="trendChart" style=" width:100%;height:200px;"></canvas>
  </div>
<div class="col-md-2">
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
  <div class="col-md-2">
    <h5>Competitors</h5>

      @foreach($competitor_array as $key => $value)
     <ul class="competitor-list">
             @foreach($value as $key2 => $val)
                @if($key2 == 'domain')
                <li class="competitor-info"><i class="circle-num">{{$key+1}}</i> {{ $val }} <i class="fa fa-caret-down" aria-hidden="true"></i></li>
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
                @endif
                @endforeach
            </ul>
      @endforeach

  </div>
    <div class="col-md-2">
         <h5>Serp Features</h5>
         <ul style="list-style-type:none;margin-left:5px;padding-left:5px;">
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
<div class="col-md-2">
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
            display: true,
            position: 'top',
        },
                elements: {
                },
                responsive: true,
            }
        });

    </script>
    <canvas id="serpChart" style=" width:100%;height:200px;"></canvas>
</div>
</div>
<div class="row">
<div class="col-md-4">
</div>
</div>
       <div class="row" style="margin-top:25px;">
            <div class="col-md-12">
                <h3>Organic Keywords</h3>
                            <table class="table backlinks-table">

                                <thead class="thead-light">
                                <tr>
                                 <th>Keyword</th>
                                 <th>KD</th>
                                 <th>Position</th>
                                 <th>Search Volume</th>
                                 <th>CPC</th>
                                <th>Comp.</th>
                                <th>Traffic %</th>
                                 <th>Traffic Cost</th>
                                 <th>Results</th>
                                 <th>SERP Features</th>
                                 <th>Keyword Trend</th>
                               
                                    </tr>
                            </thead>
                            @foreach($keyword_array as $key => $value)
                            <tr>
                                    @foreach($value as $key2 => $val)
                                    @if($key2 == 0)
                                    <td><a href="https://www.google.com/search?q={{$val}}" target="_blank">{{ $val }}  <i class="fa fa-external-link"></i></a></td>
                                        @elseif($key2 == 1 || $key2 == 2 || $key2 == 3 || $key2 == 8)
                                                    <td> {{ number_format($val) }}  </td>
                                        @elseif( $key2 == 5 || $key2 == 6)
                                        <td> {{ number_format($val, 2) }}  </td>
                                        @elseif($key2 == 4 || $key2 == 7)
                                        <td> ${{ number_format($val, 2) }}  </td>
                                        @elseif($key2 == 10)
                                                        <td>
                                            <canvas id="keywordchart-{{$key}}" style=" width:250px;height:50px;"></canvas>
                                             <script>
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
                                      data: <?php echo json_encode($val);?>
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
                                     </script>
                                        </td>
                                        @elseif($key2 == 9)
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
                                        @endif
                   


                                    @endforeach
                            </tr>
                            @endforeach

                    
                            </table>
            </div>
       </div>
    </section>
