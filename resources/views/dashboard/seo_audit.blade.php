@extends('layouts.master')
@section('title', 'SEO Audit Tool - Ninja Reports')
@section('content')

<div class="col-md-10 overview audit-container">
     <div class="inner">
       <div id="tool-desc" class="row">

        <div class="col-md-12">
        <h3>SEO Audit Report</h3>
        <p>Enter your URL into the toolbar including https:// or http:// and Ninja Reports will scan your entire website and check over 100+ SEO factors and tell you know how to fix them and rank higher.</p>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">

        <div class="col-md-6" style="padding-left:0">
                <form id='analyse_form'>
        <div class="row Analyze">
            <div class="col-md-8" style="padding-left:0">
                <input type="text" id='seo_audit' class="form-control" value="{{$_GET['url'] ?? ''}}"  placeholder="Enter URL">
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
                <th>Crawl Date</th>
                <th></th>
            </tr> 
        </thead>
            @if(!empty($audit_results))
            @foreach($audit_results as $key => $value)
            <tr class="report-{{$value['id']}}" data-id="<?php echo $value['id'];?>">
                <td>{{$key + 1}}</td>
                <td>{{$value['site_url']}}</td>
                <td id="status">Crawled</td>
                 <td>{{date("F j, Y, g:i a", strtotime($value['updated_at'])) }}</td>
                 <td>
                        <a class="btn btn-primary btn-sm" href="{{ url('audit', $value['id'])}}">View</a>
                        <a class="btn btn-success btn-sm"  style='display:none' target="_blank" href="{{ url('download_audit_report', $value['id'])}}">PDF</a>
                        <a class="btn btn-info btn-sm"  style='display:none' href=""><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        <a class="btn btn-warning btn-sm delete-report" data-id="<?php echo $value['id'];?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </td> 
            </tr>
            @endforeach
            @else
            <tr class="empty"><td colspan="7" id="no-data-row">No data in table. Add a URL above to run an SEO Audit Report.</td></tr>
            @endif
    </table>

</div>
</div>
     <!------------------------------------------Animation Script ProgressBarStart----------------------------------------------------->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.1/dist/circle-progress.js"></script>
        <script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
    <!-- <script src="scripts/index.js"></script> -->
    <Script>

        $(document).ready(function($) {
        
                     <?php
if(!empty($audit_results)) {
    ?>
 $('.table').DataTable({
            "autoWidth": true,
            "lengthChange": false,
            "pageLength": 10
        });
<?php } ?>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var loggedIn = {{ auth()->check() ? 'true' : 'false' }};
            var analyze_url =  $("#seo_audit").val();


            if (analyze_url && loggedIn) {
                if(isUrl(analyze_url) != false){
                    get_audit();
                }else{
                    //alert("The link doesn't have http or https");
                }    
            }
           
            $(document).bind('keypress', function(e) {
            if(e.keyCode==13){
                 $('#seo_audit').trigger('click');
             }
            });

            $(".delete-report").click(function(e){
                    e.preventDefault();
                    var id = $(this).attr("data-id");
                    $.ajax({
                        type:'POST',
                        url:'/delete_audit_report/' + id,
                        data: id,
                        dataType: 'json',
                        success: function (data) {
                            //   $("tr[data-id=]").hide();
                             if(data === 'success'){
                             $('tr[data-id=' + data + ']').hide();
                             Swal.fire({
                              title: 'Success!',
                              text: 'Backlink report removed.',
                              icon: 'success',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                         }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                
                });

            $("#analyse").click(function(e){
                    e.preventDefault();
                    var url =  $("#seo_audit").val();
                    
                    if(isUrl(url)) {
                    if(loggedIn){
            
                        get_audit();
                    }else{
                        var j$ = jQuery.noConflict();
                        j$("#loginModal").modal("show");
                        $("#login_btn").click(function(e){
                            var analyze_url = $("#seo_audit").val();
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
                function get_audit(){
                    var url =  $("#seo_audit").val();
                        if(url.length != 0){
                            if(isUrl(url) !== false){

                                        gtag('event', 'click', {
                                      'event_category': 'audit',
                                      'event_label': 'click',
                                      'value': url
                                    });



                                $.ajax({
                                    xhr : function() {
                                        var xhr = new window.XMLHttpRequest();
                                        xhr.upload.addEventListener('progress', function(e) {
                                            if (e.lengthComputable) {
                                                //console.log(percent);
                                              //  $('#error-box').hide();
                                               // $('#waiting').show();
                                               // $('#tool-desc').slideUp();
                                                $('#analyse').attr('disabled','disabled');
                                                $('#analyse').text('CRAWLING');
                                                $('#loading').show();
                                                $('.table').append("<tr class='temp'><td colspan='7' class='text-center'>Loading...</td></tr>");

                                            }
                                        });
                                        return xhr;
                                    },
                                    type:'POST',
                                    url:'/seo_audit',
                                    data:{url:url},
                                    
                                    success:function(data){
                                        if(data == 'notsuccessful' || data == 'Expired' || data == 'exceeded' ){
                                            $('#waiting').hide();
                                            $('#myModal').show();
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
                                                $('.table tr.temp').remove();
                                                $('#loading').hide();
                                        // var sdata = JSON.stringify(data);
                                        // jquery Example
                                     
                                        $(JSON.parse(data)).each(function() {
                                        id = JSON.stringify(this.id);
                                        url = JSON.stringify(this.url).replace(/['"]+/g, '');
                                        updated_at = JSON.stringify(this.updated_at).replace(/['"]+/g, '');
                                        });

                                         $('.table').append("<tr><td>" + id + "</td><td>" + url + "</td><td>Crawled</td><td>"+ updated_at +"</td><td><a class='btn btn-primary btn-sm' href='audit/"+id+"'>View</a><a class='btn btn-success btn-sm' target='_blank' style='display:none' href=''>PDF</a><a  style='display:none' class='btn btn-info btn-sm' href=''><i class='fa fa-refresh' aria-hidden='true'></i></a><a class='btn btn-warning btn-sm delete-report' data-id='"+id+"' href='#'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>");
                                          $('#analyse').removeAttr('disabled');
                                          $('#analyse').text('CRAWL');
                                          $('#no-data-row').hide();
                                          

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
                            alert("The link doesn't have http:// or https://");
                        }
                    }else{
                        alert('add url');
                    }
                }

            // var j$ = jQuery.noConflict();
            // //console.log(loggedIn);
            // if (!loggedIn){
            //     $(".btn").click(function(){
            //         j$('#loginModal').modal('show');
            //         $("#login_btn").click(function(e){
            //             var analyze_url = $("#seo_audit").val();
            //             if(analyze_url){
            //                 window.location ="/login?page="+document.location.href+"&url="+analyze_url;
            //             }else{
            //                 window.location ="/login?page="+document.location.href;
            //             }
            //         });
            //     });
            // }

            // Show animated elements
            
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
