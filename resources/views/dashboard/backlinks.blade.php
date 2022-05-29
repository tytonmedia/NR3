@extends('layouts.master')
@section('title', 'Backlink Checker Tool - Ninja Reports')
@section('content')
<div class="col-md-10 overview backlinks-container">
    <div class="inner">
       <div id="tool-desc" class="row">

        <div class="col-md-12">
        <h3>Backlink Reports</h3>
        <p>Enter your domain into the toolbar including https:// or http://, www or non-www and Ninja Reports will scan your website to see your backlink profile, toxicity score, referring TLDs and more.</p>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">

        <div class="col-md-6" style="padding-left:0">
                <form id='analyse_form'>
        <div class="row Analyze">
            <div class="col-md-8" style="padding-left:0">
                <input type="text" id='backlink_audit' class="form-control" value="{{$_GET['url'] ?? ''}}"  placeholder="Enter URL">
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
                <th>URL</th>
                <th>Status</th>
                <th>Backlinks</th>
                <th>Referring Domains</th>
                <th>Crawl Date</th>
                <th></th>
            </tr> 
        </thead>
            @if(!empty($backlink_results))
            @foreach($backlink_results as $key => $value)
            <tr class="report-{{$value['id']}}" data-id="<?php echo $value['id'];?>">
                <td>{{$key + 1}}</td>
                <td>{{$value['site_url']}}</td>
                <td id="status">Crawled</td>
                <td>{{number_format($value['domains_num'])}}</td>
                <td>{{number_format($value['backlinks_num'])}}</td>
                 <td>{{date("F j, Y, g:i a", strtotime($value['updated_at'])) }}</td>
                 <td>
                        <a class="btn btn-primary btn-sm" href="{{ url('backlinks', $value['id'])}}">View</a>
                        <a style="display:none;" class="btn btn-success btn-sm" target="_blank" href="{{ url('download_backlink_report', $value['id'])}}">PDF</a>
                        <a style="display:none;" class="btn btn-info btn-sm" href=""><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        <a class="btn btn-warning btn-sm delete-report" data-id="<?php echo $value['id'];?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </td> 
            </tr>
            @endforeach
            @else
            <tr class="empty"><td colspan="7">No data in table. Add a URL above to run a Backlink Report.</td></tr>
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
if(!empty($backlink_results)) {
    ?>
 $('.table').DataTable({
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 10
        });
<?php } ?>
              $(document).bind('keypress', function(e) {
            if(e.keyCode==13){
                 $('#analyse').trigger('click');
             }
        });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var loggedIn = {{ auth()->check() ? 'true' : 'false' }};
            var analyze_url =  $("#backlink_audit").val();

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
                        url:'/delete_backlink_report/' + id,
                        data: id,
                        success: function (data) {
                             $('tr[data-id=' + data + ']').hide();
                             Swal.fire({
                              title: 'Success!',
                              text: 'Backlink report removed.',
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

                    var url =  $("#backlink_audit").val();

                    if(isUrl(url)) {
                    if(loggedIn){

                        get_backlinks();
                    }else{
                        var j$ = jQuery.noConflict();
                       
                        j$("#loginModal").modal("show");
                        $("#login_btn").click(function(e){
                            var analyze_url = $("#backlink_audit").val();
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
                              text: 'The URL you entered is not valid. Make sure to add http:// or https:// and www or non-www in your URL. EX: https://www.ninjareports.com.',
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                }
                });
                function get_backlinks(){
                    var url =  $("#backlink_audit").val();

                        if(url.length != 0){
                            if(isUrl(url) !== false){

                                        gtag('event', 'click', {
                                      'event_category': 'backlinks',
                                      'event_label': url
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
                                    url:'/seo_backlinks',
                                    data:{url:url},
                                    
                                    success:function(data){
                                        if(data == 'notsuccessful' || data == 'Expired' || data == 'exceeded' || data == 'payme'){
                                           // $('#waiting').hide();
                                            $('#backlinksUpgrade').show();
                                            $('#analyse').removeAttr('disabled');
                                             $('#analyse').text('CRAWL');
                                             $('.temp').hide();
                                             }else if(data == 'empty'){
                                                    Swal.fire({
                              title: 'Sorry!',
                              text: 'We could not find any backlinks pointing to your URL. Please try another URL.',
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                                                    
   $('#analyse').removeAttr('disabled');
                                             $('#analyse').text('CRAWL');
                                             $("#backlink_audit").val('');
                                              $('#loading').hide();
                                                $('.table tr.temp').remove();
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
                                        url = JSON.stringify(this.url).replace(/['"]+/g, '');
                                        backlinks = JSON.stringify(this.backlinks);
                                        referring_domains = JSON.stringify(this.referring_domains);
                                        updated_at = JSON.stringify(this.updated_at).replace(/['"]+/g, '');
                                         
                                        });

                                         $('.table').append("<tr><td>" + id + "</td><td>" + url + "</td><td>Crawled</td><td>"+ backlinks.replace(/['"]+/g, '') +"</td><td>"+ referring_domains.replace(/['"]+/g, '') + "</td><td>"+ updated_at +"</td><td><a class='btn btn-primary btn-sm' href='analysis/"+id+"'>View</a><a class='btn btn-success btn-sm' target='_blank' style='display:none;' href=''>PDF</a><a style='display:none;' class='btn btn-info btn-sm' href=''><i class='fa fa-refresh' aria-hidden='true'></i></a><a class='btn btn-warning btn-sm delete-report' data-id='"+id+"' href='#'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>");
                                          
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
                        }else{
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


    </Script>
    <!------------------------------------------Animation Script ProgressBar End----------------------------------------------------->
</div>
</div>
@endsection
