<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>


<hr />


</div>

</div>
		<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/assets/js/cycle.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/assets/js/script.js"></script>

        <!--script type="text/javascript">

			jQuery(function(){

				var message = jQuery( "#shopgrid" );
				var originalMessageTop = message.offset().top;
				var view = jQuery( window );

				view.bind(

					"scroll resize",

					function(){
						var viewTop = view.scrollTop();
						if (

							(viewTop > originalMessageTop) &&
							!message.is( ".fixed" )
							){
							message
								.removeClass( "rela" )
								.addClass( "fixed" );
						} else if (
							(viewTop <= originalMessageTop) &&
							message.is( ".fixed" )
							){
							message
								.removeClass( "fixed" )
								.addClass( "rela" );

						}
					}
				);
			});

		</script-->

        <script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#iframe-wrap").stayInView();
			});

			// plugin code for stayInView effect
			(function($) {
				$.fn.stayInView = function() {
					return this.each(function() {
						var $element = $(this),
								elementTop = ($element.offset().top - 0);

						$(window).bind("scroll resize", function(){
							var viewTop = $(window).scrollTop();

							if((viewTop > elementTop) && !$element.is( ".fixed" ) ){
								$element.addClass( "fixed" );
							}
							else if ((viewTop <= elementTop) && $element.is( ".fixed" )){
								$element.removeClass( "fixed" );
							}

						});


							// $(window).bind("scroll", function() {
							// 	if ($(window).scrollTop() > $element.offset().top) {
							//
							// 		$element.stop().animate({ "marginTop": ($(window).scrollTop()) - ($element.offset().top + 502) }, "slow");
							//
							// 	}
							// });


					});
				};
			})(jQuery);

			//(function($) {
			//	$.fn.stayInView = function() {
			//		return this.each(function() {
			//			var $element = $(this);
			//
			//			$(window).bind("scroll", function() {
			//				$element.stop().animate({ "marginTop": ($(window).scrollTop()) - $element.offset().top }, "slow");
			//			});
			//		});
			//	};
			//})(jQuery);


		</script>
<?php
global $parts;
$footer = $parts->renderFooter();
// $footer = preg_replace("/Facebook(\s*)<\/a>(.*)<\/li>(\s+)<\/ul>/i","Facebook</a>$2</li>$3</ul><div class=\"newsletter-signup clearfix\">

//   	<form target=\"_blank\" id=\"newsletter-validate-detail\" method=\"post\" action=\"http://visitor.r20.constantcontact.com/d.jsp\">
//         <input type=\"hidden\" value=\"xftkxrcab\" name=\"llr\">
//         <input type=\"hidden\" value=\"1102254001335\" name=\"m\">
//         <input type=\"hidden\" value=\"oi\" name=\"p\">
//         <p class=\"text\">
//            <label for=\"newsletter\">Email Signup</label>
//            <input type=\"text\" class=\"input-text required-entry validate-email\" title=\"Sign up for our newsletter\" id=\"newsletter\" name=\"ea\">
//         </p>
//         <p class=\"submit\">
//            <input type=\"submit\" title=\"+\">
//         </p>
//     </form>

//     <script type=\"text/javascript\">
//     //&lt;![CDATA[
//         var newsletterSubscriberFormDetail = new VarienForm('newsletter-validate-detail');
//     //]]&gt;
//     </script>
// </div>",$footer);
$c = $_COOKIE["usingstore"];
if( $c )
    {
	if( $c == "us" )
	    $footer = str_replace( ".com/eu/", ".com/us/", $footer );
	}

echo $footer;

?>
<script language='javascript'>
    <?php if( $_COOKIE["usingstore"] == "eu" ) { ?>
					      document.getElementById( "countryselector" ).innerHTML = "<A href='/us/'>US</a> / EU";
					      <?php } else { ?>
					      document.getElementById( "countryselector" ).innerHTML = "US / <A href='/eu/'>EU</a>";
    <?php } ?>
    </script>
</body>
</html>

