 

<div class="upgrade-container">
    <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding:0">
            <h5 id="url"><STRONG>Backlink Report:</STRONG> {{$url}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right">
        </div>
    </div>
    <div class="row">
      <div class="col-md-12" style="padding:0">
        <div class="alert alert-warning" role="alert">
 Please upgrade in order to use the backlinks analysis tool. Get access to your backlink data, historical backlinks, link toxicity and more. Try our <a href="/subscription">free trial</a> today and start ranking better in search engines.</div>
</div>
</div>

    <section id="backlink_analysis" class="backlink-page">
       <div class="row four-cols" style="margin-bottom:15px;">
            <div class="col-md-3">
                Total Backlinks <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Total backlinks is the total number of domains that point to your website."><i class="fa fa-info-circle" ></i></a>
                    <h2>8734</h2>
            </div>
            <div class="col-md-3">
                    Referring Domains <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Referring domains is the total number of domains that point to your website."><i class="fa fa-info-circle" ></i></a>
                    <h2>1281</h2>
            </div>
            <div class="col-md-3">
                        Link Toxicity <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Link toxicity score is calculated by the number of toxic domains pointing to your website."><i class="fa fa-info-circle" ></i></a>
                        <h2>18.6%</h2>
            </div>
            <div class="col-md-3">
                Link Power <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The link power is calculated by the number of backlinks and backlink quality."><i class="fa fa-info-circle" ></i></a>
                 <h2>70%</h2>
            </div>

       </div>
<div class="row">
<div class="col-md-3">
    <h5>Referring Backlinks History</h5>
    <a href="/subscription" class="btn btn-warning btn-lg">FREE TRIAL</a>
    <canvas id="backlinksChart" style=" width:100%;height:200px;"></canvas>
</div>
<div class="col-md-3">
    <h5>Referring Domains History</h5>
    <a href="/subscription" class="btn btn-warning btn-lg">FREE TRIAL</a>
    <canvas id="domainsChart" style=" width:100%;height:200px;"></canvas>
</div>
<div class="col-md-3">
    <h5>Link Profile</h5>
    <a href="/subscription" class="btn btn-warning btn-lg">FREE TRIAL</a>
     <canvas id="nofollowChart" style=" width:100%;height:200px;"></canvas>
    </div>
    <div class="col-md-3">
        <h5>Referring TLDs</h5>
        <a href="/subscription" class="btn btn-warning btn-lg">FREE TRIAL</a>
        <div class="referring-tlds">

                <div class="d-flex bd-highlight">
                <div class="p-2 flex-shrink-1 bd-highlight" style="font-size:13px;width:40px">.com</div>
                <div class="p-2 w-100 bd-highlight"> <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 70%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div></div>
                </div>
<div class="d-flex bd-highlight">
                <div class="p-2 flex-shrink-1 bd-highlight" style="font-size:13px;width:40px">.net</div>
                <div class="p-2 w-100 bd-highlight"> <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 30%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div></div>
                </div>
<div class="d-flex bd-highlight">
                <div class="p-2 flex-shrink-1 bd-highlight" style="font-size:13px;width:40px">.org</div>
                <div class="p-2 w-100 bd-highlight"> <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div></div>
                </div>

                   

        </div>
    </div>
</div>


  
<script>
    var months = ["Dec","Nov","Oct"]; 
    var domains = ["1017","1281","1171"];
    var backlinks = ["4734","6234","8734"]; 

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

    var ctx = document.getElementById("nofollowChart");
var myChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['FOLLOW', 'NOFOLLOW'],
    datasets: [{
      label: 'Follow/No Follow',
      data: [12,24],
      height:200,
      backgroundColor: [
        '#0E6EEA',
        '#ff6600',

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
                    display: true,
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
                    display: true,
                    text: 'Referring Domains by Month'
                }
            }
        });

</script>
       <br/>
       <hr>
       <div class="row" style="margin-top:25px;">
            <div class="col-md-12">
                <h3>Backlinks</h3>
                <a href="/subscription" class="btn btn-warning btn-lg">FREE TRIAL</a>
                            <table class="table backlinks-table">
                                <thead class="thead-light">
                                <tr>
                                 <th>Source URL</th>
                                 <th>Target URL</th>
                                 <th>Anchor</th>
                                 <th>A-Score</th>
                                        <th>External Links</th>
                                <th>Internal Links</th>
                                 <th>Last Seen</th>
                                 <th>First Seen</th>
                               <th>Nofollow</th>
                                    </tr>
                            </thead>
                      <tr>
                      		<td>https://www.ninjareports.com/best-seo-tools/</td>
                      		<td>https://www.ninjareports.com/best-seo/</td>
                      		<td>6</td>
                      		<td>66</td>
                      		<td>52</td>
                      		<td>15</td>
                      		<td>555</td>
                      		<td>5</td>
                      		<td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>6</td>
                          <td>6</td>
                          <td>83</td>
                          <td>5</td>
                          <td>555</td>
                          <td>165</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>6</td>
                          <td>66</td>
                          <td>534</td>
                          <td>55</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo/</td>
                          <td>https://www.ninjareports.com/testing/words/</td>
                          <td>6</td>
                          <td>6</td>
                          <td>525</td>
                          <td>5</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>6</td>
                          <td>632</td>
                          <td>5</td>
                          <td>5</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-tools/keywords/</td>
                          <td>https://www.ninjareports.com/about-us/</td>
                          <td>6</td>
                          <td>36</td>
                          <td>26</td>
                          <td>51</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/sign-up/</td>
                          <td>https://www.ninjareports.com/testing-url/length/</td>
                          <td>6</td>
                          <td>234</td>
                          <td>72</td>
                          <td>73</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>34</td>
                          <td>345</td>
                          <td>542</td>
                          <td>55</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/test/</td>
                          <td>662</td>
                          <td>326</td>
                          <td>125</td>
                          <td>732</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/testing/words/</td>
                          <td>6</td>
                          <td>6</td>
                          <td>28</td>
                          <td>93</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/testing/words/</td>
                          <td>6</td>
                          <td>6</td>
                          <td>93</td>
                          <td>5</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>26</td>
                          <td>683</td>
                          <td>45</td>
                          <td>35</td>
                          <td>555</td>
                          <td>05</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-audit-tools/</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>16</td>
                          <td>672</td>
                          <td>275</td>
                          <td>58</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com/best-seo-cool/</td>
                          <td>https://www.ninjareports.com/testing/words/</td>
                          <td>6</td>
                          <td>6</td>
                          <td>5</td>
                          <td>5</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                       <tr>
                          <td>https://www.ninjareports.com//</td>
                          <td>https://www.ninjareports.com/</td>
                          <td>6</td>
                          <td>6</td>
                          <td>5</td>
                          <td>5</td>
                          <td>555</td>
                          <td>5</td>
                          <td>true</td>
                      </tr>
                            </table>
            </div>
       </div>
    </section>
</div>