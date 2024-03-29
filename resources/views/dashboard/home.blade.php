@extends('layouts.master')
@section('title', 'Dashboard - Ninja Reports')
@section('content')

<div class="col-md-10 overview home-container">
    <div class="inner">
    <div class="row audit-text pt-3 pb-3">
        <div class="col-md-12 text-start">
            <h3><STRONG>Welcome to Ninja Reports!</STRONG></h3>
        </div>
    </div>
    <div class="row Welcome-two-cols">
         <div class="col-md-6 Quick-col">
            <h6><span>Plan: </span><span>{{ $productname ?? '' }}</span></h6>
            <h6><span>Created: </span><span>{{ $created ?? '' }}</span></h6>
            <a href="{{route('subscription')}}" class="btn btn-warning btn-sm">Upgrade</a>
            <a href="https://ninjareports.zendesk.com/hc/en-us/requests/new" target="_blank" class="btn btn-secondary btn-sm">Support</a>
        </div>
        <div class="col-md-6 text-center guide-box">
                <h5>How to Guides:</h5>
            <p></p>
              <ul style="list-style-type: none">
                <li><a target="_blank" href="https://ninjareports.zendesk.com/hc/en-us/articles/360059931273">How to Run an Analysis </a></li>
                <li><a target="_blank" href="https://ninjareports.zendesk.com/hc/en-us/articles/360058188953">How to Run an Audit</a></li>
                <li><a target="_blank" href="https://www.ninjareports.com/on-page-seo-guide/">How to Fix SEO Issues</a></li>
              </ul>
      
        </div>
       
    </div>

        <div class="row">
        <div class="col-md-12 ">
             <h5>New Features:</h5>
            <p></p>
              <ol>
                <li>Download PDF Reports <i class="new-icon">NEW</i></li>
                <li>Traffic Estimation Reports <i class="new-icon">NEW</i></li>
                <li>White Label Reports <i class="new-icon">NEW</i></li>
                <li>Report History <i class="new-icon">NEW</i></li>
                <li>Backlink Analysis Tool <i class="new-icon">NEW</i></li>
                <li>Organic Keyword Rankings <i class="new-icon">NEW</i></li>
              </ol>
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script>
    $(document).ready(function($){
        var loggedIn = {{ auth()->check() ? 'true' : 'false' }};

        $("#analsis").click(function(e){
            var j$ = jQuery.noConflict();
            e . preventDefault();
            if(loggedIn){
                var url =  j$(".url").val();
                if(url){
                    window.location ="/analysis"+'?url='+url;
                }else{
                    window.location ="/analysis";
                }
            }else{
                j$("#loginModal").modal("show");
                $("#login_btn").click(function(e){
                    var analyze_url = $(".url").val();
                    if(analyze_url){
                        window.location ="/login?page="+"/analysis"+"&url="+analyze_url;
                    }else{
                        window.location ="/login?page="+"/analysis";
                    }
                });
            }
        });
    });
</script>
@endsection
