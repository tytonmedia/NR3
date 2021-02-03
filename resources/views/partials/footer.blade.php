</div>
</section>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.5/dist/umd/popper.min.js" ></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script>
$(document).ready(function($){

	 $("body").tooltip({ selector: '[data-toggle=tooltip]' });

    $('.close').click(function(e){
        $(this).closest('.modal').hide();
    });

    $("#login_button").click(function(e){
        var url = window.location.href;
        window.location ="/login?page="+url;
    });


});
</script>

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
            <p>Free accounts can only run 1 SEO analysis per 24 hour period. Come back in 24 hours or <a style="text-decoration:underline" href="{{route('subscription')}}">upgrade</a> to run more SEO analysis, audits, reports and more!</p>
            
          </div>

          <!-- Modal footer -->
          <div class="modal-footer" style="margin:auto;">
          <a class="btn-warning btn-md" href="{{route('subscription')}}" id='paybtn' style='padding:7px;text-decoration:none;'>VIEW PLANS</a>
          </div>

        </div>
      </div>
    </div>
    
</body>
</html>
