@extends('layouts.master')
@section('title', 'SEO Audit Report')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
            buttons: [ { extend: 'copyHtml5', className: 'btn btn-copy' },
            { extend: 'excelHtml5', className: 'btn btn-excel' },
            { extend: 'csvHtml5', className: 'btn btn-csv' }],
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 10
        });


               $("#emailreportlink").click(function(e){
                    e.preventDefault();
                    $("#emailReport").modal("show");
                    });

                  $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                    $("#send_email_report").click(function(e){
                    e.preventDefault();
                    var id = $('#report_id').val();
                    var url = encodeURIComponent($('#report_url').val());
                    var send_to = $('#send_to').val();
                    var data = {id:id,url:url,send_to:send_to};
                  $.ajax({
                              beforeSend: function(){
                                  $(this).text('Sending');
                                },
                                type:'POST',
                                url:'/email_audit_report',
                                data: data,
                                success:function(data){
                                $("#emailReport").modal("hide");
                                alert('Sent');
                                },
                                error: function (request, status, error) {
                                      alert(error);
                                }
                            });

                    });

  });
    </script>

       <div class="col-md-10 overview audit-container">
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
        <div class="col-md-8 text-right" style="padding-right:0;display:none;">
          <a class="btn btn-sm btn-success" href="{{ url('download_audit_report',$audit_details['id'])}}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> DOWNLOAD</a>
          <a class="btn btn-sm btn-disabled" href="#" disabled="disabled"><i class="fa fa-refresh" aria-hidden="true"></i> RE-CRAWL</a>
          <a class="btn btn-sm btn-warning" id="emailreportlink" data-id="{{$audit_details['id']}}" href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> EMAIL</a>
      </div>
    </div>

     <div class="row audit-text pt-3 pb-3">
        <div class="col-md-5">
            <h5><STRONG>SEO AUDIT REPORT: </STRONG>{{$audit_details['site_url']}}</h5>
        </div>
        <div class="col-md-2">

        </div>
        <div class="col-md-5 text-right">
            <h5>Last Crawled: {{$audit_details['created_at']}}</h5>
        </div>
    </div>

    <section id="overview">
        <div class="row four-cols">
            <div class="col-md-3">
                <h5>ON-PAGE SEO SCORE</h5>
             <div class="graph-container" style="margin-top:15px;">

                  <canvas id="seo-chart" width="200" height="200"></canvas>
                          <script>
                            Chart.pluginService.register({
  beforeDraw: function(chart) {
    var width = chart.chart.width,
        height = chart.chart.height,
        ctx = chart.chart.ctx;

    ctx.restore();
    var fontSize = (height / 100).toFixed(2);
    ctx.font = fontSize + "em sans-serif";
    ctx.textBaseline = "middle";

    var text = '<?php echo $audit_details['health_score'];?>%',
        textX = Math.round((width - ctx.measureText(text).width) / 2),
        textY = height / 2;

    ctx.fillText(text, textX, textY);
    ctx.save();
  }
});
                    var passed_score = <?php echo $audit_details['health_score'];?>;
                    var opp = <?php echo 100 - $audit_details['health_score'];?>;

                    if(passed_score > 0 && passed_score <= 50){
                        color="#ff0000";
                    }else if(passed_score > 50 && passed_score <= 70){
                         color="#ff6600";
                    }else if(passed_score > 70 && passed_score <= 90){
                         color="#0E6EEA";
                    }else {
                       color = "#008000";
                    }
                          new Chart(document.getElementById("seo-chart"), {
                      type: 'doughnut',
                      data: {
                        labels: ["SEO Score"],
                        datasets: [
                          {
                            label: "",
                            backgroundColor: [color, "#eee"],
                            data: [passed_score,opp]
                          }
                        ]
                      },
                      options: {
                         cutoutPercentage: 65,
                        tooltips: {
                             enabled: false
                        },
                         legend: {
                              display: false
                           },
                        responsive: false,
                        title: {
                          display: false,
                          text: 'SEO Score'
                        }
                      }
                  });
                  </script>

                    <h5 style="text-align:center">{{$audit_details['audit_description']}}</h5>
                </div>
             <hr/>
                <h5 style="margin-top:10%;"><i class="fa fa-check" aria-hidden="true"></i> {{$audit_details['passed_pages'] < 0 ? 0 : $audit_details['passed_pages']}} URLs Passed</h5>
                <h5><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ $audit_details['errors'] }} Errors</h5>
                <h5><i class="fa fa fa-exclamation-circle" aria-hidden="true"></i> {{ $audit_details['warning'] }} Warnings</h5>
                <h5><i class="fa fa-flag" aria-hidden="true"></i> {{ $audit_details['notices'] }} Notices</h5>
            </div>

            <div class="col-md-3 error-box">
                <h5 style="color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ERRORS</h5>
                <h5 class="number-error">{{ $audit_details['errors'] }}</h5>
                <p class="description">Errors are SEO issues that have the highest impact on your website's SEO performance.</p>
                <ul class="found-list">
                @if($audit_details['status_404'] > 0)
                <li> {{ count(json_decode($audit_details['status_404']))}}
                  @endif
                  {{$audit_details['status_404'] > 0 ? 'Broken links':''}}
                  @if($audit_details['status_404'] > 0) 
                </li> 
                @endif

                @if(!empty($audit_details['status_500'] ))
                <li>
                  {{ count(json_decode($audit_details['status_500']))}} 
                  @endif
                  {{!empty($audit_details['status_500']) ? '500 error':''}}
                  @if(!empty($audit_details['status_500']))
                </li> 
                @endif

                @if(!empty($audit_details['links_empty_h1'] )) <li> {{ count(json_decode($audit_details['links_empty_h1']))}} @endif    {{!empty($audit_details['links_empty_h1'])    ? 'H1 tags missing':''}}  @if(!empty($audit_details['links_empty_h1'] )) </li> @endif
                @if(!empty($audit_details['page_miss_meta'] )) <li> {{ count(json_decode($audit_details['page_miss_meta']))}} @endif     {{!empty($audit_details['page_miss_meta'])    ? 'Missing meta descriptions':''}} @if(!empty($audit_details['page_miss_meta'] )) </li> @endif
                @if(!empty($audit_details['page_miss_title'] ))  <li> {{ count(json_decode($audit_details['page_miss_title']))}}  @endif  {{!empty($audit_details['page_miss_title'])? 'Title tags missing':''}} @if(!empty($audit_details['page_miss_title'] ))  </li>  @endif
                @if(!empty($audit_details['duplicate_meta_description'] )) <li> {{ count(json_decode($audit_details['duplicate_meta_description']))}}  @endif    {{!empty($audit_details['duplicate_meta_description']) ? 'Duplicate meta description' : ''}} @if(!empty($audit_details['duplicate_meta_description'] )) </li>  @endif
                @if(!empty($audit_details['duplicate_title'] )) <li> {{ count(json_decode($audit_details['duplicate_title']))}}   @endif  {{!empty($audit_details['duplicate_title']) ? 'Duplicate title tags' : ''}} @if(!empty($audit_details['duplicate_title'] )) </li>   @endif
                @if(!empty($audit_details['short_title'] )) <li>  {{ count(json_decode($audit_details['short_title']))}} @endif    {{!empty($audit_details['short_title']) ? 'Title tags too short':''}} @if(!empty($short_title)) </li>   @endif
                @if(!empty($audit_details['long_title']))   <li> {{ count(json_decode($audit_details['long_title']))}} @endif  {{!empty($audit_details['long_title'])  ? 'Title tags too long' :''}} @if(!empty($audit_details['long_title']))  </li>  @endif 
                @if(!empty($audit_details['long_meta_description'])) <li> {{ count(json_decode($audit_details['long_meta_description']))}} @endif    {{!empty($audit_details['long_meta_description']) ? 'Long meta descriptions' : ''}} @if(!empty($audit_details['long_meta_description'])) </li> @endif
                @if(!empty($audit_details['short_meta_description'])) <li> {{ count(json_decode($audit_details['short_meta_description']))}} @endif    {{!empty($audit_details['short_meta_description']) ? 'Short meta descriptions' : ''}} @if(!empty($audit_details['short_meta_description'])) </li> @endif
            </ul>
                @if($audit_details['errors'] == 0)  <div class="clean"><i class="fa fa-check" aria-hidden="true"></i> Nice, no errors!</div> @endif
                <div class="link-div text-right view-more">
                   <a href="#errors">View errors</a>
                </div>
            </div>

            <div class="col-md-3 warning-box">
                <h5 style="color:#ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> WARNINGS</h5>
                <h5 class="number-error">{{ $audit_details['warning'] ?? 0}}</h5>
                      <p class="description">Warnings have less impact on your SEO performance but should not be overlooked.</p>
                      <ul class="found-list">
                @if(!empty($audit_details['less_page_words'] )) <li>  @endif {{!empty($audit_details['less_page_words'])   ? 'Low word count':''}} @if(!empty($audit_details['less_page_words'] )) </li>  @endif
                @if(!empty($audit_details['page_without_canonical'] )) <li>   @endif  {{!empty($audit_details['page_without_canonical']) ? 'Canonical tag missing':''}} @if(!empty($audit_details['page_without_canonical'] )) </li>   @endif
                @if(!empty($audit_details['duplicate_h1'] ))  <li>  @endif    {{!empty($audit_details['duplicate_h1']) ? 'Duplicate h1 tags':''}} @if(!empty($audit_details['duplicate_h1'] ))  </li>  @endif
                @if(!empty($audit_details['page_incomplete_card'] )) <li> @endif     {{!empty($audit_details['page_incomplete_card'])  ? 'Twitter card incomplete' :''}} @if(!empty($audit_details['page_incomplete_card'])) </li> @endif
                @if(!empty($audit_details['status_301'] ))   <li> @endif  {{!empty($audit_details['status_301'])     ? '301 redirects found':''}} @if(!empty($audit_details['status_301'] )) </li> @endif
                @if(!empty($audit_details['status_302'] )) <li> @endif {{!empty($audit_details['status_302'] ) ? '302 redirects found':''}} @if(!empty($audit_details['status_302'] )) </li> @endif
                @if(!empty($audit_details['less_code_ratio'] )) <li> @endif {{!empty($audit_details['less_code_ratio']) ? 'Text-to-HTML ratio < 10%': ''}} @if(!empty($audit_details['less_code_ratio'])) </li> @endif
               </ul>
                 @if($audit_details['warning'] == 0)  <div class="clean"><i class="fa fa-check" aria-hidden="true"></i> Nice, no warnings!</div> @endif
                <div class="link-div text-right view-more">
                  <a href="#warnings">View Warnings</a>
                </div>
            </div>
            <div class="col-md-3 notice-box">
                <h5 style="color:#0e6eea;"><i class="fa fa-flag" aria-hidden="true"></i> NOTICES</h5>
                <h5 class="number-error">{{ $audit_details['notices']}}</h5>
                  <p class="description">Notices are not critical to your SEO performance but should be corrected.</p>
                  <ul class="found-list">
                @if(!empty($audit_details['links_more_h1'])) 
                <li> 
                    @endif 
                 {{!empty($audit_details['links_more_h1']) ? 'Multiple h1 tags found':''}}
                  @if(!empty($audit_details['links_more_h1'])) 
                    </li> 
                 @endif 

                @if( !empty($audit_details['url_length']) )
                <li>
                @endif  
            {{ !empty($audit_details['url_length'])  ?  'Longs URL':'' }}
               @if( !empty($audit_details['url_length']) ) 
                    </li>  
                @endif 

                @if(empty($audit_details['twitter']))
                <li>
                @endif
                    {{!empty($audit_details['twitter']) ? '' :'Twitter card missing'}}
                        @if(empty($audit_details['twitter']))
                    </li>
                    @endif
                @if( empty($audit_details['graph_data']) )
                <li> 
                @endif
                 {{!empty($audit_details['graph_data']) ? '' :'Open graph tag missing'}}
                     @if(empty($audit_details['graph_data']))
                            </li>
                    @endif

                @if(!empty($audit_details['page_incomplete_graph'])) 
                <li>
                 @endif  
             {{!empty($audit_details['page_incomplete_graph']) ? 'Open Graph tag incomplete':''}}
              @if(!empty($audit_details['page_incomplete_graph'])) 
          </li> 
          @endif
                @if(empty($audit_details['robot']))
                <li>
                @endif
            {{empty($robot) ? 'Robot.txt missing' : ''}}
             @if(empty($audit_details['robot'])) 
                    </li>
                     @endif
            </ul>
                @if($audit_details['notices'] == 0)  <div class="clean"><i class="fa fa-check" aria-hidden="true"></i> Nice, no notices!</div> @endif
                <div class="link-div text-right view-more">
                    <a href="#notices">View Notices</a>
                </div>

            </div>
        </div>
    </section>

    <section id="errors">
   
            <div class="row errors-table">
                <table class="table">
                    <h4 style="color: red;padding: 10px;">
                        Errors ({{ $audit_details['errors'] }})
                    </h4>
                    <thead>
                        <tr>
                        <th>URL</th>
                        <th>Error</th>
                        <th>Description</th>
             
                        </tr>
                    </thead>
                    <tbody>
                    
                    @if(!empty($audit_details['links_empty_h1']))
                            @foreach(json_decode($audit_details['links_empty_h1']) as $link)
                                <tr>
                                    <td><a href="{{$link}}" target="_blank">{{$link}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Heading Tag Issues</td>
                                    <td>Your page is not using heading tags appropriately. Heading tags are the section titles of your content. They should be structured correctly.</td>
                              
                                </tr>
                            @endforeach    
                        @endif

                    @if(!empty($audit_details['page_miss_meta'] ))
                            @foreach(json_decode($audit_details['page_miss_meta']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Meta Description Missing</td>
                                    <td>Your meta description is missing. Meta descriptions are important for your CTR, which is a ranking factor for search engines.</td>
                                   
                                </tr>
                            @endforeach    
                        @endif

                    @if(!empty($audit_details['duplicate_title'] ))
                        @foreach(json_decode($audit_details['duplicate_title']) as $key => $val)
                            <tr>
                                <td><a href="{{$key}}" target="_blank">{{$key}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Identical Title Tags</td>
                                <td>One or more pages have identical title tags.</td>
                                
                            </tr>
                        @endforeach    
                    @endif

                    @if(!empty($audit_details['duplicate_meta_description']))
                        @foreach(json_decode($audit_details['duplicate_meta_description']) as $key => $val)
                            <tr>
                                <td><a href="{{$key}}" target="_blank">{{$key}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Identical Meta Descriptions</td>
                                <td>One or more pages have identical meta description tags.</td>
                          
                            </tr>
                        @endforeach    
                    @endif

                    @if(!empty($audit_details['link_500']))
                        @foreach(json_decode($audit_details['link_500']) as  $val)
                            <tr>
                                <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>500 Error</td>
                                <td>Your page has 500 error</td>
                               
                            </tr>
                        @endforeach    
                    @endif

                    @if(!empty($audit_details['link_404']))
                        @foreach(json_decode($audit_details['link_404']) as  $val)
                            <tr>
                                <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Broken Links Found</td>
                                <td>You have broken links on your page. Those links are sending users to a page that does not exist.</td>
                               
                            </tr>
                        @endforeach    
                    @endif
                    @if(!empty($audit_details['long_title'] ))
                            @foreach(json_decode($audit_details['long_title']) as $long)
                                <tr>
                                    <td><a href="{{$long}}" target="_blank">{{$long}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Meta Title Length</td>
                                    <td>You have a meta title but it is not the optimal length. It needs to be between 50-60 characters to fit inside Google's recommended length.</td>
                                  
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($audit_details['short_title'] ))
                            @foreach(json_decode($audit_details['short_title']) as $short)
                                <tr>
                                    <td><a href="{{$short}}" target="_blank">{{$short}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Short Title Tag</td>
                                    <td>The title tag is too short on this page. The reccommneed title tag length is between 50-60 characters.</td>
                                
                                </tr>
                            @endforeach
                        @endif
                        @if(!empty($audit_details['long_meta_description'] ))
                            @foreach(json_decode($audit_details['long_meta_description']) as $long_meta)
                                <tr>
                                    <td><a href="{{$long_meta}}" target="_blank">{{$long_meta}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Meta Description Too Long</td>
                                    <td>You have a meta description but it is not the optimal length. It needs to be around 160 characters to fit inside Google's recommended length.</td>
                                 
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($audit_details['short_meta_description']))
                            @foreach(json_decode($audit_details['short_meta_description']) as $short_meta)
                                <tr>
                                    <td><a href="{{$short_meta}}" target="_blank">{{$short_meta}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>Meta Description Too Short</td>
                                    <td>You have a meta description but it is not the optimal length. It needs to be around 160 characters to fit inside Google's recommended length.</td>
                                  
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    </table>
            </div>
    </section>
    
    <section id="warnings">
        <div class="row errors-table">
                <table class="table">
                    <h4 style="color: orange;padding: 10px;">
                        WARNINGS ({{ $audit_details['warning'] }})
                    </h4>
                    <thead>
                        <tr>
                        <th>URL</th>
                        <th>Error</th>
                        <th>Description</th>
                  
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($audit_details['page_without_canonical']))
                        @foreach(json_decode($audit_details['page_without_canonical']) as $val)
                            <tr>
                                <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                <td><span style="margin-right:5px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>Canonical Tag Missing</td>
                                <td>Your canonical tag is missing. Canonical tags are important because they tell search engines what the correct URL of the page should be.</td>
                       
                            </tr>
                        @endforeach    
                    @endif

                    @if(!empty($audit_details['less_code_ratio'] ))
                        @foreach(json_decode($audit_details['less_code_ratio']) as $less)
                                <tr>
                                    <td><a href="{{$less}}" target="_blank">{{$less}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>Low Text-HTML Ratio</td>
                                    <td>Your text-HTML ratio is too low. Search engines need text to know what a page is about. You should shoot for a 10% or greater text-HTML ratio.</td>
                                   
                                </tr>
                            @endforeach
                    @endif


                        @if(!empty($audit_details['less_page_words'] ))
                            @foreach(json_decode($audit_details['less_page_words']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>Thin Content</td>
                                    <td>There are less than 600 words of content on this page. It is recommended to add more quality text to this page to rank well.</td>
                               
                                </tr>
                            @endforeach    
                        @endif
                        @if(!empty($audit_details['link_302'] ))
                            @foreach(json_decode($audit_details['link_302']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>302 Redirect</td>
                                    <td>Your page has a 302 redirect to an internal link.</td>
                                    
                                </tr>
                            @endforeach    
                        @endif
                        
                        @if(!empty($audit_details['link_301']))
                            @foreach(json_decode($audit_details['link_301']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>301 redirect</td>
                                    <td>Your page has a 301 redirect to an internal link.</td>
                                 
                                </tr>
                            @endforeach    
                        @endif

                        @if(!empty($audit_details['page_incomplete_card']))
                            @foreach(json_decode($audit_details['page_incomplete_card']) as $val)
                                @if(strpos($val,"facebook") == false && strpos($val,"twitter") == false && strpos($val,"linkedin") == false && strpos($val,"instagram") == false)
                                    <tr>
                                        <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                        <td><span style="margin-right: 5px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>Twitter Card Issue</td>
                                        <td>Your site is missing open Twitter card tags. These are tags that allow you to control what content shows when a webpage is shared on social media.</td>
                                
                                    </tr>
                                @endif
                            @endforeach    
                        @endif

                        
                    </tbody>
                    </table>
            </div>
    </section>
    
    <section id="notices">
        <div class="row errors-table">
                <table class="table">
                    <h4 style="color: #0E6EEA;padding: 10px;">
                        NOTICES ({{ $audit_details['notices']}})
                    </h4>
                    <thead>
                        <tr>
                        <th>URL</th>
                        <th>Error</th>
                        <th>Description</th>
                   
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($audit_details['links_more_h1'] ) )
                            @foreach(json_decode($audit_details['links_more_h1']) as $link)
                                <tr>
                                    <td><a href="{{$link}}" target="_blank">{{$link}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>Multiple h1 Tags</td>
                                    <td>Multiple h1 tags found on your page. Each page should only have 1 h1 tag the includes the main kewyords.</td>
                                   
                                </tr>
                            @endforeach
                        @endif

                
                        @if(!empty($audit_details['page_twitter_missing'] ))
                            @foreach(json_decode($audit_details['page_twitter_missing']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>Twitter Card Missing</td>
                                    <td>Twitter card missing. This can help build trust in search engines.</td>
                                   
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($audit_details['page_open_graph']))
                            @foreach(json_decode($audit_details['page_open_graph']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>Open Graph Tags Missing</td>
                                    <td>Your site is missing open graph tags. These are tags that allow you to control what content shows when a webpage is shared on social media.</td>
                                   
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($audit_details['url_length']))
                            @foreach(json_decode($audit_details['url_length']) as $val)
                                <tr>
                                    <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                    <td><span style="margin-right: 5px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>Unfriendly URLs</td>
                                    <td>This page has long and unfriendly SEO URLs.</td>
                               
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($audit_details['page_incomplete_graph']))
                            @foreach(json_decode($audit_details['page_incomplete_graph']) as $val)
                                @if(strpos($val,"facebook") == false && strpos($val,"twitter") == false && strpos($val,"linkedin") == false && strpos($val,"instagram") == false)
                                    <tr>
                                        <td><a href="{{$val}}" target="_blank">{{$val}} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                        <td><span style="margin-right: 5px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>Open Graph Tags Incomplete</td>
                                        <td>Your site is missing some open graph tags. These are tags that allow you to control what content shows when a webpage is shared on social media.</td>
                                
                                    </tr>
                                @endif
                            @endforeach    
                        @endif
                    </tbody>
                    </table>
            </div>
    </section>
    </div>
</div>
</div>
</div>
</div>
</div>
  <div class="modal" id="emailReport" tabindex="-1" role="dialog" aria-labelledby="emailReport" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
           <form id='audit_email_form'>
            <input type="hidden" id="report_url" name="report_url" value="{{ $audit_details['site_url'] }}">
            <input type="hidden" id="report_id" name="report_id" value="{{ $audit_details['id'] }}">
          <!-- Modal Header -->
          <div class="modal-header">
               <h4>Send SEO Report</h4>
            <button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body" style="padding:20px;">
         
            <p>Send this SEO audit to an email.</p>

                    
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
          <a class="btn-warning btn-md" href="{{route('email_seo_report')}}" id='send_email_report' style='padding:7px;text-decoration:none;'>SEND REPORT</a>
          </div>
 </form>
        </div>
      </div>
    </div>
@endsection