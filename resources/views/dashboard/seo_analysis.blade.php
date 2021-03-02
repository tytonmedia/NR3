@extends('layouts.master')
@section('title', 'SEO Analysis Tool - Ninja Reports')
@section('content')
<div class="col-md-10 overview analysis-container">
    <div class="inner">
        <div id="tool-desc" class="row">
        <div class="col-md-12">
        <h3>Technical SEO Report</h3>
        <p>Enter your URL into the toolbar including https:// or http:// and Ninja Reports will scan the page for over 100+ SEO factors. Analyze your URL to see how you can get better rankings in search engines. The analysis can take a few minutes to scan your page for all of the SEO factors.</p>
        @if( !auth()->check())
        <div class="try-it-free alert alert-success">
          <div class="row">
            <div class="col-md-12">
              <img src="{{asset('images/arrow down green.png')}}" alt="arrow"/>
      <h4>GET A FREE SEO ANALYSIS!</h4>
         <p>Try this tool for <b>FREE</b> with your <a href="/login">Google account</a>! Check your website for the latest SEO errors and see how you can get more traffic from search engines. Enter your URL below to get started.
</p>
</div>

        </div>
      </div>
      @endif
        <div id="error-box" class="alert alert-error" style="display:none;">
            Whoops, we could not run an analysis on that URL. Please try again.
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">

        <div class="col-md-6" style="padding-left:0">
                <form id='analyse_form'>
        <div class="row Analyze">
            <div class="col-md-8" style="padding-left:0">
                <input type="text" id='analyze' class="form-control" value="{{$_GET['url'] ?? ''}}"  placeholder="Enter URL">
            </div>
            <div class="col-md-4">
                <button class="btn" id='analyse'>CRAWL</button><img src="{{asset('images/762.gif')}}" alt="loading" id="loading" style="display:none;"/>
            </div>
        </div>
    </form>
        </div>
    </div>

</div>
</div>
<div class="row">
    <div class="col-md-12">
    <table class="table table-striped seo-report-table" style="margin-top:25px;">
        <thead class="light">
            <tr>
                <th>ID</th>
                <th>URL</th>
                <th>Status</th>
                <th>Score</th>
                <th>Errors</th>
                <th>Crawl Date</th>
                <th></th>
            </tr> 
        </thead>
            @if(!empty($seo_results))
            @foreach($seo_results as $key => $value)
            <tr class="report-{{$value['id']}}" data-id="<?php echo $value['id'];?>">
                <td>{{$key+1}}</td>
                <td>{{$value['url']}}</td>
                <td id="status">Crawled</td>
                <td>{{$value['passed_score']}}%</td>
                <td>{{$value['error_score']}}</td>
                 <td>{{date("F j, Y, g:i a", strtotime($value['updated_at'])) }}</td>
                 <td>
                        <a class="btn btn-primary btn-sm" href="{{ url('analysis', $value['id'])}}">View</a>
                        <a class="btn btn-success btn-sm" style="display:none" target="_blank" href="{{ url('download_seo_report', $value['id'])}}">PDF</a>
                        <a class="btn btn-info btn-sm"  style="display:none" href=""><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        <a class="btn btn-warning btn-sm delete-report" data-id="<?php echo $value['id'];?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </td> 
            </tr>
            @endforeach
            @else
            <tr class="empty"><td colspan="7" id="no-data-row">No data in table. Add a URL above to run an SEO Report.</td></tr>
            @endif
    </table>

</div>
</div>


    <!------------------------------------------Animation Script ProgressBarStart----------------------------------------------------->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
                <script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
        <!-- <script src="scripts/index.js"></script> -->
        <script>
            $(document).ready(function($) {

 $("#analyse_form").on("submit", function(e) {
    e.preventDefault();
      $('#analyse').trigger('click');
  });
                
                <?php
if(!empty($seo_results)) {
    ?>
   var table = $('.table').DataTable({
            "order": [[ 5, "desc" ]],
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 10
        });
   rowcount = table.rows().count();
<?php } ?>



                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var loggedIn = {{ auth()->check() ? 'true' : 'false' }};
                var analyze_url =  $("#analyze").val();
                    if(analyze_url && loggedIn){
                        if(isUrl(analyze_url) != false){
                         //   $(".progress-bar1").css("animation-play-state", "running");
                            analyzeURL();
                        }else{
                            //no url on load
                          //  alert("The link doesn't have http or https");
                        }
                    }


                $(".delete-report").click(function(e){
                    e . preventDefault();
                    var id = $(this).attr("data-id");
                    $.ajax({
                        type:'POST',
                        url:'/delete_seo_report/' + id,
                        data: id,
                        success: function (data) {
                           // data = JSON.stringify(data);
                             $('tr[data-id=' + data + ']').hide();
                             Swal.fire({
                              title: 'Success!',
                              text: 'SEO report removed.',
                              icon: 'success',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                        },
                        error: function (data) {
                             Swal.fire({
                              title: 'Error!',
                              text: data,
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                        }
                    });
                
                });
                $("#analyse").click(function(e){
                    e . preventDefault();
                    var url =  $("#analyze").val();

                     if(isUrl(url)) {
                    if(loggedIn){
                        
                        analyzeURL();
                            //analyzeURL();
                    }else{
                        var j$ = jQuery.noConflict();
                        var analyze_url = $("#analyze").val();

                        j$("#loginModal").modal("show");
                        $("#login_btn").click(function(e){
                            if(analyze_url){
                                window.location ="/login?page="+document.location.href+"&url="+analyze_url;
                            }else{
                                window.location ="/login?page="+document.location.href;
                            }
                        });
                    }
                } else {
                     Swal.fire({
                              title: 'Error!',
                              text: 'The URL you entered is not valid. Make sure to add http:// or https:// and www or non-www in your URL. EX: https://www.ninjareports.com.',
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                }

                });

                function analyzeURL(){
                    var url =  $("#analyze").val();
                    if(url.length != 0){
                    if(isUrl(url) != false){
                         //send analytics event
                             
                         gtag('event', 'click', {
                                      'event_category': 'audit',
                                      'event_label': 'click',
                                      'value': url
                                    });

                           // $(".progress-bar1").css("animation-play-state", "running");
                            $.ajax({
                                xhr : function() {
                                    var xhr = new window.XMLHttpRequest();
                                    
                                    xhr.upload.addEventListener('progress', function(e) {
                                        if (e.lengthComputable) {
                                            var percent = Math.round((e.loaded / e.total) * 100)-60;
                                            var datenow = new Date();
                                            //console.log(percent);
                                            $('#error-box').hide();
                                            $('#loading').show();
                                           $('#waiting').show();
                                             $('#analyse').attr('disabled','disabled');
                                             $('#analyse').text('CRAWLING');
                                             if(rowcount == 0){
                                              $('#empty').fadeOut(1000);
                                             }
                                             $('.table').append("<tr class='temp'><td colspan='7' class='text-center'><span class='loading-label'>Loading...</span></td></tr>");
                                             count = 0;
                                             wordsArray = ["Finding Backlinks...", "Checking Technical SEO...", "Gathering Keywords...", "Checking Mobile Responsiveness","Scanning Core Web Vitals..."];
                                            setInterval(function () {
                                              count++;
                                              $(".loading-label").fadeOut(200, function () {
                                                $(this).text(wordsArray[count % wordsArray.length]).fadeIn(200);
                                              });
                                            }, 9000);
                                        }
                                    });
                                    return xhr;
                                },
                                
                                type:'POST',
                                url:'/seo',
                                data:{url:url},
                                success:function(data){
                                    
                                    //console.log(data);
                                    if(data == 'notsuccessful' || data == 'Expired' || data == 'exceeded' || data == 'upgrade' ){
                                        $('#waiting').hide();
                                        $('#upgradeModel').show();
                                        $('.table tr.temp').remove();
                                        $('#analyse').removeAttr('disabled');
                                        $('#analyse').text('CRAWL');
                                        $('#loading').hide();
                                    }else if(data == 'duplicate'){
                                           // alert("That URL is already scanned. Check the table below.")
                                            Swal.fire({
                                                  title: 'Error!',
                                                  text: 'That URL is already scanned. Check the table below.',
                                                  icon: 'error',
                                                  showConfirmButton: 'false',
                                                  showCloseButton: 'true',
                                                })
                                    
                                             $('#analyse').removeAttr('disabled');
                                             $('#analyse').text('CRAWL');
                                             $("#backlink_audit").val('');
                                              $('#loading').hide();
                                                $('.table tr.temp').remove();
                                        } else {
                                        $('#waiting').hide();
                                        $('#loading').hide();
                                        $('.table tr.temp').hide();
                                        $('.no-data-row').hide();
                                        // var sdata = JSON.stringify(data);
                                        // jquery Example
                                        //alert(JSON.stringify(data));
                                        data = JSON.parse(data);
                                        id = data.id;
                                        url = data.url;
                                        passed_score = data.passed_score;
                                        error_score = data.error_score;
                                        updated_at = data.updated_at;
                                         
                                        var rowCount = table.rows().count();
                                         $('.table').append("<tr><td>" + rowCount + 1 + "</td><td>" + url + "</td><td>Crawled</td><td>"+ passed_score +"%</td><td>"+ error_score + "</td><td>"+ updated_at +"</td><td><a class='btn btn-primary btn-sm' style='margin-right:5px;' href='analysis/"+id+"'>View</a><a class='btn btn-success btn-sm' target='_blank'  style='display:none' href=''>PDF</a><a class='btn btn-info btn-sm'  style='display:none' href=''><i class='fa fa-refresh' aria-hidden='true'></i></a><a class='btn btn-warning btn-sm delete-report' data-id='"+id+"' href='#'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>");
                                          
                                      //  $('.analysis_section').show();
                                       // runPagespeed();
                                        $('#analyse').removeAttr('disabled');
                                        $('#analyse').text('CRAWL');
                                        $('#analyze').val('');

                                        
                                    }
                                    
                                },
                                error: function (request, status, error) {
                                $('#loading').hide();
                                $('#analyse').removeAttr('disabled');
                                $('#error-box').show();

                                }
                            });
                        }else{
                            alert("The URL doesn't have http:// or https://");
                        }
                    }else{
                        alert('add a url');
                    }
                }

                function isUrl(s) {
                    var pattern = new RegExp('^https?:\\/\\/'+ // protocol
                                            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                                            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                                            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                                            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                                            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
                                          return !!pattern.test(s);
                                      }

                });

        </script>


</div>
</div>

@endsection
