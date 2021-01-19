 

<div class="upgrade-container">
  
      <div class="row audit-text pt-3 pb-3">
        <div class="col-md-7 text-left" style="padding-left:0">
            <h5 id="url"><STRONG>Keyword Report:</STRONG> {{$url}}</h5>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-4 text-right" style="padding-right:0">
            <h5>Last Crawled: {{ $time }}</h5>
        </div>
    </div>
<div class="row">
      <div class="col-md-12" style="padding:0">
        <div class="alert alert-warning" role="alert">
 Please upgrade in order to use the rankings analysis tool. Get access to your keyword data, competitor info, keyword trends and more. Try our <a href="/subscription">free trial</a> today and start ranking better in search engines.</div>
</div>
</div>
    <section id="rankings" class="rankings-page">
       <div class="row cols" style="margin-bottom:15px;">
            <div class="col-md-2">
                Total Keywords <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This is the total number of organic keywords your URL is ranked for in search engines."><i class="fa fa-info-circle" ></i></a>
                    <h2>1,623</h2>
            </div>
            <div class="col-md-2">
                    Average Position <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="The average position is the average position each of your keywords are in search engines."><i class="fa fa-info-circle" ></i></a>
                    <h2>7.8</h2>
            </div>
            <div class="col-md-2">
                         Traffic Value <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Traffic is the moentary value of the current traffic coming to your website."><i class="fa fa-info-circle" ></i></a>
                        <h2>$423</h2>
            </div>
            <div class="col-md-2">
                Traffic Potential <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="Traffic potential refers to the amount of volume you could potentially get from your ranked keywords."><i class="fa fa-info-circle" ></i></a>
                 <h2>8,724</h2>
            </div>
                 <div class="col-md-2">
                   Traffic Share <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>10%</h2>
            </div>
              <div class="col-md-2">
                Top Keyword <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This keyword brings in the most amount of organic traffic to your website."><i class="fa fa-info-circle" ></i></a>
                 <h2>top keyword</h2>
                  
            </div>

       </div>

<div class="row sub-cols">
    <div class="col-md-4">
    <h5>Keyword Trends (Last 12 Mo.)</h5>
    <script>
        var trend_array = ['1','3','4','5','7','8','9','14','16','20','21','25','17'];
        var months = ['1','2','3','4','5','6','7','8','9','10','11','12'];

    var backlinksData = {
        labels: months,
        datasets: [{
            label: 'Avg. Trends',
            borderColor: "#0e6eea",
            fill: false,
            data: trend_array
        }]
    };

        var ctx = document.getElementById("trendChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: backlinksData,

             labels: months,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(5, 50, 92)',
                    }
                },
                responsive: true,
                title: {
                    display: false,
                    text: 'Referring URLs by Month'
                }
            }
        });

    </script>
    <canvas id="trendChart" style=" width:100%;height:200px;"></canvas>
  </div>
<div class="col-md-2">
     <h5>Position Distribution <a href="#" class="seotip" data-toggle="tooltip" data-placement="top" title="This shows you how many keywords your URL is ranked for in each of the ranges below."><i class="fa fa-info-circle" ></i></a></h5>
    <ul class="pos-dist">

           <li><i style="color:green" class="fa fa-circle" aria-hidden="true"></i> 1-10: 15</li> 
           <li><i style="color:lightgreen" class="fa fa-circle" aria-hidden="true"></i> 10-25: 38</li> 
           <li><i style="color:lightyellow" class="fa fa-circle" aria-hidden="true"></i> 25-50: 87</li> 
           <li><i style="color:orange" class="fa fa-circle" aria-hidden="true"></i> 50-75: 124</li> 
           <li><i style="color:red" class="fa fa-circle" aria-hidden="true"></i> 75-100: 16</li> 
           <li><i style="color:darkred" class="fa fa-circle" aria-hidden="true"></i> 100+:  12</li> 

</ul>
</div>
  <div class="col-md-2">
    <h5>Competitors</h5>

 
     <ul class="competitor-list">
                <li class="competitor-info"><i class="circle-num">1</i> somewebsite.com <i class="fa fa-caret-down" aria-hidden="true"></i></li>
             <li class="competitor-info"><i class="circle-num">2</i> anotherwebsite.com <i class="fa fa-caret-down" aria-hidden="true"></i></li>
             <li class="competitor-info"><i class="circle-num">3</i> competitor.com <i class="fa fa-caret-down" aria-hidden="true"></i></li>
             <li class="competitor-info"><i class="circle-num">4</i> businessname.com <i class="fa fa-caret-down" aria-hidden="true"></i></li>
             <li class="competitor-info"><i class="circle-num">5</i> testwebsite.com <i class="fa fa-caret-down" aria-hidden="true"></i></li>
                     
        
            </ul>


  </div>
    <div class="col-md-2">
         <h5>Serp Features</h5>
         <ul class="pos-dist" style="list-style-type:none;margin-left:5px;padding-left:5px;">

            <li><i style="margin-right:5px;" class="fa fa-question-circle-o"></i> FAQ: 17</li>
            <li><i style="margin-right:5px;" class="fa fa-link"></i> Site link: 15</li>
            <li><i style="margin-right:5px;" class="fa map-o"></i> Local: 12</li>
            <li><i style="margin-right:5px;" class="fa fa-question-circle-o"></i> News: 11</li>
            <li><i style="margin-right:5px;" class="fa fa-question-circle-o"></i> Video: 7</li>


        
     </ul>
    </div>
<div class="col-md-2">
    <script>

    var serpData = {
        labels: ['FAQ','Image Pack','Site link','Video'],
        datasets: [{
          label: 'SERP Features',
          data: ['5','2','17','4'],
          backgroundColor: [
            '#0E6EEA',
            '#2776dc',
            '#3e7ccc',
            '#4374b4',
            '#1058b5',
            '#2c5994',
            '#24497a',
            '#1b3b64',
            '#458dea',
            '#6ba4ee',
          ],
          hoverOffset: 4
        }]
    };

        var ctx = document.getElementById("serpChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'doughnut',
            data: serpData,
            options: {
                legend: {
                    labels: {
                         boxWidth: 5
                     },
            display: true,
            position: 'top',
        },
                elements: {
                },
                responsive: true,
            }
        });

    </script>
    <canvas id="serpChart" style=" width:100%;height:200px;"></canvas>
</div>
</div>
<div class="row">
<div class="col-md-4">
</div>
</div>
       <div class="row" style="margin-top:25px;">
            <div class="col-md-12">
                <h3>Organic Keywords</h3>
                            <table class="table backlinks-table">

                                <thead class="thead-light">
                                <tr>
                                 <th>Keyword</th>
                                 <th>KD</th>
                                 <th>Position</th>
                                 <th>Search Volume</th>
                                 <th>CPC</th>
                                <th>Comp.</th>
                                <th>Traffic %</th>
                                 <th>Traffic Cost</th>
                                 <th>Results</th>
                                 <th>SERP Features</th>
                                 <th>Keyword Trend</th>
                               
                                    </tr>
                            </thead>
                    
                            <tr>
                                <td><a href="#">test keyword</a></td>
                                <td>17</td>
                                <td>2</td>
                                <td>12,002</td>
                                <td>$3.00</td>
                                <td>0.14%</td>
                                <td>4%</td>
                                <td>$126</td>
                                <td>722,737</td>
                                <td><i class=""></i><i class="fa fa-shopping-basket"></i><i class="fa fa-map-o"></i><i class="fa fa-newspaper-o"></i></td>
                                <td></td>   
                            </tr>
                             <tr>
                                <td><a href="#">long tail keyword</a></td>
                                <td>173</td>
                                <td>21</td>
                                <td>123,002</td>
                                <td>$7.00</td>
                                <td>0.01%</td>
                                <td>2%</td>
                                <td>$789</td>
                                <td>1,722,737</td>
                                <td><i class=""></i><i class="fa fa-shopping-basket"></i><i class="fa fa-map-o"></i><i class="fa fa-newspaper-o"></i></td>
                                <td></td>   
                            </tr>
                             <tr>
                                <td><a href="#">another keyword keyword</a></td>
                                <td>62</td>
                                <td>12</td>
                                <td>2,002</td>
                                <td>$2.00</td>
                                <td>0.34%</td>
                                <td>14%</td>
                                <td>$826</td>
                                <td>722,737</td>
                                <td><i class=""></i><i class="fa fa-shopping-basket"></i><i class="fa fa-map-o"></i><i class="fa fa-newspaper-o"></i></td>
                                <td></td>   
                            </tr>
                             <tr>
                                <td><a href="#">upgrade now keyword</a></td>
                                <td>68</td>
                                <td>19</td>
                                <td>2,002</td>
                                <td>$0.00</td>
                                <td>1.34%</td>
                                <td>9%</td>
                                <td>$3,267</td>
                                <td>129,345</td>
                                <td><i class="fa fa-link"></i><i class=""></i><i class="fa fa-shopping-basket"></i><i class="fa fa-map-o"></i><i class="fa fa-newspaper-o"></i></td>
                                <td></td>   
                            </tr>
                             <tr>
                                <td><a href="#">test keyword</a></td>
                                <td>45</td>
                                <td>29</td>
                                <td>12,002</td>
                                <td>$3.00</td>
                                <td>0.14%</td>
                                <td>4%</td>
                                <td>$126</td>
                                <td>722,737</td>
                                <td><i class="fa fa-picture-o"></i><i class="fa fa-shopping-basket"></i><i class="fa fa-map-o"></i><i class="fa fa-link"></i></td>
                                <td></td>   
                            </tr>
                             <tr>
                                <td><a href="#">test keyword</a></td>
                                <td>27</td>
                                <td>45</td>
                                <td>12,002</td>
                                <td>$3.00</td>
                                <td>0.14%</td>
                                <td>4%</td>
                                <td>$126</td>
                                <td>722,737</td>
                                <td><i class="fa fa-quote-right"></i><i class="fa fa-shopping-basket"></i><i class="fa fa-lightbulb-o"></i><i class="fa fa-newspaper-o"></i><i class="fa fa-link"></i></td>
                                <td></td>   
                            </tr>

                        

                    
                            </table>
            </div>
       </div>
    </section>

</div>