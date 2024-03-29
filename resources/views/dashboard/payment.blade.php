@extends('layouts.master')
@section('title', 'Stripe Checkout')
@section('content')
<style>
		/**
	* The CSS shown here will not be introduced in the Quickstart guide, but shows
	* how you can use CSS to style your Element's container.
	*/
	/* body{
		background-color: #eee;
	} */
	.StripeElement {
	box-sizing: border-box;
	width: 98%;
	height: 40px;

	padding: 10px 12px;

	border: 1px solid #ccc;
	border-radius: 4px;
	background-color: white;
	margin-left: 4px;
	box-shadow: 0 1px 3px 0 #e6ebf1;
	-webkit-transition: box-shadow 150ms ease;
	transition: box-shadow 150ms ease;
	}

	.StripeElement--focus {
	box-shadow: 0 1px 3px 0 #cfd7df;
	}

	.StripeElement--invalid {
	border-color: #fa755a;
	}

	.StripeElement--webkit-autofill {
	background-color: #fefde5 !important;}
	.pay{
		width:50%;  
	}
	.container{	
		margin: 0;
		background-color: #eee;
		position: absolute;
		top: 50%;
		left: 50%;
		-ms-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);
		width:80%;
		height:70vh;
		border:1px solid #ccc;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.btn{
	display: block;
	width: 100%;
	border: none;
	background-color: #9e28b4;
	color: white;
	padding: 9px 28px;
	font-size: 16px;
	cursor: pointer;
	text-align: center;
	border-radius: 3px;
	}
	.label{
		width: 100% !important;
		background: #eee;
		padding: 7px 30% 10px 37%;
		border-radius: 3px;
		margin-bottom: -10px;

	}
	#payment-form{
		margin-top:12px;
	}

</style>

<div class="col-md-10 overview">
	<div class="row" style="margin-top:75px;">
		<div class="col-md-1">
		</div>
	<div class="col-md-4">

			<h1>Subscribe <img src="{{asset('images/powered by stripe.png')}}" alt="stripe" style="max-width:130px"/></h1>
				<label for="card-element" class="label label-primary">
					Credit or debit card
				</label>
				<form action="/stripe/{{$id}}" method="post" id="payment-form">
				@csrf
				<input type="hidden" value="{{$id}}">
				<div class="form-row">
					
					<div id="card-element">
					<!-- A Stripe Element will be inserted here. -->
					</div>

					<!-- Used to display form errors. -->
					<div id="card-errors" role="alert"></div>
				</div>
				<div style="text-align:center;"><input type="checkbox" id="policy" name="policy" checked="checked"> <label for="policy" style="font-size:13px;padding-top:7px;color:#999">I have read and I agree to the <a target="_blank" href="https://www.ninjareports.com/terms-conditions/">Terms of Use</a>.</label></div>
				<button class="btn btn-lg btn-warning" style="font-weight:bold;font-size:21px;">GET ACCESS &nbsp;<i class="fa fa-caret-right" aria-hidden="true"></i></button>
			</form>
			
	</div>
	<div class="col-md-4" style="padding:65px 15px 15px 15px;">
		
		<h3>Subscription Details</h3>

<label>Next Billing Date</label>: <strong>{{$next_billing}}</strong>
</br>
<label>Package</label>:
		@if($id == '1')
					<strong>Webmaster</strong>
				@endif
				@if($id == '2')
					<strong>Business</strong>
				@endif
				@if($id == '3')
					<strong>Agency</strong>
				@endif
				<br/>
<label>Savings</label>: <strong style="color:#ff0000">
		@if($id == '1')
					35%
				@endif
				@if($id == '2')
					40%
				@endif
				@if($id == '3')
					40%
				@endif
</strong>
<br/>
	<div style="border-top:1px solid #ddd;padding:5px;background:#f2f2f2">
		<label style="margin:0">Price</label>:
			@if($id == '1')
					<strong style="font-size:18px;color:green"><strike style="color:#999">$29</strike> $10</strong>
				@endif
				@if($id == '2')
					<strong style="font-size:18px;color:green"><strike style="color:#999">$49</strike> $20</strong>
				@endif
				@if($id == '3')
					<strong style="font-size:18px;color:green"><strike style="color:#999">$99</strike> $30</strong>
				@endif
	</div>



	</div>
</div>

<div class="row text-center" style="padding:30px 0">
	<div class="col-md-1">
	</div>
	<div class="col-md-8">
		<strong style="display:block;text-align:center;font-size:12px;color:#ccc">FEATURED ON</strong>
	<img src="https://www.ninjareports.com/wp-content/uploads/2020/07/as-seen-on.png" alt="seen on" style="width:100%"/>
	<hr/>
</div>
<div class="col-md-1">
	</div>
</div>

	     <div class="row testimonials Audit-image-text" style="padding:25px;margin-top:50px;">
	     	<div class="col-md-1">
	     	</div>
          <div class="col-md-4">
            <img src="{{asset('images/brandon.jpeg')}}" style="float:left;margin-right:10px;" alt=""/>
            <h4>Great SEO Tool</h4>
            <p>I save a lot of time using this product. I can easily check the SEO health of my website and find all SEO issues to fix for maximum rankings.</p>
            <label style="font-weight:bold;text-align:right;display:block;">Brandon S.</label>
          </div>
           <div class="col-md-4">
             <img src="{{asset('images/megan.jpeg')}}" style="float:left;margin-right:10px;" lt=""/>
            <h4>Great All Around Tool</h4>
            <p>Makes audit reporting much faster than it was before with other tools. Really love this software.</p>
            <label style="font-weight:bold;text-align:right;display:block;">Megan R.</label>
          </div>
          <div class="col-md-2">
          </div>
    </div>


	</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
	// Create a Stripe client.
	var key = '{{ Config::get('services.stripe.publishable_key') }}';
	var stripe = Stripe(key);

	// Create an instance of Elements.
	var elements = stripe.elements();

	// Custom styling can be passed to options when creating an Element.
	// (Note that this demo uses a wider set of styles than the guide below.)
	var style = {
	base: {
		color: '#32325d',
		fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
		fontSmoothing: 'antialiased',
		fontSize: '16px',
		'::placeholder': {
		color: '#aab7c4'
		}
	},
	invalid: {
		color: '#fa755a',
		iconColor: '#fa755a'
	}
	};

	// Create an instance of the card Element.
	var card = elements.create('card', {style: style});

	// Add an instance of the card Element into the `card-element` <div>.
	card.mount('#card-element');
	// Handle real-time validation errors from the card Element.
	card.on('change', function(event) {
	var displayError = document.getElementById('card-errors');
	if (event.error) {
		displayError.textContent = event.error.message;
	} else {
		displayError.textContent = '';
	}
	});

	// Handle form submission.
	var form = document.getElementById('payment-form');
	form.addEventListener('submit', function(event) {
	event.preventDefault();

	stripe.createToken(card).then(function(result) {
		if (result.error) {
		// Inform the user if there was an error.
		var errorElement = document.getElementById('card-errors');
		errorElement.textContent = result.error.message;
		} else {
		// Send the token to your server.
		stripeTokenHandler(result.token);
		}
	});
	});

	// Submit the form with the token ID.
	function stripeTokenHandler(token) {
	// Insert the token ID into the form so it gets submitted to the server
	var form = document.getElementById('payment-form');
	var hiddenInput = document.createElement('input');
	hiddenInput.setAttribute('type', 'hidden');
	hiddenInput.setAttribute('name', 'stripeToken');
	hiddenInput.setAttribute('value', token.id);
	form.appendChild(hiddenInput);

	// Submit the form
	form.submit();
	}
</script>
@endsection