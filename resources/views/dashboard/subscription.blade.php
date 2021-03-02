@extends('layouts.master')
@section('title', 'Ninja Reports Pricing')
@section('content')
<div class="col-md-10 pricing-page">
    @if(!empty($_GET['success']))
        <div class="alert alert-success mt-4">
            <?php echo base64_decode($_GET['success']); ?>
        </div>
    @endif  
<h1 style="width:29%;margin:auto;text-align:center;margin-top: 50px;">Ninja Reports Pricing</h1>
        <h5 style="width:40%;text-align:center;margin:auto;margin-top:2%;">Ninja Reports is a revolutionary SEO audit & analysis tool for agencies, webmasters, and SEOs. Browse our affordable plans below!</h5>
    <div class="container">
            <div class="row three-cols pricing-boxes" style="margin-top:10%;">
                <div class="col-md-4 text-center Webmaster-col">
                    <h3>Webmaster</h3>
                    <p>Affordable Package for webmasters or small businesses wanting to enhance their SEO.</p>
                    <strike style="color:#ff0000">$29</strike>               
                    <h5><strong>$19</strong></h5>
                    <h6>per month</h6>
                         @if($status == 1 && $product->id == env('WEBMASTER_ID'))
                       <h4><a href="javascript:void(0)" class="btn btn-secondary btn-lg" style="pointer-events: none;">CURRENT PLAN</a></h4>
                         @elseif($status == 1)
                        <h4><a href="{{route('payment',['id' => 1])}}" class="btn btn-primary btn-lg">UPGRADE</a></h4>
                         @endif
                        @if($status == 0)
                       <h4><a href="{{route('payment',['id' => 1])}}" class="btn btn-primary btn-lg">GET ACCESS</a></h4>
                         @endif
                    <a href="#detailed">Learn More</a>
                </div>
                <div class="col-md-4 text-center Business-col" style="padding-top: 0px;">
                    <h6 style="color: white;background-color: #ff6600;position: relative;top:-27px;padding:3px;">MOST POPULAR</h6>
                    <h3>Business</h3>
                    <p>Our most popular package, perfect for businesses with multiple sites or clients looking to grow their traffic.</p>
                    <strike style="color:#ff0000">$49</strike>
                    <h5><strong>$29</strong></h5>
                    <h6>per month</h6>
                        @if($status == 1 && $product->id == env('BUSINESS_ID'))
                       <h4><a href="javascript:void(0)" class="btn btn-secondary btn-lg" style="pointer-events: none;">CURRENT PLAN</a></h4>
                         @elseif($status == 1)
                        <h4><a href="{{route('payment',['id' => 2])}}" class="btn btn-warning btn-lg">UPGRADE</a></h4>
                         @endif
                        @if($status == 0)
                       <h4><a href="{{route('payment',['id' => 2])}}" class="btn btn-warning btn-lg">GET ACCESS</a></h4>
                         @endif

                    <a href="#detailed">Learn More</a>
                </div>
                <div class="col-md-4 text-center Agency-col">
                    <h3>Agency</h3>
                    <p>For agencies looking to grow their client’s traffic and keep on-page SEO health 100%.
</p>
<strike style="color:#ff0000">$99</strike>
                    <h5><strong>$59</strong></h5>
                    <h6>per month</h6>
                    @if($status == 1 && $product->id == env('AGENCY_ID'))
                       <h4><a href="javascript:void(0)" class="btn btn-secondary btn-lg" style="pointer-events: none;">CURRENT PLAN</a></h4>
                         @elseif($status == 1)
                        <h4><a href="{{route('payment',['id' => 3])}}" class="btn btn-primary btn-lg">UPGRADE</a></h4>
                         @endif
                        @if($status == 0)
                       <h4><a href="{{route('payment',['id' => 3])}}" class="btn btn-primary btn-lg">GET ACCESS</a></h4>
                         @endif
                    <a href="#detailed">Learn More</a>
                </div>
            </div>
    </div>
    <div class="container">
        <div class="row three-cols">
    <a name="detailed"></a>
    <div class="col-md-11" style="margin-top:25px;"><h4>Pricing Details</h4>
<table class="table detailed-pricing">
<tbody>
<tr>
<th></th>
<th>Free</th>
<th>Webmaster</th>
<th>Business</th>
<th>Agency</th>
</tr>
<tr>
<td>URL Analysis Reports</td>
<td>1/day</td>
<td>100/mo</td>
<td>UNLIMITED</td>
<td>UNLIMITED</td>
</tr>
<tr>
<td>Site Audit Reports</td>
<td>0</td>
<td>25/mo</td>
<td>100/mo</td>
<td>250/mo</td>
</tr>
<tr>
<td>Backlink Reports</td>
<td>0</td>
<td>5/mo</td>
<td>15/mo</td>
<td>100/mo</td>
</tr>
<tr>
<td>Ranking/Keyword Reports</td>
<td>0</td>
<td>10/mo</td>
<td>25/mo</td>
<td>100/mo</td>
</tr>
<tr>
<td>Traffic Reports</td>
<td>0</td>
<td>15/mo</td>
<td>30/mo</td>
<td>50/mo</td>
</tr>
<tr>
<td>White Label Reports</td>
<td>No</td>
<td>No</td>
<td>No</td>
<td>Yes</td>
</tr>
<tr>
    <tr style="color:green;font-weight:bold;">
<td>Price</td>
<td>Free</td>
<td><strike style="color:#ff0000">$29</strike> $19/mo</td>
<td><strike style="color:#ff0000">$49</strike> $29/mo</td>
<td><strike style="color:#ff0000">$99</strike> $59/mo</td>
</tr>
<tr>
<td></td>
<td></td>
<td>
 @if($status == 1 && $product->id == env('WEBMASTER_ID'))
                       <h4><a href="javascript:void(0)" class="btn btn-secondary btn-sm" style="pointer-events: none;">CURRENT PLAN</a></h4>
                         @elseif($status == 1)
                        <h4><a href="{{route('payment',['id' => 1])}}" class="btn btn-primary btn-sm">UPGRADE</a></h4>
                         @endif
                        @if($status == 0)
                       <h4><a href="{{route('payment',['id' => 1])}}" class="btn btn-primary btn-sm">GET ACCESS</a></h4>
                         @endif
                         </td>
<td>     @if($status == 1 && $product->id == env('BUSINESS_ID'))
                       <h4><a href="javascript:void(0)" class="btn btn-secondary btn-sm" style="pointer-events: none;">CURRENT PLAN</a></h4>
                         @elseif($status == 1)
                        <h4><a href="{{route('payment',['id' => 2])}}" class="btn btn-warning btn-sm">UPGRADE</a></h4>
                         @endif
                        @if($status == 0)
                       <h4><a href="{{route('payment',['id' => 2])}}" class="btn btn-warning btn-sm">GET ACCESS</a></h4>
                         @endif</td>
<td> @if($status == 1 && $product->id == env('AGENCY_ID'))
                       <h4><a href="javascript:void(0)" class="btn btn-secondary btn-sm" style="pointer-events: none;">CURRENT PLAN</a></h4>
                         @elseif($status == 1)
                        <h4><a href="{{route('payment',['id' => 3])}}" class="btn btn-primary btn-sm">UPGRADE</a></h4>
                         @endif
                        @if($status == 0)
                       <h4><a href="{{route('payment',['id' => 3])}}" class="btn btn-primary btn-sm">GET ACCESS</a></h4>
                         @endif</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
    <div class="container">
          <div class="row Audit-image-text" style="width:100%;padding:25px;margin-top:0px;text-align:center">
<div class="col-md-12"><h3>SEO software that helps grow your traffic, rankings, and sales online.</h3>
    <p>Ninja Report’s revolutionary SEO tools will allow you to get more organic traffic online and grow your business. Check out our features below to see how we can help you grow.</p>
</div>
          </div>
            <hr>
     <div class="row testimonials Audit-image-text" style="padding:25px;margin-top:50px;">
          <div class="col-md-6">
            <img src="images/brandon.jpeg" style="float:left;margin-right:10px;" alt=""/>
            <h4>Great SEO Tool</h4>
            <p>I save a lot of time using this product. Weekly reports of my website audit in my inbox are a dream come true for any SEO agency or marketer!</p>
            <label>Brandon S.</label>
          </div>
           <div class="col-md-6">
             <img src="images/megan.jpeg" style="float:left;margin-right:10px;" lt=""/>
            <h4>Great All Around Tool</h4>
            <p>Makes audit reporting much faster than it was before with other tools. I estimate I save 10-20 minutes per report with this tool.</p>
            <label>Megan R.</label>
          </div>
    </div>

    </div>
</div>
@endsection
