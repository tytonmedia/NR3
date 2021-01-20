
@php
if (isset($_GET['url'])) {
	$url = $_GET['url'];
} else {
	$url = '';
}
@endphp
<div class="row">
	<div class="col-md-2 side-col">
			<div class="side-bar">
			<ul class="nav flex-column">
				<li class="nav-item">
					<a class="{{(Request::is('home') || Request::is('home/')) ? 'active' : ''}} nav-link " href="/home"><i class="fa fa-home" aria-hidden="true"></i> DASHBOARD</a>
				</li>
				<li class="nav-item">
					<a style="display:none" class="nav-link" href="#" data-toggle="modal" data-target="#myModal">KEYWORD TRACKING</a></li>

				<li class="nav-item">
					@if($url != '')
					<a class="{{(Request::is('audit') || Request::is('audit/')) ? 'active' : ''}} nav-link"  href="{!! route('audit',['url'=>$url]) !!}"><i class="fa fa-refresh" aria-hidden="true"></i> SITE AUDIT</a>
					@else
					<a class="{{(Request::is('audit') || Request::is('audit/')) ? 'active' : ''}} nav-link"  href="{{route('audit')}}"><i class="fa fa-refresh" aria-hidden="true"></i> SITE AUDIT</a>
					@endif
					
					<div class="audit-item" style="display:none;">
						<a class="dropdown-item" href="#overview"><i class="fa fa-bullseye" aria-hidden="true"></i> OVERVIEW</a>
						<a class="dropdown-item" href="#errors"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ERRORS</a>
						<a class="dropdown-item" href="#warnings"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> WARNINGS</a>
						<a class="dropdown-item" href="#notices"><i class="fa fa-flag" aria-hidden="true"></i> NOTICES</a>
					</div>
				</li>

				<li class="nav-item">
					@if($url != '')
					<a class="{{(Request::is('analysis') || Request::is('analysis/')) ? 'active' : ''}} nav-link" href="{!! route('analysis',['url'=>$url]) !!}"><i class="fa fa-search" aria-hidden="true"></i> SEO REPORT</a>
					@else
					<a class="{{(Request::is('analysis') || Request::is('analysis/')) ? 'active' : ''}} nav-link" href="{{ route('analysis') }}"><i class="fa fa-search" aria-hidden="true"></i> SEO REPORT</a>
					@endif

					
					<div class="analysis_section" style="display:none;">
						<div class="">
							<a class="dropdown-item" href="#header"><i class="fa fa-code" aria-hidden="true"></i> HEADER</a>
							<a class="dropdown-item" href="#technical"><i class="fa fa-cog" aria-hidden="true"></i> TECHNICAL</a>
							<a class="dropdown-item" href="#rankings"><i class="fa fa-bullseye" aria-hidden="true"></i> RANKINGS</a>
							<a class="dropdown-item" href="#links"><i class="fa fa-link" aria-hidden="true"></i> LINKS</a>
							<a class="dropdown-item" href="#Content"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> CONTENT</a>
							<a class="dropdown-item" href="#performance"><i class="fa fa-bar-chart" aria-hidden="true"></i> PERFORMANCE</a>
							<a class="dropdown-item" href="#security"><i class="fa fa-shield" aria-hidden="true"></i> SECURITY</a>
							<a class="dropdown-item" href="#social"><i class="fa fa-facebook-official" aria-hidden="true"></i> SOCIAL</a>
							<a class="dropdown-item" href="#other"><i class="fa fa-asterisk" aria-hidden="true"></i> OTHER</a>
						</div>
					</div>
				</li>
				@if($url != '')
					<li class="nav-item"><i class="new-icon">NEW</i><a class="{{(Request::is('backlinks') || Request::is('backlinks/')) ? 'active' : ''}} nav-link" href="{!! route('backlinks',['url'=>$url]) !!}"><i class="fa fa-link" aria-hidden="true"></i> BACKLINKS</a></li>
					@else
					<li class="nav-item"><i class="new-icon">NEW</i><a class="{{(Request::is('backlinks') || Request::is('backlinks/')) ? 'active' : ''}} nav-link" href="{{ route('backlinks') }}"><i class="fa fa-link" aria-hidden="true"></i> BACKLINKS</a></li>
					@endif
						@if($url != '')
					<li class="nav-item"><i class="new-icon">NEW</i><a class="{{(Request::is('rankings') || Request::is('rankings/')) ? 'active' : ''}} nav-link" href="{!! route('rankings',['url'=>$url]) !!}"><i class="fa fa-bar-chart" aria-hidden="true"></i> RANKINGS</a></li>
					@else
					<li class="nav-item"><i class="new-icon">NEW</i><a class="{{(Request::is('rankings') || Request::is('rankings/')) ? 'active' : ''}} nav-link" href="{{ route('rankings') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> RANKINGS</a></li>
					@endif
				
				<li class="nav-item"><a class="{{(Request::is('subscription') || Request::is('subscription/')) ? 'active' : ''}} nav-link" href="/subscription"><i class="fa fa-tachometer" aria-hidden="true"></i> PRICING</a></li>
			</ul>

			<div class="footer">
				<p class="text-center">© Copyright Ninja Brands LLC, 2020</p>
			</div>
		</div>
			<!--The Model-->
		<div class="modal" id="rankingsUpgrade" tabindex="-1" role="dialog" aria-labelledby="rankingsUpgrade" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					
					<!-- Modal Header -->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
					</div>

					<!-- Modal body -->
					<div class="modal-body" style="padding:20px;">
						<h2>Whoops!</h2>
						<p>You have reached the limit on the ranking analysis you can run for your billing period. You must upgrade in order to run more ranking analysis.
					</div>

					<!-- Modal footer -->
					<div class="modal-footer" style="margin:auto;">
					<a class="btn-warning btn-md" href="{{route('subscription')}}" id='paybtn' style='padding:7px;text-decoration:none;'>UPGRADE</a>
					</div>

				</div>
			</div>
		</div>
		<!--The Model-->
		<div class="modal" id="backlinksUpgrade" tabindex="-1" role="dialog" aria-labelledby="backlinksUpgrade" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					
					<!-- Modal Header -->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
					</div>

					<!-- Modal body -->
					<div class="modal-body" style="padding:20px;">
						<h2>Whoops!</h2>
						<p>You have reached the limit on the backlink analysis you can run for your billing period. You must upgrade in order to run more backlink analysis.
					</div>

					<!-- Modal footer -->
					<div class="modal-footer" style="margin:auto;">
					<a class="btn-warning btn-md" href="{{route('subscription')}}" id='paybtn' style='padding:7px;text-decoration:none;'>UPGRADE</a>
					</div>

				</div>
			</div>
		</div>
		<!--The Model-->
		<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					
					<!-- Modal Header -->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
					</div>

					<!-- Modal body -->
					<div class="modal-body" style="padding:20px;">
						<h2>Whoops!</h2>
						<p>You must upgrade in order to run a website audit. View our affordable plans below to see all of the SEO errors on your website.

					</div>

					<!-- Modal footer -->
					<div class="modal-footer" style="margin:auto;">
					<a class="btn-warning btn-md" href="{{route('subscription')}}" id='paybtn' style='padding:7px;text-decoration:none;'>VIEW PLANS</a>
					</div>

				</div>
			</div>
		</div>
		<!-- Modal -->
		<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h3>Login with Google</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
			
				<div class="modal-body">
					<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
					<p>You must be logged in to use Ninja Reports. Sign in using your Google account to test your website for 100+ SEO factors.</p>
					<div class="google-btn">
						<div class="google-icon-wrapper">
							<img class="google-icon" src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg"/>
						</div>
						<a class="btn-text " href="javascript:0;" id="login_btn" style="text-decoration:none"><b>Sign in with google</b></a>
					</div>
				</div>
				<hr>
				<!-- <button class="btn-text" href="" id="login_btn"  style="text-decoration : none"><b>google</b></button> -->
				<div class="modal-footer">

				</div>
				</div>
			</div>
		</div>
		<!-- Access Model -->

		<div class="modal" id="upgradeModel" tabindex="-1" role="dialog" aria-labelledby="upgradeModel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<!-- Modal Header -->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
					</div>

					<!-- Modal body -->
					<div class="modal-body" style="padding:20px;">
					<h2>Whoops!</h2>
						<p>Free accounts can only run 1 SEO analysis per 24 hour period. Come back in 24 hours or try our <a style="text-decoration:underline" href="{{route('subscription')}}">free trial</a> to run more SEO analysis, audits, reports and more!</p>
						
					</div>

					<!-- Modal footer -->
					<div class="modal-footer" style="margin:auto;">
					<a class="btn-warning btn-md" href="{{route('subscription')}}" id='paybtn' style='padding:7px;text-decoration:none;'>VIEW PLANS</a>
					</div>

				</div>
			</div>
		</div>


		<div class="modal" id="addprojectmodal" tabindex="-1" role="dialog" aria-labelledby="addprojectmodal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<!-- Modal Header -->
					<div class="modal-header">
						<h3>Add a Website</h3>
						<button type="button" class="close" data-dismiss="modal" id="close">&times;</button>
					</div>

					<!-- Modal body -->
					<div class="modal-body" style="padding:20px;">
						<p>Add your website URL below:</p>
						<input type="text" class="form-control" placeholder="Website URL" name="project_url" id="project_url">
						<br/>
					</div>

					<!-- Modal footer -->
					<div class="modal-footer" style="margin:auto;">
					<a class="btn-warning btn-md" href="{{route('add_website')}}" id='add_website_btn' style='padding:7px;text-decoration:none;'>ADD PROJECT</a>
					</div>

				</div>
			</div>
		</div>

	</div>
