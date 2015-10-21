<?php
/**
 * Template Name: Auther map New
 *
 * @package Marketify
 */
?>
<?php get_header(); ?>

<?php //echo do_shortcode('[azindex content="true" filter="title" posttype="download" postcount="10"]'); ?>


<style type="text/css">
 .acf-map {
	width: 100%;
	height: 400px;
	border: #ccc solid 1px;
	margin: 10px 2px 20px;
}
 </style>


<script type="text/javascript">
(function($) {
 /*
*  render_map
*  This function will render a Google Map onto the selected jQuery element
*/
 function render_map( $el ) {
 	// var
	var $markers = $el.find('.marker');
 	// vars
	var args = {
		zoom		: 14,
		center		: new google.maps.LatLng(0, 0),
		mapTypeId	: google.maps.MapTypeId.ROADMAP
	};
 	// create map
	var map = new google.maps.Map( $el[0], args);
 	// add a markers reference
	map.markers = [];
 	// add markers
	$markers.each(function(){
     	add_marker( $(this), map );
 	});
 	// center map
	center_map( map );
 }
 /*
*  add_marker
*  This function will add a marker to the selected Google Map
*/
function add_marker( $marker, map ) {
 	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );
 	// create marker
	var marker = new google.maps.Marker({
		position	: latlng,
		map			: map
	});
 	// add to array
	map.markers.push( marker );
 	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.html()
		});
 		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.close();
 			infowindow.open( map, marker );
 			$('#nirmal').remove();
 			document.getElementById("zamir").innerHTML = infowindow.content;
 		});

	}
 }
 /*
*  center_map
*  This function will center the map, showing all markers attached to this map
*/
 function center_map( map ) {
 	// vars
	var bounds = new google.maps.LatLngBounds();
 	// loop through all markers and create bounds
	$.each( map.markers, function( i, marker ){
 		var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
 		bounds.extend( latlng );
 	});
 	// only 1 marker?
	if( map.markers.length == 1 )
	{
		// set center of map
	    map.setCenter( bounds.getCenter() );
	    map.setZoom( 14 );
	}
	else
	{
		// fit to bounds
		map.fitBounds( bounds );
	}
 }
/*
*  document ready
*  This function will render each map when the document is ready (page has loaded)
*/
 $(document).ready(function(){
 	$('.acf-map').each(function(){
 		render_map( $(this) );
 	});
 });
 })(jQuery);
</script>

	<?php
			$blogusers = get_users( array( 'orderby' => array( 'display_name' ) ) );?>

			<div class="acf-map">
				<?php foreach ( $blogusers as $user ) {?>

					<?php
					echo '<span>' . esc_html( $user->display_name ) . '</span>';
					/*  ..............................................................
							To display only fronted users only
							//echo $user->roles[1];
						...............................................................
					*/

				if($user->roles[1] =="frontend_vendor" || $user->roles[0] =="administrator") {

					$auther_test = $auther->user_login;
					if(empty($user->city)) {
						add_user_meta( $user->ID, 'city', 'Texas');
					} else {
					$city = $user->city;
					}
					if(empty($user->country)) {
						add_user_meta( $user->ID, 'country','United States');
					} else {
					$country = $user->country;
					}
					$address = $city . $country;
					$address = str_replace(" ", "+", $address);
						//$region = "India";

					$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
					$json = json_decode($json);

					$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
					$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
					echo $lat."</br>".$long;
					?>
					<div class="marker" data-lat="<?php echo $lat; ?>" data-lng="<?php echo $long; ?>">
						<p class="address"><?php echo $user->first_name; ?></p>
						<?php
						$args = array(
							'post_type'			=>	'download',
						    'author'        	=>  $user->ID, // I could also use $user_ID, right?
						    'posts_per_page'	=>  -1,
						    'order'         	=>  'ASC'
						    );

						$auther_post = new WP_Query($args);
						if($auther_post->have_posts()) :
							while($auther_post->have_posts()) : $auther_post->the_post(); ?>

								<?php echo get_the_title(); ?>

								<?php the_post_thumbnail('thumbnail'); ?>
								<!-- ...............Add to cart shortcode ...................-->
								<div class="entry-image">
									<div class="overlay">
										<?php do_action( 'marketify_download_content_image_overlay_before' ); ?>
										<div class="actions">
											<?php marketify_purchase_link( get_the_ID() ); ?>
											<?php do_action( 'marketify_download_content_image_overlay_after' ); ?>
										</div>
									</div>
								</div>

								<?php locate_template( array( 'modal-download-purchase.php' ), true, false ); ?>
								<!-- ...............Add to cart shortcode end...................-->
								<?php echo "Auther ID is :".$post->post_author; ?><br />
								<a class="button" href="<?php the_permalink();?>">Use this art</a></p>
								<hr />
							<?php endwhile; ?>
							<?php else: ?>
							<?php echo "No data Found"; ?>
							<?php endif; ?>
							<?php wp_reset_postdata();?>
					</div> <!-- .marker-->
					<?php } ?> <!-- endif-->

				<?php } ?>
			</div><!-- .acf-map -->














<?php get_footer(); ?>
<div id="zamir"></div>
<div id="nirmal">This will be remove when someone click on map location.</div>
