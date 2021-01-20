    <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left">
            <h5 id="url"><STRONG>SEO Analysis:</STRONG> {{$url}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right">
            <h5>{{ $time }}</h5>
        </div>
    </div>
    
    <section id="analysis" class="analysis-page">
        <div class="row Analysis-details" style="border:1px solid #f4f4f4;padding: 6px;">
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

    var text = '<?php echo $passed_score;?>%',
        textX = Math.round((width - ctx.measureText(text).width) / 2),
        textY = height / 2;

    ctx.fillText(text, textX, textY);
    ctx.save();
  }
});

                    var opp = <?php echo 100 - $passed_score;?>;
                          new Chart(document.getElementById("seo-chart"), {
                      type: 'doughnut',
                      data: {
                        labels: ["SEO Score"],
                        datasets: [
                          {
                            label: "",
                            backgroundColor: ["#0E6EEA", "#eee"],
                            data: [<?php echo $passed_score;?>,opp]
                          }
                        ]
                      },
                      options: {
                         cutoutPercentage: 70,
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
                <h3 style="text-align:center">{{$score_description ?? ''}}</h3>
            </div>
            <div class="col-md-4">
                <!-- <img src="images/desktop.jpg" style="height:140px;margin-top:15%;margin-left:5%;"> -->
                <div class="screen-container">
                    <div class="screen monitor">
                        <div class="content" id="image">
                        <!-- <img src="images/screenshot.png" style="width:100%" /> -->
                        
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
<!--                 <h5 class="meta"><a href="{{$url}}">{{$url}} <i class="fa fa-external-link" aria-hidden="true"></i></a></h5> -->
                
                <h6 class="pass">
                    <i class="fa fa-check" style="color:#008000" aria-hidden="true"></i> Passed
                </h6>
                <div class="progress" style="margin-bottom:5px;width: 70%;float: left;">
                        <div id="passed_progress" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="10"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{ $passed_score ?? ''}}%;background-color: #008000;">
                        </div>
                </div>
                <div class="clear"></div>
                 <h6 class="mca">
                    <i style="color:#ff0000" class="fa fa-exclamation-triangle" aria-hidden="true"></i> Errors
                </h6>
                <div class="progress" style="margin-bottom:5px;width: 70%;float: left;">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{$error_score ?? ''}}%;background-color: #ff0000;">
                        </div>
                </div>
                <div class="clear"></div>
                <h6 class="cta">
                    <i style="color:#ff6600" class="fa fa-exclamation-circle" aria-hidden="true"></i> Warnings
                </h6>
                <div class="progress" style="margin-bottom:5px;width: 70%;float: left;">
                        <div id="warning" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{$warning_score ?? ''}}%;background-color: #ff6600;">
                        </div>
                </div>
                <div class="clear"></div>
                <h6 class="pta">
                    <i style="color:#0E6EEA" class="fa fa-flag" aria-hidden="true"></i> Notices
                </h6>
                <div class="progress" style="margin-bottom:5px;width: 70%;float: left;">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="70"
                        aria-valuemin="0" aria-valuemax="100" style="width:{{$notice_score ?? ''}}%;background-color: #0E6EEA;">
                        </div>
                </div>
                <div class="clear"></div>
                <div class="row export-tools" style="display:none;">
                        <div class="col-md-6">
                            <a class="btn btn-success" href="#">EMAIL</a>
                        </div>
                          <div class="col-md-6">
                            <a class="btn btn-success" href="#">DOWNLOAD</a>
                        </div>
                </div>
                <p class="overview-desc" style="margin-top:15px;">On this scan, {{ $passed_score ?? ''}} factors passed with {{$error_score ?? ''}} errors, {{$warning_score ?? ''}} warnings and {{$notice_score ?? ''}} notices. Errors are the most important to fix in order to rank higher in search engines.</p>
            </div>
      
        </div>
    </section>

    <section id='header'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Header</h2>
        <div class="heading-section">

            <div class="row">
                <div class="col-md-3">
                    <h6>
                        @if($title_length > 30 && $title_length <= 60)
                            <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                             @elseif($title_length <= 30 || $title_length > 60)
                                 <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                        @endif
                        Title Tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The title tag is the text that Google often uses to display your website link in SERPs (search engine results pages)."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p>{{$title}}</p>
                    <p>Length: <strong>{{$title_length}} Characters</strong> (Recommended: 60 characters)</p>
                </div>
            </div>
                <hr>

            <div class="row">
                <div class="col-md-3">
                     <h6>
                @if($meta_length >= 120 && $meta_length <= 160)
                   <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @else
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                @endif
                Meta Description Tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The meta description is the text that Google often uses to display your webpage information in SERPs (search engine results pages)."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p>{{$meta}}</p>
                    <p>Length: <strong>{{$meta_length}} Characters</strong> (Recommended: 120-160 characters)</p>
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($canonical))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Canonical Tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="These tags tell search engines what the correct URL of the page should be. "><i class="fa fa-info-circle" ></i></a></h6>
                    
                </div>
                <div class="col-md-9">
                    <p>{{$canonical ?? 'Your canonical tag is missing. Canonical tags are important because they tell search engines what the correct URL of the page should be.'}}</p>
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6><i style="color:#999" class="fa fa-google" aria-hidden="true"></i> Google Preview</h6>
                </div>
                <div class="col-md-9">
                    <div class="google-preview">
                    <h5 style="color:#1a0dab;">{{$title}}</h5>
                    <h6 style="font-size:14px;color:green;">{{$url}}</h6>
                    <p>{{ \Illuminate\Support\Str::limit($meta, 160, '...') }}</p>
                </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($favicon))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Favicon <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="These tags tell search engines what the correct URL of the page should be. "><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9 favicon">
                    @if(!empty($favicon))
                       <img src="{{$favicon}}" alt="" class="fav-icon"> <p class="fav">Your site using favicon.</p>
                    @else
                        <p>Your site is missing it's favicon. Favicons are important for brand visability and SEO.</p>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($mobile_friendly === 'MOBILE_FRIENDLY')
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @elseif(!empty($mobile_friendly) && $mobile_friendly === 'NOT_MOBILE_FRIENDLY')
                    <h6><span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    Mobile Friendly <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="With a mobile responsive website, you will rank better in the mobile index."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if($mobile_friendly === 'MOBILE_FRIENDLY')
                        <p>Your website is mobile-friendly.</p>
                    @elseif($mobile_friendly === 'NOT_MOBILE_FRIENDLY')
                        <p>Your website is not mobile responsive.</p>
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
                    <h6>
                        @if(!empty($schema))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Schema Tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Schema tags help crawlers determine certain information about a website, business, product or video."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($schema))
                        <p>Schema tags found on your page:</p>
                        <ul style="list-style-type:none">
                             @foreach($schema_types as $type)
                             <li><span style="margin-right: 2px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span> {{ $type }}</li>
                             @endforeach
                        </ul>
                    @else
                        <p>Your page is missing schema tags.</p>
                    @endif
                    <!-- <h6>Organisation, Service</h6>
                    <p>No Schema Errors</p> -->
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if(empty($all_img_src))
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                @else
                    @if(empty($img_miss_alt))
                   <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                @endif
                Alt Tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Alt tags tell search engines what your images are about since they can only read text."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(empty($all_img_src))
                        <p>NO Images Found</p>
                    @else
                        @if(empty($img_miss_alt))
                            <p>No images are missing alt tags.</p>
                            @else
                            <p>{{$img_miss_alt}} images are missing alt tags.({{$img_alt}} images passed)</p>
                        @endif
                    
                        @if(!empty($img_without_alt))
                            @foreach($img_without_alt as $alt)
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
                    @if($url_seo_friendly == "SEO-Friendly")
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @elseif($url_seo_friendly == "Unfriendly SEO URLs")
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    SEO Friendly URL <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Keywords are not only important in your content but your URLs as well."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p>{{$url}}  {{$url_seo_friendly}} URL</p>
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if(!empty($iframe))
                <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                @else
                <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @endif
                Flash/Iframes <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Iframes and flash are not good for SEO. Google can't crawl or read them so its good to keep them off your website."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
               @if(!empty($iframe))
                    <p>You are using an Iframe on your page.</p>
                @else
                  <p>No Iframes found on the page.</p>
                @endif
                </div>
            </div>
            </div>
        </div>
    </section>
    <section id='rankings'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Rankings</h2>
        <div class="keyword-section">
           
          <div class="row">
                <div class="col-md-3">
                        <h6>
                     @if($keyword_list != 'empty')
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Organic Keywords <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Organic keywords are the queries that your page ranks for in search engines."><i class="fa fa-info-circle" ></i></a></h6>

                </div>
                <div class="col-md-9">
                @if($keyword_list == 'payme')
                 <div class="upgrade-tease"><a class="btn upgrade-btn btn-warning" href="/subscription">FREE TRIAL</a><img src="{{ asset('../images/keywords.png')}}" style="width:100%" class="responsive" alt="keywords"/></div>
                @else

                    @if($keyword_list == 'empty')
                        <p>You aren't ranked for any organic keywords.</p>
                     @endif
                     @if($keyword_list != 'empty')
                     <table class="table image-table">
                      @foreach($keyword_list as  $key => $val)
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
                @if(!empty($internal_link))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                @else
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                @endif
                Internal Linking <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to all of your internal pages helps users and crawlers find all of your pages."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($internal_link))
                        <p>Internal links were found on your page.</p>
                    @else
                        <p>Internal links were not found on your page.</p>
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
                    @if($domains_num == 'payme')
                        <p>Upgrade to view backlink data. <a class="btn btn-sm btn-warning" href="/subscription">FREE TRIAL</a></p>
                    @else
                        @if($domains_num != 'empty')
                        <p>{{ number_format($domains_num) }} domains are pointing to your page.</p>
                         @else
                        <p>You don't have any backlinks.</p>
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
                     @if($urls_num == 'payme')
                        <p>Upgrade to view backlink data. <a class="btn btn-sm btn-warning" href="/subscription">FREE TRIAL</a></p>
                    @else
                        @if($urls_num != 'empty')
                        <p>{{ number_format($urls_num) }} backlinks are pointing to your page.</p>
                         @else
                        <p>You don't have any backlinks.</p>
                        @endif
                    @endif
                </div>
            </div>
            <hr>
             <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>Top 5 Backlinks <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This shows the top backlinks pointing to your URL. The more quality links pointing to your page, the better it will rank in search engines."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                   @if($semrush_links == 'payme')    
                   <div class="upgrade-tease"><a class="btn upgrade-btn btn-warning" href="/subscription">FREE TRIAL</a><img src="{{ asset('../images/backlinks.png')}}" style="width:100%" class="responsive" alt="backlinks"/></div>
                   @else
                        @if($semrush_links == 'empty')
                         <p>You don't have any backlinks.</p>
                        @else
                        <table class="table image-table">
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
                    @if($h1_tags > 0)
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    H1 tag <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Heading tags are the section titles of your content. They should be structured correctly. The h1 tag should be used once and include the main keyword."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                     {{$h1_tags}} H1 tags were found on your page.
                    <ol>
                    @foreach($h1 as $val)
                      <li>{{$val}}</li>
                    @endforeach
                    </ol>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($h2_tags > 0)
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    H2 tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Heading tags are the section titles of your content. They should be structured correctly. h2 tags are sub headings of the h1 tag."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    {{$h2_tags}} H2 tags were found on your page.
                    <ol>
                    @foreach($h2 as $val)
                        <li>{{$val}}</li>
                    @endforeach
                    </ol>

                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if($h3_tags > 0)
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: #ff0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    H3 tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Heading tags are the section titles of your content. They should be structured correctly."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    {{$h3_tags}} H3 tags were found on your page.
                    <ol>
                    @foreach($h3 as $val)
                      <li>{{$val}}</li>
                    @endforeach
                    </ol>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                     <h6>
                    @if(!empty($word_count))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Keyword Density <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The keywords most used on your pages are likely the keywords you will rank for."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($word_count))
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
                                <?php $i = 1;?>
                                    @foreach ($word_count as $key => $val)
                                        @if($i < 6 && strlen($key)>2)
                                            <tr>
                                                <th>{{$key}}</th>
                                                <td>{{$val}}</td>
                                                <td>{{number_format(($val / $word) * 100, 1)}}%</td>
                                                @if(stripos($title, $key) !== false)
                                                    <td style="color:green;"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                @else
                                                <td style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></td>
                                                @endif
                                                @if(stripos($meta, $key) !== false)
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
                    @if($page_words < 300)
                    <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @endif
                    Thin Content <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Google loves content. The more content that’s on your pages, the more keywords, and traffic."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                        Page contains {{ number_format($page_words,0) }} words.
                    </p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                @if($page_text_ratio > 10)
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
                                <td>{{number_format($page_size,'1')}} Kb</td>
                                <td>{{number_format($page_words_size,'1')}} kb</td>
                                <td>{{number_format($page_text_ratio,'1')}} %</td>
                                <td>10%</td>
                            </tr>
                        </tbody>
                    </table>
                   
                </div>
            </div>
        </div>
    </section>

    <section id='performance'>
        <h2 style="margin-bottom: 30px;margin-top: 30px;">Performance</h2>
        <div class="Technical-section">
            <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>HTTP Request <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having a secure website can help build trust and will help you rank better."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @foreach($http as $https)
                        {{$https}} <br>
                    @endforeach
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6><span style="margin-right: 9px;color: green;" id="img_color"><i class="fa fa-check" aria-hidden="true" id="img_err"></i></span>JS/CSS Minification <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Minifying your files and code can help speed up your website, which is good for SEO."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p id='css_minified'></p>
                    <p id='js_minified'></p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(empty($all_img_src))
                        <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @else
                        @if(!empty($img_data))
                           <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                        @else
                           <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                        @endif
                    @endif   
                    Image Size Analysis <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Large images take longer to load, making your website slower. Optimize images for load time and size."><i class="fa fa-info-circle" ></i></a></h6>   
                </div>
                <div class="col-md-9">
                    @if(empty($all_img_src))
                        <p>No Images Found</p>
                    @else
                        @if(!empty($img_data))
                            <table class="table image-table">
                                <thead class="thead-light">
                                    <tr>
                                    <th>Image Location</th>
                                    <th>Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($img_data as  $key => $val)
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
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    
                    <h6><span style="margin-right: 9px;color: green;" id="gzip_color"><i class="fa fa-check" aria-hidden="true" id="img_gzip"></i></span>GZIP Compression <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="GZIP compression makes your website load faster."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p id='gzip_compression'></p>
                </div>
            </div>
             <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($cache))
                       <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    Browser Caching <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Caching can help increase your page speed and retain users longer."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                   @if(!empty($cache))
                        <p>Your page has cached installed.</p>
                   @else
                        <p>Cacheing was not found on your page.</p>
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
                    @if($page_https == "Page using HTTPS")
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    HTTPS <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having a secure website can help build trust and will help you rank better."><i class="fa fa-info-circle" ></i></a></h6> 
                </div>
                <div class="col-md-9">
                   @if(!empty($page_https))
                        <p>Your page is using HTTPS SSL.</p><br>
                    @endif    
                    
                </div>
            </div>
             <hr>
                 <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($ssl_certificate))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @endif
                    SSL Certificate <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having a secure website can help build trust and will help you rank better."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                  @if(!empty($ssl_certificate))
                        <p>An SSL certificate was found on your domain.</p>
                    @else
                        <p>An SSL certificate was not found on your domain.</p>
                    @endif
                     
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($a_https) && !empty($link_https) && !empty($script_https))
                    <span style="margin-right: 9px;color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @endif
                     Mixed Content Issues <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to non-https files can create a mixed content error on your SSL connection, making your website insecure."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($a_https) && !empty($link_https)  && !empty($script_https))
                        <p>Links pointing to non-HTTPS URLs found on your page.</p>
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
                    @if(!empty($social_media_link))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Links to Social Media <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to social media and including URLs to schema.org social media profiles can help search engines find your business."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    <p>{{$social_media_link ?? 'Link to social media profiles not found.'}}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <h6>
                    @if(!empty($social_schema))
                    <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                    <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Social Schema Tags <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Linking to social media and including URLs to schema.org social media profiles can help search engines find your business."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($social_schema))
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
                    @if(!empty($robot) && $robot[0] !== '<!doctype')
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                       <span style="margin-right: 9px;color:#0E6EEA;"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    @endif
                    Robots.txt <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This file tells crawlers what they can and can’t crawl on your website."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($robot) && $robot[0] !== '<!doctype')
                        <p>A robots.txt file was found.</p>
                    @else
                        <p>We could not find a robots.txt file on your domain.</p>
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
                    @if(!empty($sitemap))
                        <span style="margin-right: 9px;color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>
                    @else
                        <span style="margin-right: 9px;color: #ff6600;"><i class="fa fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    @endif
                    XML Sitemap <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Having an XML sitemap can help search engines find your pages better."><i class="fa fa-info-circle" ></i></a></h6>
                </div>
                <div class="col-md-9">
                    @if(!empty($sitemap))
                        <p>Your website has an XML sitemap.</p>
                    @else
                        <p>We cannot seem to find your website's sitemap.xml file.</p>
                    @endif
                </div>
            </div>
            </div>
         </div>

    </section>
