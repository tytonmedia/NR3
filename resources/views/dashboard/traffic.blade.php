@extends('layouts.master')
@section('title', 'Traffic Report - Ninja Reports')
@section('content')
<div class="col-md-10 overview backlinks-container">
    <div class="inner">
       <div id="tool-desc" class="row">

        <div class="col-md-12">
        <h3>Traffic Report</h3>
        <p>Enter a domain into the toolbar and Ninja Reports will generate a traffic analysis of the website. For example, ninjareports.com</p>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">

        <div class="col-md-6" style="padding-left:0">
                <form id='analyse_form'>
        <div class="row Analyze">
            <div class="col-md-8" style="padding-left:0">
                <input type="text" id='traffic_audit' class="form-control" value="{{$_GET['url'] ?? ''}}"  placeholder="Enter Domain, eg: ninjareports.com">
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
                <th>#</th>
                <th>Domain</th>
                <th>Status</th>
                <th>Traffic</th>
                <th>Crawl Date</th>
                <th></th>
            </tr> 
        </thead>
            @if(!empty($traffic_results))
            @foreach($traffic_results as $key => $value)
            <tr class="report-{{$value['id']}}" data-id="<?php echo $value['id'];?>">
                <td>{{$key + 1}}</td>
                <td>{{$value['domain']}}</td>
                <td id="status">Crawled</td>
                <td>{{number_format($value['traffic'])}}</td>
                 <td>{{date("F j, Y, g:i a", strtotime($value['updated_at'])) }}</td>
                 <td>
                        <a class="btn btn-primary btn-sm" href="{{ url('traffic', $value['id'])}}">View</a>
                        <a class="btn btn-warning btn-sm delete-report" data-id="<?php echo $value['id'];?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </td> 
            </tr>
            @endforeach
            @else
            <tr class="empty"><td colspan="7">No data in table. Add a domain above to run a Traffic Report.</td></tr>
            @endif
    </table>

</div>
</div>

     <!------------------------------------------Animation Script ProgressBarStart----------------------------------------------------->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.1/dist/circle-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
    
    <!-- <script src="scripts/index.js"></script> -->
    <script>


        $(document).ready(function($) {
            <?php
if(!empty($traffic_results)) {
    ?>
 $('.table').DataTable({
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 10
        });
<?php } ?>
 $("#analyse_form").on("submit", function(e) {
    e.preventDefault();
      $('#analyse').trigger('click');
  });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var loggedIn = {{ auth()->check() ? 'true' : 'false' }};
            var analyze_url =  $("#traffic_audit").val();

                // if(analyze_url && loggedIn){
                //         if(isUrl(analyze_url) != false){
                //          //   $(".progress-bar1").css("animation-play-state", "running");
                //             get_backlinks();
                //         }else{
                //             //alert("The link doesn't have http or https");
                //         }
                //     }
           // enter keyd

            $(".delete-report").click(function(e){
                    e.preventDefault();
                    var id = $(this).attr("data-id");
                    $.ajax({
                        type:'POST',
                        url:'/delete_traffic_report/' + id,
                        data: id,
                        success: function (data) {
                             $('tr[data-id=' + data + ']').hide();
                             Swal.fire({
                              title: 'Success!',
                              text: 'Traffic report removed.',
                              icon: 'success',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                        },
                        error: function (data) {
                           
                            Swal.fire({
                              title: 'Error!',
                              text: JSON.stringify(data),
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                        }
                    });
                
                });

         //   $("#analyse").click(function(e){
                 $("#analyse").click(function(e){
                    e.preventDefault();

                    var domain =  $("#traffic_audit").val();

                    if(isDomain(domain)) {
                    if(loggedIn){

                        get_traffic();
                    }else{
                        var j$ = jQuery.noConflict();
                       
                        j$("#loginModal").modal("show");
                        $("#login_btn").click(function(e){
                            var analyze_url = $("#traffic_audit").val();
                            if(analyze_url){
                                window.location ="/login?url="+encodeURIComponent(analyze_url)+"&page="+document.location.href;
                            }else{
                                window.location ="/login?page="+document.location.href;
                            }
                        });
                    }

                } else {
                 //   alert('The URL you entered is not valid. Make sure to add http:// or https:// and www or non-www in your URL. EX: https://www.ninjareports.com.');
                    Swal.fire({
                              title: 'Error!',
                              text: 'Please enter a valid domain name. Do not include https:// or http://!',
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                }
                });
                function get_traffic(){
                    var domain =  $("#traffic_audit").val();

                        if(domain.length != 0){
                            if(isDomain(domain) !== false) {

                                        gtag('event', 'click', {
                                      'event_category': 'traffic',
                                      'event_label': 'click',
                                      'value': domain
                                    });

                                $.ajax({
                                    xhr : function() {
                                        var xhr = new window.XMLHttpRequest();
                                        xhr.upload.addEventListener('progress', function(e) {
                                            if (e.lengthComputable) {
                                                var percent = Math.round((e.loaded / e.total) * 100)-60;
                                                //console.log(percent);
                                                $('#error-box').hide();
                                               $('#analyse').text('CRAWLING');
                                             $('.table').append("<tr class='temp'><td colspan='7' class='text-center'>Loading...</td></tr>");
                                                $('#analyse').attr('disabled','disabled');
                                            }
                                        });
                                        return xhr;
                                    },
                                    type:'POST',
                                    url:'/seo_traffic',
                                    data:{domain:domain},
                                    
                                    success:function(data){
                                        if(data == 'notsuccessful' || data == 'Expired' || data == 'exceeded' || data == 'payme'){
                                           // $('#waiting').hide();
                                            $('#trafficUpgrade').show();
                                            $('#analyse').removeAttr('disabled');
                                             $('#analyse').text('CRAWL');
                                             $('.temp').hide();
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
                                        }else{
                                           // $('div#text-container').append(data);
                                            $('#loading').hide();
                                         $('.table tr.temp').remove();
                                        // var sdata = JSON.stringify(data);
                                        // jquery Example
                                       // alert(data);
                                        $(JSON.parse(data)).each(function() {
                                        id = JSON.stringify(this.id);
                                        domain = JSON.stringify(this.domain).replace(/['"]+/g, '');
                                        traffic = JSON.stringify(this.traffic);
                                        updated_at = JSON.stringify(this.updated_at).replace(/['"]+/g, '');
                                         
                                        });

                                         $('.table').append("<tr><td>" + id + "</td><td>" + domain + "</td><td>Crawled</td><td>"+ traffic +"</td><td>"+ updated_at +"</td><td><a class='btn btn-primary btn-sm' href='analysis/"+id+"'>View</a><a class='btn btn-success btn-sm' target='_blank' style='display:none;' href=''>PDF</a><a style='display:none;' class='btn btn-info btn-sm' href=''><i class='fa fa-refresh' aria-hidden='true'></i></a><a class='btn btn-warning btn-sm delete-report' data-id='"+id+"' href='#'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>");
                                          
                                      //  $('.analysis_section').show();
                                       // runPagespeed();
                                        $('#analyse').removeAttr('disabled');
                                        $('#analyse').text('CRAWL');

                                             
                                          
                                        }
                                    }
                                    ,
                                error: function (request, status, error) {
                                $('#waiting').hide();
                                $('#analyse').removeAttr('disabled');
                                $('#error-box').show();
                                }
                                });
                        } else{
                         //   alert("The link doesn't have http:// or https://");
                            Swal.fire({
                              title: 'Error!',
                              text: 'The link doesnt have http:// or https://',
                              icon: 'error',
                            });
                        }
                    }else{
                         Swal.fire({
                              title: 'Error!',
                              text: 'Add a URL to the input and click crawl to run a report.',
                              icon: 'error',
                            });
                    }
                }

            var j$ = jQuery.noConflict();
            //console.log(loggedIn);
            if (!loggedIn){
                $("#analyse").click(function(){
                    j$('#loginModal').modal('show');
                    $("#login_btn").click(function(e){
                        var analyze_url = $("#backlink_audit").val();
                        if(analyze_url){

                            window.location ="/login?page="+document.location.href+"&url="+analyze_url;
                        }else{

                            window.location ="/login?page="+document.location.href;
                        }
                    });
                });
            }

           // $(window).scroll(animateElements);
           function isDomain(s) {
                    var pattern = new RegExp('^(http|https)://','i'); // fragment locator
                                          if(pattern.test(s)){
                                            return false;
                                          } else{
                                            return true;
                                          }
                                      }
        });


    </Script>
    <!------------------------------------------Animation Script ProgressBar End----------------------------------------------------->
</div>
</div>
@endsection

