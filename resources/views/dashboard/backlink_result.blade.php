
<?php   use \App\Http\Controllers\backlinkController; ?>
<script>
 $('.table').DataTable({
            "autoWidth": false,
            "lengthChange": false,
            "pageLength": 15
        });
    </script>
 <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding:0">
            <h5 id="url"><STRONG>Backlink Report:</STRONG> {{$url}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding:0">
            <h5>{{ $time }}</h5>
        </div>
    </div>
    
    <section id="analysis" class="analysis-page">
       <div class="row four-cols" style="margin-bottom:15px;">
            <div class="col-md-3">
                Total Backlinks <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Total backlinks is the total number of domains that point to your website."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ number_format($urls_num) }}</h2>
            </div>
            <div class="col-md-3">
                    Referring Domains <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Referring domains is the total number of domains that point to your website."><i class="fa fa-info-circle" ></i></a>
                    <h2>{{ number_format($domains_num) }}</h2>
            </div>
            <div class="col-md-3">
                        Link Toxicity <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Link toxicity score is calculated by the number of toxic domains pointing to your website."><i class="fa fa-info-circle" ></i></a>
                        <h2 style="color:{{ backlinkController::get_toxicity_color($linktoxicity) }}">{{ $linktoxicity }}%</h2>
            </div>
            <div class="col-md-3">
                         Link Power <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The link power is calculated by the number of backlinks and backlink quality."><i class="fa fa-info-circle" ></i></a>
                 <h2 style="color:{{ backlinkController::get_linkpower_color($linkpower) }}">{{ $linkpower }}%</h2>
                      
                </div>


       </div>

<div class="row">
<div class="col-md-3">
    <h5>Referring Backlinks History</h5>
    <canvas id="backlinksChart" style=" width:100%;height:200px;"></canvas>
</div>
<div class="col-md-3">
    <h5>Referring Domains History</h5>
    <canvas id="domainsChart" style=" width:100%;height:200px;"></canvas>
</div>
<div class="col-md-3">
    <h5>Link Profile</h5>
     <canvas id="nofollowChart" style=" width:100%;height:200px;"></canvas>
    </div>
    <div class="col-md-2">
        <h5>Referring TLDs</h5>
        <div>
                @foreach($tld_array as $key => $value)
                <div class="d-flex bd-highlight">
                <div class="p-2 flex-shrink-1 bd-highlight" style="font-size:13px;width:40px">{{$key}}</div>
                <div class="p-2 w-100 bd-highlight"> <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $value;?>%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($value,0);?>%</div>
                        </div></div>
                </div>

                   
                @endforeach
        </div>
    </div>
</div>
    


     
</div>
<script>
     <?php     

            foreach($historical_array as $key => $val){
                    //only display data, not header
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
       </div>
       <br/>
       <div class="row" style="margin-top:50px;">
            <div class="col-md-12">
                <h3>Backlinks</h3>
                            <table class="table backlinks-table">
                                <thead class="thead-light">
                                <tr>
                                 <th>Source URL</th>
                                 <th>Target URL</th>
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
                             @foreach($backlink_array as $key => $value)
                                <tr>
                                    @foreach($value as $key2 => $val)
                                    @if($key2 == 0)
                                        <td><a href="{{ $val }}" target="_blank">{{$val}} <i class="fa fa-external-link"></i></a></td>
                                    @elseif($key2 == 6 || $key2 == 7)
                                        <td>{{ date('M j \'y',strtotime($val)) }}</td>
                                    @else
                                        <td>{{$val ?? 'N/A'}}</td>
                                    @endif
                                    
  
                                    @endforeach
                                </tr>
         
                            @endforeach
                      @endif
                            </table>
            </div>
       </div>
    </section>
