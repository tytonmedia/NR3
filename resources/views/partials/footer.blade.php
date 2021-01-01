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
<!-- Start of ninjareports Zendesk Widget script -->
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=ebb41629-5af1-47a6-8db6-e8816aa0c264"> </script>
<!-- End of ninjareports Zendesk Widget script -->
</body>
</html>
