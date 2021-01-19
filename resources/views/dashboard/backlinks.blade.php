@extends('layouts.master')
@section('title', 'Backlink Checker Tool - Ninja Reports')
@section('content')
<div class="col-md-10 overview backlinks-container">
       <div id="tool-desc" class="row">

        <div class="col-md-12">
        <h3>Backlinks</h3>
        <p>Enter your domain into the toolbar including https:// or http:// and Ninja Reports will find all backlinks pointing to your website.</p>
    </div>

</div>
    <div class="row Analyze ">
        <div class="col-md-10">
            <input type="text" id="backlink_audit" class="form-control" value="{{$_GET['url'] ?? ''}}" placeholder="Enter URL">
        </div>
        <div class="col-md-2">
            <button class="btn" id="analyse">ANALYZE</button>
        </div>
    </div>
        <div id="waiting" style="display:none;">
        <div class="loading-box">
            <img src="{{asset('images/806.gif')}}" alt="loading"/>
            <h4>Finding Backlinks...</h4>
            <p>Please wait while we find the backlinks. This process can take a few minutes.</p>
        </div>
    </div>
     <div id="error-box" style="display:none"><h4>Whoops!</h4><p>There was an error trying to run your backlink audit. Please check your URL and try again!</p></div>
    <div id="text-container" ></div>
     <!------------------------------------------Animation Script ProgressBarStart----------------------------------------------------->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.1/dist/circle-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
    <!-- <script src="scripts/index.js"></script> -->
    <Script>

        /**
            * index.js
            * - All our useful JS goes here, awesome!
            Maruf-Al Bashir Reza
            */
        function insertParam(key, value) {
                key = encodeURIComponent(key);
                value = encodeURIComponent(value);

                // kvp looks like ['key1=value1', 'key2=value2', ...]
                var kvp = document.location.search.substr(1).split('&');
                let i=0;

                for(; i<kvp.length; i++){
                    if (kvp[i].startsWith(key + '=')) {
                        let pair = kvp[i].split('=');
                        pair[1] = value;
                        kvp[i] = pair.join('=');
                        break;
                    }
                }

                if(i >= kvp.length){
                    kvp[kvp.length] = [key,value].join('=');
                }

                // can return this or...
                let params = kvp.join('&');

                // reload page with new params
                document.location.search = params;
            }

        $(document).ready(function($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var loggedIn = {{ auth()->check() ? 'true' : 'false' }};
            var analyze_url =  $("#backlink_audit").val();

            analyze_url = ((analyze_url.indexOf('://') === -1)) ? 'https://' + analyze_url : analyze_url;

                if(analyze_url && loggedIn){
                        if(isUrl(analyze_url) != false){
                         //   $(".progress-bar1").css("animation-play-state", "running");
                            get_backlinks();
                        }else{
                            //alert("The link doesn't have http or https");
                        }
                    }
           // enter keyd
        $(document).bind('keypress', function(e) {
            if(e.keyCode==13){
                 $('#backlink_audit').trigger('click');
             }
        });

            $(".btn").click(function(e){
                    e.preventDefault();
                    var url =  $("#backlink_audit").val();
                    url = ((url.indexOf('://') === -1)) ? 'https://' + url : url;

                    if(isUrl(url)) {
                    if(loggedIn){
                        !!url && insertParam('url', url);
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
                    alert('URL not valid');
                }
                });
                function get_backlinks(){
                    var url =  $("#backlink_audit").val();
                    url = ((url.indexOf('://') === -1)) ? 'https://' + url : url;
                    
                        if(url.length != 0){
                            if(isUrl(url) !== false){

                                        gtag('event', 'click', {
                                      'event_category': 'backlinks',
                                      'event_label': 'click',
                                      'value': url
                                    });

                                $.ajax({
                                    xhr : function() {
                                        var xhr = new window.XMLHttpRequest();
                                        xhr.upload.addEventListener('progress', function(e) {
                                            if (e.lengthComputable) {
                                                var percent = Math.round((e.loaded / e.total) * 100)-60;
                                                //console.log(percent);
                                                $('#error-box').hide();
                                                $('#waiting').show();
                                                $('#tool-desc').slideUp();
                                                $('#analyse').attr('disabled','disabled');
                                            }
                                        });
                                        return xhr;
                                    },
                                    type:'POST',
                                    url:'/seo_backlinks',
                                    data:{url:url},
                                    
                                    success:function(data){
                                        if(data == 'notsuccessful' || data == 'Expired' || data == 'exceeded' ){
                                            $('#waiting').hide();
                                            $('#myModal').show();
                                        }else{
                                            $('div#text-container').append(data);
                                            $('#waiting').hide();
                                            $('#analyse').removeAttr('disabled');
                                          
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

            var j$ = jQuery.noConflict();
            //console.log(loggedIn);
            if (!loggedIn){
                $(".btn").click(function(){
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

            function animateElements() {
                $('.Progress').each(function() {
                    var elementPos = $(this).offset().top;
                    var topOfWindow = $(window).scrollTop();
                    var percent = $(this).find('.circle').attr('data-percent');
                    //console.log(percent);
                    var percentage = parseInt(percent, 10) / parseInt(100, 10);
                    var animate = $(this).data('animate');
                    if (elementPos < topOfWindow + $(window).height() - 30 && !animate) {
                        $(this).data('animate', true);
                        $(this).find('.circle').circleProgress({
                            startAngle: -Math.PI / 2,
                            value: percent / 100,
                            thickness: 13,
                            size: 190,
                            lineCap: "round",
                            emptyFill: "#f2f2f2",
                            fill: {
                            color: '#1B58B8'
                            }
                        }).on('circle-animation-progress', function(event, progress, stepValue) {
                            $(this).find('div').text((stepValue * 100).toFixed(0) + "%");
                        }).stop();
                    }
                });
            }

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
@endsection