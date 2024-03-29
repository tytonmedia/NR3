
@extends('layouts.master')
@section('title', $seo_audit_details['url'].' SEO Report | Ninja Reports')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha1/html2canvas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.1/dist/circle-progress.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
            <script>
              
              $(document).ready(function($) {

                $("#download_report").click(function(e){
                    e.preventDefault();

                    html2canvas($(".analysis-container .inner"), {
                    onrendered: function(canvas) {        
                    var imgData = canvas.toDataURL('image/png');
                        var imgWidth = 210; 
                    var pageHeight = 295;  
                   var imgHeight = canvas.height * imgWidth / canvas.width;
                   var heightLeft = imgHeight;
                   var doc = new jsPDF('p', 'mm');
                       var position = 0;

                  doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                 heightLeft -= pageHeight;

                 while (heightLeft >= 0) {
                   position = heightLeft - imgHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                 }
                 doc.save( 'seo_report.pdf'); 

                        }

                      });
                  });


            $('.analysis_section').show(); 
                      
               $("#emailreportlink").click(function(e){
                    e.preventDefault();
                    $("#emailReport").modal("show");
                  });


               <?php if(empty($payment)) { ?>
                    var payment = 0;
               <?php } else { ?>
                    var payment = 1;
                  <?php } ?>

                $(".how-to-fix-button").click(function(e){
                    e.preventDefault();
                     if(payment == 1) {
                    $(this).children('i').toggleClass("fa-caret-right fa-caret-down");
                    $(this).siblings('.how-to-fix').toggle();
                      } else {
                           Swal.fire({
                              title: 'Error!',
                              html:
                                'Only paid users can view our SEO advice on how to rank better. To see how to fix this issue, ' +
                            '<a href="/subscription">upgrade</a> ' +
                           'now to start ranking better today!',
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                      }
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
                                url:'/email_seo_report',
                                data: data,
                                success:function(data){
                                $("#emailReport").modal("hide");
                                 Swal.fire({
                              title: 'Sent!',
                              text: 'Your email report was sent!',
                              icon: 'success',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                                },
                                error: function (request, status, error) {
                                      Swal.fire({
                              title: 'Error!',
                              text: error,
                              icon: 'error',
                              showConfirmButton: 'false',
                              showCloseButton: 'true',
                            });
                                }
                            });

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
        <div class="col-md-8 text-right" style="padding-right:0;">
          <a class="btn btn-sm btn-info" target="_blank" id="download_report" href="#"><i class="fa fa-download" aria-hidden="true"></i> DOWNLOAD PDF</a>
         
      </div>
    </div>
  <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding-left:0">
          
            <h5 id="url"><STRONG>SEO Report:</STRONG> {{$seo_audit_details['url']}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding-right:0">
            <h6>Last Crawl: {{ date('F j, Y, g:i a', strtotime($seo_audit_details['created_at'])) }}</h6>
        </div>
    </div>
    
    <section id="analysis" class="analysis-page">
        <div class="row Analysis-details">
                  <div class="col-md-4 score-box">
                     <h4>Page SEO Score</h4>
                <div class="blue text-center">
                    <div class="score-wrapper">
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

    var text = '<?php echo $seo_audit_details['passed_score'];?>%',
        textX = Math.round((width - ctx.measureText(text).width) / 2),
        textY = height / 2;

    ctx.fillText(text, textX, textY);
    ctx.save();
  }
});
                    var passed_score = <?php echo $seo_audit_details['passed_score'];?>;
                    var opp = <?php echo 100 - $seo_audit_details['passed_score'];?>;

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
                    </div>
                </div>
                <h4 style="text-align:center;font-size:24px;">{{$seo_audit_details['score_description'] }}</h4>
            </div>
            <div class="col-md-4 screenshot-container">
                 <h4>Mobile Responsive</h4>
                <!-- <img src="images/desktop.jpg" style="height:140px;margin-top:15%;margin-left:5%;"> -->
                <div class="screen-container">
                    <div class="screen monitor">
                        <div class="content" id="image">
                         <img src="{{$seo_audit_details['image']}}" style="width:100%" /> 
                        </div>
                        <div class="basee">
                            <div class="grey-shadow"></div>
                            <div class="foot top"></div>
                            <div class="foot bottom"></div>
                            <div class="shadow"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 breakdown">  
                 <h4 style="margin-bottom:35px;">SEO Breakdown</h4>
                <h6 class="pass">
                    <i class="fa fa-check" style="color:#008000" aria-hidden="true"></i> Passed
                </h6>
                <div class="progress" >
                        <div id="passed_progress" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="10"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{ $seo_audit_details['passed_score'] ?? ''}}%;background-color: #008000;"></div></div>
                <div class="clear"></div>
                 <h6 class="mca">
                    <i style="color:#ff0000" class="fa fa-exclamation-triangle" aria-hidden="true"></i> Errors
                </h6>
                <div class="progress">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{$seo_audit_details['error_score'] ?? ''}}%;background-color: #ff0000;"></div>
                </div>
                <div class="clear"></div>
                <h6 class="cta">
                    <i style="color:#ff6600" class="fa fa-exclamation-circle" aria-hidden="true"></i> Warnings
                </h6>
                <div class="progress" >
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{$seo_audit_details['warning_score']}}%;background-color: #ff6600;"></div>
                </div>
                <div class="clear"></div>
                <h6 class="pta">
                    <i style="color:#0E6EEA" class="fa fa-flag" aria-hidden="true"></i> Notices
                </h6>
                <div class="progress" >
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="70"
                        aria-valuemin="25" aria-valuemax="100" style="width:{{$seo_audit_details['notice_score'] ?? 0}}%;background-color: #0E6EEA;"></div>
                </div>
                <div class="clear"></div>
                <div class="row breakdown-data">
                        <div class="col-md-4">
                          @if($seo_audit_details['urls_num'] == 'payme')
                          <label>Backlinks</label><span>N/A</span>
                          @elseif($seo_audit_details['urls_num'] == 'empty')
                          <label>Backlinks</label><span>0</span>
                          @else
                          <label>Backlinks</label><span>{{number_format($seo_audit_details['urls_num'])}}</span>
                          @endif
                           
                        </div>
                          <div class="col-md-4">
                           <label>Load Time</label><span>{{$seo_audit_details['loadtime']}}</span>
                        </div>
                        <div class="col-md-4">
                           <label>Word Count</label><span>{{number_format($seo_audit_details['page_words'])}}</span>
                        </div>

                </div>
                <p class="overview-desc" style="margin-top:15px;display:none;">On this scan, {{ $seo_audit_details['passed_score'] ?? ''}} factors passed with {{ $seo_audit_details['error_score'] ?? ''}} errors, {{$seo_audit_details['warning_score'] ?? ''}} warnings and {{$seo_audit_details['notice_score'] ?? ''}} notices. Errors are the most important to fix in order to rank higher in search engines.</p>


            </div>
      
        </div>
    </section>

    <section id='header'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Header</h2>
        <div class="heading-section">

            <div class="row">
                <div class="col-md-3">
                    <h6>
                        @if($seo_audit_details['title_length'] > 50 && $seo_audit_details['title_length'] <= 60)
                            <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                             @elseif($seo_audit_details['title_length'] <= 50 || $seo_audit_details['title_length'] > 60)
                                 <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                        @endif
                        Title Tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The title tag is the text that Google often uses to display your website link in SERPs (search engine results pages)."><i class="fa fa-info-circle" ></i></a></h6>
             
              </div>
                <div class="col-md-9">
                    <p>{{$seo_audit_details['title']}}</p>
                   <p class="analysis-more-detail">Length: <strong>{{$seo_audit_details['title_length']}} Characters</strong> (Recommended: 60 characters)</p>
                    @if($seo_audit_details['title_length'] <= 50 || $seo_audit_details['title_length'] > 60)
                      <button id="fix_title_tag" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Fixing Title Tags</h3>
                        <p>Your title tag is not optimized for length. Google allows up to 60 characters in the title tag. Any more characters and the title tag is cut off in the SERPs (search engine results pages).</p>
                        <p>How you change your title tag will depend on what CMS you are using. If you are using a CMS like WordPres, you can edit your page's title tag on each post/page using a SEO plugin like Yoast SEO.</p>
                        <p>The title tag of each page is the text in between the &lt;title&gt; &lt;/title&gt; tags.</p>
                        <p>Your title tag should follow the format below:</p>
                        <code>Main Keyword - Brand Name</code>
                        <a href="https://www.ninjareports.com/on-page-seo-guide/#Meta_Title_Tags" target="_blank">READ MORE</a>
                      </div>
                      @endif
                </div>
            </div>
                <hr>

            <div class="row">
                <div class="col-md-3">
                     <h6>
                @if($seo_audit_details['meta_length'] >= 120 && $seo_audit_details['meta_length'] <= 160)
                   <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @else
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                @endif
                Meta Description Tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The meta description is the text that Google often uses to display your webpage information in SERPs (search engine results pages)."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                        @if(empty($seo_audit_details['meta']))
                            <p style="color:#ccc">No Meta Description</p>
                    @else
                    <p>{{$seo_audit_details['meta']}}</p>
                    @endif
                   <p class="analysis-more-detail">Length: <strong>{{$seo_audit_details['meta_length']}} Characters</strong> (Recommended: 120-160 characters)</p>

                   @if($seo_audit_details['meta_length'] <= 120 || $seo_audit_details['meta_length'] > 160)
                              <button id="fix_meta_tag" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Fixing Meta Descrptions</h3>
                       <p> The description attribute provides a concise summary of what each web page is about.</p>

<p>Google doesn’t use meta descriptions as a ranking factor, but CTR is a ranking factor, and having a good meta description can give your web page a higher CTR.</p>

<p>The reason you can get a higher CTR is because Google highlights any text in the meta description matching the search term like below:</p>

<img src="https://www.ninjareports.com/wp-content/uploads/2020/09/ninja-reports-serps.png" />
<p>When we search for ‘Ninja Reports’, you can see that they highlight that keyword in our meta description on the SERPs.</p>

<p>This makes your listing stand out more, increasing the clicks and CTR to your web page.</p>

<p>Meta descriptions should be between 150-160 characters in length and should be unique to each web page.</p>
                          <a href="https://www.ninjareports.com/on-page-seo-guide/#Meta_Description_Tags" target="_blank">READ MORE</a>
                      </div>
                   @endif
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['canonical']))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Canonical Tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="These tags tell search engines what the correct URL of the page should be. "><i class="fa fa-info-circle" ></i></a></h6>
                    
                </div>
                <div class="col-md-9">
                    <p>{{$seo_audit_details['canonical'] ?? 'Your canonical tag is missing. Canonical tags are important because they tell search engines what the correct URL of the page should be.'}}</p>
                    @if(empty($seo_audit_details['canonical']))
                     <button id="fix_canonical_tag" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Fixing Canonical Tags</h3>
                          The canonical URL is a link element that tells search engines the preferred version (or URL) of a certain web page so you can avoid duplicate content issues.

  This meta tag should be added in the &gt;head&lt; section of your website pages.

  Canonical URLs can help search engines decipher the right web page to use for:

  E-commerce sites with product variants
  Blogs with paging
  Large websites with possible duplicate content issues
  Websites with both HTTP and HTTPS versions
  Websites that use query strings
  Example:

  <code><link rel="canonical" href="https://www.ninjareports.com/blog/on-page-seo-guide/"></code>
  <a href="https://www.ninjareports.com/on-page-seo-guide/#Canonical_Tags" target="_blank">READ MORE</a>
                      </div>
                    @endif
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6><i style="color:#999" class="fa fa-google" aria-hidden="true"></i> Google Preview</h6>
                </div>
                <div class="col-md-9">
                    <div class="google-preview">
                    <h5 style="color:#1a0dab;">{{$seo_audit_details['title']}}</h5>
                    <h6 style="font-size:14px;color:green;">{{ $seo_audit_details['url']}}</h6>
                    <p>{{ \Illuminate\Support\Str::limit( $seo_audit_details['meta'], 160, '...') }}</p>
                </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['favicon']))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Favicon <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="These tags tell search engines what the correct URL of the page should be. "><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['favicon']))
                       <img src="{{$seo_audit_details['favicon']}}" alt="" class="fav-icon"> <p class="fav">Your site is using favicon.</p>
                    @else
                        <p>Your site is missing it's favicon. Favicons are important for brand visibility and SEO.</p>
                         <button id="fix_favicon" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Adding a Favicon for SEO</h3>
                      
                      </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['mobile_friendly'] === 'MOBILE_FRIENDLY')
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @elseif(!empty($seo_audit_details['mobile_friendly']) && $seo_audit_details['mobile_friendly'] === 'NOT_MOBILE_FRIENDLY')
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    Mobile Friendly <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="With a mobile responsive website, you will rank better in the mobile index."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if($seo_audit_details['mobile_friendly'] === 'MOBILE_FRIENDLY')
                        <p>Your website is mobile-friendly.</p>
                    @elseif($seo_audit_details['mobile_friendly'] === 'NOT_MOBILE_FRIENDLY')
                        <p>Your website is not mobile responsive.</p>

                        <button id="fix_mobile_responsive" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Mobile Responsive & SEO</h3>
                          Google prefers responsive web design out of the handful of techniques and gives priority to websites that are responsive to all devices.
                          Make sure that all your pages pass the test and fix any errors that Google spits out.

Although it’s important to note that even though you pass this test, your website could still see high bounce rates if your responsive design is not good for the user.

You should test your website design on all devices too see the user experience your visitors are having.

This is the only way to reduce high bounce rates, increasing your rankings in Google.
                      </div>

                    @endif
                </div>
            </div>
        </div>
    </section>

    <section id='technical'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Technical</h2>
        <div class="Technical-section">

  <div class="row">
                <div class="col-md-3">
                    @php
                            $fcp = str_replace(' s', '', $seo_audit_details['fcp']) ;
                   @endphp
                    <h6>
                        @if($fcp < 2)
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    First Contentful Paint <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="FCP measures how long it takes the browser to render the first piece of DOM content after a user navigates to your page. "><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                   <p style="float:left;margin-right:15px">{{$seo_audit_details['fcp']}}</p>
                   @if($fcp <= 2)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:25%;background-color: green;">{{$seo_audit_details['fcp']}}</div></div>
                   @elseif($fcp > 2 && $fcp < 4)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                        aria-valuemin="0" aria-valuemax="100" style="width:50%;background-color: #ff6600;">{{$seo_audit_details['fcp']}}
                        </div></div>
                   @elseif($fcp > 4)
                        <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                        aria-valuemin="0" aria-valuemax="100" style="width:100%;background-color: #ff6600;">{{$seo_audit_details['fcp']}}
                        </div></div>
                   @endif
                       <p style="color:#999;clear:both;font-size:13px;margin:0">Recommended Score: Less than 2 seconds.</p>
                  </div>
                </div>
                <hr>
                  <div class="row">
                <div class="col-md-3">

                    @php
                            $lcp = str_replace(' s', '', $seo_audit_details['lcp']) ;
                   @endphp
                    <h6>
                        @if($lcp < 2.5)
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Last Contentful Paint <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The Largest Contentful Paint (LCP) metric reports the render time of the largest image or text block visible within the viewport."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                         <p style="float:left;margin-right:15px">{{$seo_audit_details['lcp']}}</p>
                   
                   @if($lcp <= 2.5)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$lcp}}" aria-valuemin="0" aria-valuemax="100" style="width:25%;background-color: green;">{{$seo_audit_details['lcp']}}</div></div>
                   @elseif($lcp > 2.5 && $lcp < 4)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$lcp}}" aria-valuemin="0" aria-valuemax="100" style="width:50%;background-color: #ff6600;">{{$seo_audit_details['lcp']}}</div></div>
                   @elseif($lcp > 4)
                        <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$lcp}}" aria-valuemin="0" aria-valuemax="100" style="width:100%;background-color: #ff0000;">{{$seo_audit_details['lcp']}}</div></div>
                   @endif
                   <p style="color:#999;clear:both;font-size:13px;margin:0">Recommended Score: Less than 2.5 seconds.</p>
                </div>
            </div>
                <hr>
                  <div class="row">
                <div class="col-md-3">
                    <h6>
                          @php
                            $cls = str_replace(' s', '', $seo_audit_details['cls']) ;
                   @endphp
                        @if($cls < .1)
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Cumulative Layout Shift <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="CLS measures the sum total of all individual layout shift scores for every unexpected layout shift that occurs during the entire lifespan of the page."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                             <p style="float:left;margin-right:15px">{{$seo_audit_details['cls']}}</p>
                 
                   @if($cls <= 0.1)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$cls}}" aria-valuemin="0" aria-valuemax="100" style="width:25%;background-color: green;">{{$seo_audit_details['cls']}}</div></div>
                   @elseif($cls > 0.1 && $cls <= 0.25)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$cls}}" aria-valuemin="0" aria-valuemax="100" style="width:50%;background-color: #ff6600;">{{$seo_audit_details['cls']}}</div></div>
                   @elseif($cls > 0.25)
                        <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$cls}}" aria-valuemin="0" aria-valuemax="100" style="width:100%;background-color: #ff0000;">{{$seo_audit_details['cls']}}</div></div>
                   @endif
                   <p style="color:#999;clear:both;font-size:13px;margin:0">Recommended Score: Less than 0.1.</p>
                </div>
            </div>
                <hr>

            <div class="row">
                <div class="col-md-3">
                    <h6>    
                        @if(!empty($seo_audit_details['schema_types']))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Schema Tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Schema tags help crawlers determine certain information about a website, business, product or video."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['schema_types']))
                        <p>Schema tags found on your page:</p>
                        <ul style="list-style-type:none">
                             @foreach(json_decode($seo_audit_details['schema_types']) as $type)
                              @if(is_array($type))
                                @foreach($type as $t)
<li><span style="margin-right: 2px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span> {{ $t }}</li>
                                @endforeach
                              @else
           <li><span style="margin-right: 2px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span> {{ $type }}</li>
                              @endif
                            
                             @endforeach
                        </ul>
                    @else
                        <p>Your page is missing schema tags.</p>

                         <button id="fix_schema" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Schema Tags & SEO</h3>
                          <p>Schema markup is a code snippet that you add in your HTML web pages to help search engines consume your data and display this data to searchers.</p>

                      <p>Here is an example of the results of including schema on your web page:</p>

                     <img src="https://ninjareports.com/wp-content/uploads/2020/09/structured-data-google.png" style="width: 648px;height: 110px;"/>

                     <p>As you can see from the listing, there are a few included rich snippets and they have included both a rating schema and a price schema that give more detail about the business or service.</p>

                     <p>Having this data included in your listing will increase the CTR (click-through-rate) of your web pages and boost your rankings.</p>
                      </div>


                    @endif
                    <!-- <h6>Organisation, Service</h6>
                    <p>No Schema Errors</p> -->
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if(empty($seo_audit_details['all_img_src']))
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                @else
                    @if(empty($seo_audit_details['img_miss_alt'] ))
                   <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                @endif
                Alt Tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Alt tags tell search engines what your images are about since they can only read text."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(empty($seo_audit_details['all_img_src']))
                        <p>NO Images Found</p>
                    @else
                        @if(empty($seo_audit_details['img_miss_alt']))
                            <p>No images are missing alt tags.</p>
                            @else
                            <p>{{$seo_audit_details['img_miss_alt']}} images are missing alt tags.({{$seo_audit_details['img_alt']}} images passed)</p>

                             <button id="fix_images" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Alts Tags and SEO</h3>
                          
                     <p></p>
                      </div>

                        @endif
                    
                        @if(!empty($seo_audit_details['img_without_alt']))
                            @foreach($seo_audit_details['img_without_alt'] as $alt)
                            <p style="margin-bottom: 0;">{{$alt}}</p>
                        @endforeach
                        @else
                        
                        @endif
                    @endif
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['url_seo_friendly']  == "SEO-Friendly")
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @elseif($seo_audit_details['url_seo_friendly'] == "Unfriendly SEO URLs")
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    SEO Friendly URL <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Keywords are not only important in your content but your URLs as well."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p>{{$seo_audit_details['url']}}  {{$seo_audit_details['url_seo_friendly']}} URL</p>
                      @if($seo_audit_details['url_seo_friendly'] == "Unfriendly SEO URLs")
                     <button id="fix_schema" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Fixing Unfriendly URLS</h3>
                         </div>
                     @endif
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if(!empty($seo_audit_details['iframe']))
                <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                @else
                <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @endif
                Flash/Iframes <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Iframes and flash are not good for SEO. Google can't crawl or read them so its good to keep them off your website."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
               @if(!empty($seo_audit_details['iframe']))
                    <p>You are using an Iframe on your page.</p>

                       <button id="fix_iframe" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Iframes & SEO</h3>
                         </div>

                @else
                  <p>No Iframes found on the page.</p>
                @endif
                </div>
            </div>
    </section>
    <section id='rankings'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Rankings</h2>
        <div class="keyword-section">
           
          <div class="row">
                <div class="col-md-3">
                        <h6>
                     @if($seo_audit_details['keyword_list'] != 'empty')
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Organic Keywords <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Organic keywords are the queries that your page ranks for in search engines."><i class="fa fa-info-circle" ></i></a></h6>

                </div>
                <div class="col-md-9">
                    @php
                   // dd(trim($seo_audit_details['keyword_list'],'"'));
                    @endphp

                @if(trim($seo_audit_details['keyword_list'],'"') == 'payme')
                 <div class="upgrade-tease"><a class="btn upgrade-btn btn-warning" href="/subscription">UPGRADE</a><img src="{{ asset('../images/keywords.png')}}" style="width:100%" class="responsive" alt="keywords"/></div>
                @else

                    @if($seo_audit_details['keyword_list']  === NULL || empty($seo_audit_details['keyword_list']))
                        <p>You aren't ranked for any organic keywords.</p>
                    @else
                     <table class="table image-table">
                        @php
                            $keyword_list = json_decode($seo_audit_details['keyword_list']);
                        @endphp
              
                      @foreach($keyword_list as $key => $val)
                        @if($key == 0)
                       <thead class="thead-light">
                        <tr>
                          @foreach($val as $heady)
                             <th>{{ $heady }}</th>
                             @endforeach
                        </tr>
                        </thead>
                        @endif
                        @if($key > 0)
                              <tr>
                                @foreach($val as $key => $bod)
                                @if($key == 0)
                                 <td><a href="https://www.google.com/search?q={{$bod}}" target="_blank">{{ $bod }} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                 @else
                                 <td>{{ $bod }}</td>
                                 @endif
                               @endforeach
                                </tr>

                        @endif
                          @endforeach
                          </table>
                
                    @endif
                @endif
                </div>
            </div>
        </div>
    </section>
    <section id='links'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Links</h2>
        <div class="links-section">
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if(!empty($seo_audit_details['internal_link'] ))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @else
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                @endif
                Internal Linking <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to all of your internal pages helps users and crawlers find all of your pages."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['internal_link']))
                    @php
                    $internal_links = json_decode($seo_audit_details['internal_link']);
              
                  $internal_links_count = count((array)$internal_links);
                    @endphp
                        <p>{{$internal_links_count}} Internal links were found on your page.</p>
                    @else
                        <p>Internal links were not found on your page.</p>

                           <button id="fix_internal_links" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Internal Linking & SEO</h3>
                         </div>

                    @endif
                </div>
            </div>
            <hr>
      
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($status404))
                        <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @endif
                    Broken Links <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to broken or nonexistent pages can hurt your own pages."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($status404))
                        <p>You have broken links on your page. Those links are sending users to a page that does not exist.</p>

                           <button id="fix_links" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Fixing Broken Links</h3>
                         </div>

                    @else
                        <p>No broken links found.</p>
                    @endif
                </div>
            </div>
            <hr>
                  <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>Referring Domains <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The number of domains that are linking to your URL."><i class="fa fa-info-circle" ></i></a></h6>

                </div>
                <div class="col-md-9">
                    @if($seo_audit_details['domains_num'] == 'payme')
                        <p>Upgrade to view backlink data. <a class="btn btn-sm btn-warning" href="/subscription">UPGRADE</a></p>
                    @else
                        @if($seo_audit_details['domains_num'] != 'empty')
                        <p>{{ number_format($seo_audit_details['domains_num']) }} domains are pointing to your page.</p>
                         @else
                        <p>You don't have any backlinks.</p>
                           <button id="fix_backlinks" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Backlinks & SEO</h3>
                         </div>
                        @endif
                    @endif

                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>Total Backlinks <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The total number of backlinks linking to your URL."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                     @if($seo_audit_details['urls_num'] === 'payme')
                        <p>Upgrade to view backlink data. <a class="btn btn-sm btn-warning" href="/subscription">UPGRADE TO VIEW</a></p>
                    @else
                        @if($seo_audit_details['urls_num'] != 'empty')
                        <p>{{ number_format($seo_audit_details['urls_num'] ) }} backlinks are pointing to your page.</p>
                         @else
                        <p>You don't have any backlinks.</p>
                          <button id="fix_backlinks" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Backlinks & SEO</h3>
                         </div>
                        @endif
                    @endif
                </div>
            </div>
            <hr>
             <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>Top Backlinks <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This shows the top backlinks pointing to your URL. The more quality links pointing to your page, the better it will rank in search engines."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(trim($seo_audit_details['semrush_links'],'"') == 'payme')
             
                   <div class="upgrade-tease"><a class="btn upgrade-btn btn-warning" href="/subscription">UPGRADE TO VIEW</a><img src="{{ asset('../images/backlinks.png')}}" style="width:100%" class="responsive" alt="backlinks"/></div>
                   @else
                        @if(trim($seo_audit_details['semrush_links'],'"') == 'empty')
                         <p>You don't have any backlinks.</p>
                        @else
                        <table class="table image-table">
                            @php
                            $semrush_links = json_decode($seo_audit_details['semrush_links']);
                            @endphp

                      @foreach($semrush_links as  $key => $val)
                    @if($key == 0)
                       <thead class="thead-light">
                        <tr>
                          @foreach($val as $heady)
                             <th>{{ $heady }}</th>
                             @endforeach
                        </tr>
                        </thead>
                          @endif
                           @if($key > 0)
                              <tr>
                                @foreach($val as $key => $bod)
                                @if($key == 0)
                                 <td><a href="{{ $bod }}" target="_blank">{{ $bod }} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                 @elseif($key == 4)
                                 <td> {{ $bod }}</td>
                                 @else
                                 <td>{{ $bod }}</td>
                                 @endif
                               @endforeach
                                </tr>

                              @endif
                                @endforeach
                            </table>
                             @endif
                   @endif

                </div>
            </div>
            <hr>
        </div>
    </section>
    <section id='Content'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Content</h2>
        <div class="Technical-section">
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['h1_tags'] > 0)
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    H1 tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Heading tags are the section titles of your content. They should be structured correctly. The h1 tag should be used once and include the main keyword."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                     {{$seo_audit_details['h1_tags']}} H1 tags were found on your page.
                    <ol>
                        @php
                            $h1 = json_decode($seo_audit_details['h1']);
                        @endphp
                    @foreach($h1 as $val)
                      <li>{{$val}}</li>
                    @endforeach
                    </ol>
                    @if($seo_audit_details['h1_tags'] == 0)
                      <button id="fix_h1" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>H1 Tags & SEO</h3>
                         </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['h2_tags'] > 0)
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    H2 tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Heading tags are the section titles of your content. They should be structured correctly. h2 tags are sub headings of the h1 tag."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    {{$seo_audit_details['h2_tags']}} H2 tags were found on your page.
                    <ol>
                         @php
                            $h2 = json_decode($seo_audit_details['h2']);
                        @endphp
                    @foreach($h2 as $val)
                        <li>{{$val}}</li>
                    @endforeach
                    </ol>

 @if($seo_audit_details['h2_tags'] == 0)
                      <button id="fix_h1" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>H2 Tags & SEO</h3>
                         </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['h3_tags'] > 0)
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    H3 tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Heading tags are the section titles of your content. They should be structured correctly."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    {{$seo_audit_details['h3_tags']}} H3 tags were found on your page.
                    <ol>
                         @php
                            $h3 = json_decode($seo_audit_details['h3']);
                        @endphp
                    @foreach($h3 as $val)
                      <li>{{$val}}</li>
                    @endforeach
                    </ol>

                     @if($seo_audit_details['h3_tags'] == 0)
                      <button id="fix_h1" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>H3 Tags & SEO</h3>
                         </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                     <h6>
                    @if(!empty($seo_audit_details['word_count']))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Keyword Density <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The keywords most used on your pages are likely the keywords you will rank for."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['word_count']))
                        <table class="table keyword-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Keyword</th>
                                    <th>SHOWN</th>
                                    <th>DENSITY</th>
                                    <th>TITLE</th>
                                    <th>DESC</th>
                                    <th>H1</th>
                                    <th>H2</th>
                                    <th>H3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                     $word_count = json_decode($seo_audit_details['word_count']);
                                ?>
                                    @foreach ($word_count as $key => $val)
                                        @if($i < 6 && strlen($key)>2)
                                            <tr>
                                                <th>{{$key}}</th>
                                                <td>{{$val}}</td>
                                                <td>{{number_format(($val / $seo_audit_details['word']) * 100, 1)}}%</td>
                                                @if(stripos($seo_audit_details['title'], $key) !== false)
                                                    <td style="color:green;"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                @else
                                                <td style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></td>
                                                @endif
                                                @if(stripos($seo_audit_details['meta'], $key) !== false)
                                                    <td style="color:green;"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                @else
                                                <td style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></td>
                                                @endif
                                                @if(stripos(implode("",$h1), $key) !== false)
                                                    <td style="color:green;"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                @else
                                                <td style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></td>
                                                @endif
                                                @if(stripos(implode("",$h2), $key) !== false)
                                                    <td style="color:green;"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                @else
                                                <td style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></td>
                                                @endif
                                                @if(stripos(implode("",$h3), $key) !== false)
                                                    <td style="color:green;"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                @else
                                                <td style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></td>
                                                @endif
                                            </tr>
                                            @endif
                                    <?php $i++;?>
                                    @endforeach
                            </tbody>
                        </table>
                        @else
                           <p></p>
                        @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['page_words'] < 300)
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @endif
                    Thin Content <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Google loves content. The more content that’s on your pages, the more keywords, and traffic."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                       <p> Page contains {{ number_format($seo_audit_details['page_words'],0) }} words.</p>

                        @if($seo_audit_details['page_words'] < 300)
                      <button id="fix_word_count" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Word Count & SEO</h3>
                         </div>
                    @endif

                  
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if($seo_audit_details['page_text_ratio'] > 10)
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @else
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                @endif
                Text-to-HTML ratio <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Search engines need text to know what a page is about. You should shoot for a 10% or higher text-to-HTML ratio."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <table class="table size-table">
                        <thead class="thead-light">
                            <tr>
                            <th>Page Size</th>
                            <th>Text Size</th>
                            <th>Text-to-HTML Ratio(%)</th>
                            <th>Recommended Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{number_format($seo_audit_details['page_size'],'1')}} Kb</td>
                                <td>{{number_format($seo_audit_details['page_words_size'] ,'1')}} kb</td>
                                <td>{{number_format($seo_audit_details['page_text_ratio'],'1')}} %</td>
                                <td>10%</td>
                            </tr>
                        </tbody>
                    </table>
                           @if($seo_audit_details['page_text_ratio'] < 10)
                      <button id="fix_https" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Text to HTML & SEO</h3>
                         </div>
                           @endif
                </div>
            </div>
        </div>
    </section>

    <section id='performance'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Performance</h2>
        <div class="Technical-section">
            <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>HTTP Request <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title=""><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @php
                        $http = json_decode($seo_audit_details['http']);
                    @endphp
                     {{$http[0]}}
                </div>
            </div>
            <hr>
             <div class="row">
                <div class="col-md-3">
                    @php    
                      $loadtime = str_replace(' s', '', $seo_audit_details['loadtime']);    
                     @endphp
                    <h6>
                        @if($loadtime > 4)
                         <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                @else
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @endif
Page Load Time <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title=""><i class="fa fa-info-circle" ></i></a></h6>
              </h6>
                </div>
                <div class="col-md-9">
                                <span style="float:left;margin-right:5px;">{{ $seo_audit_details['loadtime'] }}.</span>

                                              @if($loadtime <= 3)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$loadtime}}" aria-valuemin="0" aria-valuemax="100" style="width:25%;background-color: green;">{{$loadtime}}</div></div>
                   @elseif($loadtime > 3 && $loadtime < 5)
                             <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$loadtime}}" aria-valuemin="0" aria-valuemax="100" style="width:50%;background-color: #ff6600;">{{$loadtime}}</div></div>
                   @elseif($loadtime > 5)
                        <div class="progress" style="width: 200px;float: left;height:20px;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$loadtime}}" aria-valuemin="0" aria-valuemax="100" style="width:100%;background-color: #ff0000;">{{$loadtime}}</div></div>
                   @endif
                        <p class="analysis-more-detail" style="clear:both;">Recommended: < 4 seconds</p>
                           @if($loadtime > 4)
                         <button id="fix_js_min" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Pageload Speed & SEO</h3>
                         </div>
                         @endif

                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(empty($seo_audit_details['js_min']))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff6600;">  <i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    </span>JS Minification <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Minifying your files and code can help speed up your website, which is good for SEO."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(empty($seo_audit_details['js_min']))
                        <p>Your JS is minified.</p>
                    @else
                        <p>Your JS is not minified.</p>

                         <button id="fix_js_min" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>JS Minification & SEO</h3>
                         </div>

                        
                    @endif
                    @if(!empty($seo_audit_details['js_min_bytes']))
                        <p class="analysis-more-detail">Potential Savings: {{$seo_audit_details['js_min_bytes']}} KiB.</p>
                       @endif
                      
                </div>
            </div>
            <hr>
                       <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(empty($seo_audit_details['css_min']))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff6600;">  <i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    CSS Minification <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Minifying your files and code can help speed up your website, which is good for SEO."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(empty($seo_audit_details['css_min']))
                        <p>Your CSS is minified.</p>
                    @else
                        <p>Your CSS is not minified. </p>
                         <button id="fix_jcss_min" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>CSS Minification & SEO</h3>
                         </div>
                    @endif
                    @if(!empty($seo_audit_details['css_min_bytes']))
                        <p class="analysis-more-detail">Potential Savings: {{$seo_audit_details['css_min_bytes']}} KiB.</p>
                        @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(empty($seo_audit_details['all_img_src']))
                        <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @else
                        @if(!empty($seo_audit_details['img_data'] ))
                           <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                        @else
                           <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                        @endif
                    @endif   
                    Image Size Analysis <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Large images take longer to load, making your website slower. Optimize images for load time and size."><i class="fa fa-info-circle" ></i></a></h6>   
                </div>
                <div class="col-md-9">
                    @if(empty($seo_audit_details['all_img_src']))
                        <p>No Images Found</p>
                    @else
                        @if(is_array($seo_audit_details['img_data']))
                            <table class="table image-table">
                                <thead class="thead-light">
                                    <tr>
                                    <th>Image Location</th>
                                    <th>Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $img_data = json_decode($seo_audit_details['img_data']);
                                    @endphp
                                    @foreach($img_data as $key => $val)
                                        <tr>
                                            <td><a target="_blank" href="{{$key}}">{{substr($key,0,80)}}  <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
                                            <td>{{number_format($val,1)}} kb</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                </table>

                        @else
                            <p>Error Fetching Images.</p>
                        @endif
                    @endif    
                    <p class="analysis-more-detail">{{$seo_audit_details['responsive_images']}} </p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['gzip_compression']))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif

                    GZIP Compression <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="GZIP compression makes your website load faster."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                   @if(!empty($seo_audit_details['gzip_compression']))
                                <p>Your page is being compressed with GZIP.</p>
                   @else
                            <p>Your page is not being compressed with GZIP.</p>
                             <button id="fix_gzip" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>GZIP & SEO</h3>
                         </div>

                   @endif
                </div>
            </div>
             <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['cache']))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Browser Caching <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Caching can help increase your page speed and retain users longer."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                   @if($seo_audit_details['cache'] == 1)
                        <p>Your page has cache installed.</p>
                   @else
                        <p>Caching was not found on your page.</p>
                        <button id="fix_cache" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Caching & SEO</h3>
                         </div>
                   @endif
                </div>
            </div>
        </div>
    </section>
     <section id='security'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Security</h2>
        <div class="Technical-section">
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($seo_audit_details['page_https'] == "Page using HTTPS")
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    HTTPS <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having a secure website can help build trust and will help you rank better."><i class="fa fa-info-circle" ></i></a></h6> 
                </div>
                <div class="col-md-9">
                   @if(!empty($seo_audit_details['page_https']))
                        <p>Your page is using HTTPS SSL.</p>
                      @else
                       <button id="fix_https" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>HTTPS, Security & SEO</h3>
                         </div>
                    @endif    
                </div>
            </div>
             <hr>
                 <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['ssl_certificate']))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    SSL Certificate <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having a secure website can help build trust and will help you rank better."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                  @if(!empty($seo_audit_details['ssl_certificate']))
                        <p>An SSL certificate was found on your domain.</p>
                    @else
                        <p>An SSL certificate was not found on your domain.</p>
                          <button id="fix_cert" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>HTTPS, Security & SEO</h3>
                         </div>
                    @endif
                     
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['a_https']) && !empty($seo_audit_details['link_https']) && !empty($seo_audit_details['script_https']))
                    <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @endif
                     Mixed Content Issues <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to non-https files can create a mixed content error on your SSL connection, making your website insecure."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['a_https']) && !empty($seo_audit_details['link_https'])  && !empty($seo_audit_details['script_https']))
                        <p>Links pointing to non-HTTPS URLs found on your page.</p>
                          <button id="fix_non_https" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>HTTPS linking & SEO</h3>
                         </div>

                    @else
                        <p>Links pointing to non-HTTPS URLs not found on your page.</p>

                    @endif
                </div>
            </div>
        </div>
    </section>

    <section id='social'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Social</h2>
        <div class="Technical-section">
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['social_media_link']))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Links to Social Media <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to social media and including URLs to schema.org social media profiles can help search engines find your business."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                  @if(!empty($seo_audit_details['social_media_link']))
                    <p>Link to social media profiles found.</p>
                    @else
                      <p>Link to social media profiles not found.</p>
                      <button id="fix_https" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Social Media & SEO</h3>
                         </div>
                    @endif
                      

                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['social_schema']))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Social Schema Tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to social media and including URLs to schema.org social media profiles can help search engines find your business."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['social_schema']))
                        <p>Schema tags for social media profiles found.</p>
                    @else
                    <p>Schema tags for social media profiles not found.</p>
                    @endif
                </div>
            </div>

        </div>
    </section>
    
    <section id='other'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Other</h2>
        <div class="Technical-section">
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['robot']) && $seo_audit_details['robot'][0] !== '<!doctype')
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Robots.txt <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This file tells crawlers what they can and can’t crawl on your website."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['robot']) && $seo_audit_details['robot'][0] !== '<!doctype')
                        <p>A robots.txt file was found.</p>
                    @else
                        <p>We could not find a robots.txt file on your domain.</p>
                        <button id="fix_robots" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>Robots.txt & SEO</h3>
                         </div>
                    @endif

                </div>
            </div>
            <hr>
            <div class="clear">
                
            </div>
            <div class="sectionmap"> 
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($seo_audit_details['sitemap']))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    XML Sitemap <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having an XML sitemap can help search engines find your pages better."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($seo_audit_details['sitemap']))
                        <p>Your website has an XML sitemap.</p>
                    @else
                        <p>We cannot seem to find your website's sitemap.xml file.</p>

                        <button id="fix_sitemap" class="how-to-fix-button btn btn-sm btn-warning text-right">HOW TO FIX &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></button>
                      <div class="how-to-fix" style="display:none;">
                        <h3>XML Sitemap & SEO</h3>
                         </div>
                    @endif
                </div>
            </div>
            </div>
         </div>

    </section>
</div>
</div>

    <div class="modal" id="emailReport" tabindex="-1" role="dialog" aria-labelledby="emailReport" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
           <form id='seo_email_form'>
            <input type="hidden" id="report_url" name="report_url" value="{{ $seo_audit_details['url'] }}">
            <input type="hidden" id="report_id" name="report_id" value="{{ $seo_audit_details['id'] }}">
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
          <a class="btn-warning btn-md" href="{{route('email_seo_report')}}" id='send_email_report' style='padding:7px;text-decoration:none;'>SEND REPORT</a>
          </div>
 </form>
        </div>
      </div>
    </div>
@endsection