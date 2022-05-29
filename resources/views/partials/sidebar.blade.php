
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

					<a class="{{(Request::is('audit') || Request::is('audit/*')) ? 'active' : ''}} nav-link"  href="{{route('audit')}}"><i class="fa fa-refresh" aria-hidden="true"></i> SITE AUDIT</a>

					<div class="audit-item" style="display:none;">
						<a class="dropdown-item" href="#overview"><i class="fa fa-bullseye" aria-hidden="true"></i> OVERVIEW</a>
						<a class="dropdown-item" href="#errors"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ERRORS</a>
						<a class="dropdown-item" href="#warnings"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> WARNINGS</a>
						<a class="dropdown-item" href="#notices"><i class="fa fa-flag" aria-hidden="true"></i> NOTICES</a>
					</div>
				</li>

				<li class="nav-item">

					<a class="{{(Request::is('analysis') || Request::is('analysis/*')) ? 'active' : ''}} nav-link" href="{{ route('analysis') }}"><i class="fa fa-search" aria-hidden="true"></i> SEO REPORT</a>
					
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

					<li class="nav-item"><a class="{{(Request::is('backlinks') || Request::is('backlinks/*')) ? 'active' : ''}} nav-link" href="{{ route('backlinks') }}"><i class="fa fa-link" aria-hidden="true"></i> BACKLINKS <i class="new-icon">NEW</i></a></li>


					<li class="nav-item"><a class="{{(Request::is('rankings') || Request::is('rankings/*')) ? 'active' : ''}} nav-link" href="{{ route('rankings') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> RANKINGS <i class="new-icon">NEW</i></a></li>
		
		<li class="nav-item"><a class="{{(Request::is('traffic') || Request::is('traffic/*')) ? 'active' : ''}} nav-link" href="{{ route('traffic') }}"><i class="fa fa-dashboard" aria-hidden="true"></i> TRAFFIC <i class="new-icon">NEW</i></a></li>

				
				<li class="nav-item"><a class="{{(Request::is('subscription') || Request::is('subscription/')) ? 'active' : ''}} nav-link" href="/subscription"><i class="fa fa-money" aria-hidden="true"></i> PRICING</a></li>

			</ul>

			<div class="footer">
				<p class="text-center">© Copyright Ninja Brands LLC, 2020</p>
			</div>
		</div>



	</div>
