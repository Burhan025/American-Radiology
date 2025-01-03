<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

//* Portfolio Post type
require_once('physicians-post-type.php');

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles', 1000 );
function parallax_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'fonts', get_stylesheet_directory_uri() . '/fonts/stylesheet.css', array() );

    // Scripts
}

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'font-awesome' ); // FontAwesome 4
    wp_enqueue_style( 'font-awesome-5' ); // FontAwesome 5

    wp_dequeue_script( 'bootstrap' );
    wp_dequeue_script( 'jquery-fitvids' );
    wp_dequeue_script( 'jquery-waypoints' );
}, 9999 );


// Add Additional Image Sizes
add_image_size( 'physician-main-thumb', 228, 230, array( 'center', 'top' ) );
add_image_size( 'physician-featured', 382, 398, array( 'center', 'top' ) );
add_image_size( 'leadership-team', 432, 592, true );

// Physician Post type order
function posts_orderby_lastname( $orderby_statement ) {
    global $wpdb;

    // Check if the current query is for the 'physicians_post_type'
    if ( is_post_type_archive( 'physicians' ) || is_singular( 'physicians' ) ) {
        // Apply custom ordering for 'physicians_post_type'
        $orderby_statement = $wpdb->posts . '.post_title ASC';
    }

    return $orderby_statement;
}
add_filter( 'posts_orderby', 'posts_orderby_lastname' );


// Physician Search
function search_physician_name() {
	add_filter( 'posts_orderby', 'posts_orderby_lastname' );
	$args = array(
		'post_type'      => 'physicians',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	$searchbyname = new WP_Query( $args );

	if ( $searchbyname->have_posts() ) :
		?>
		<select id="physicians_name">
			<option value="https://www.americanrad.com/physicians/">View Providers By Name</option>
			<?php
			while ( $searchbyname->have_posts() ) : $searchbyname->the_post();
				?>
				<option value="<?php the_permalink(); ?>"><?php the_title(); ?></option>
				<?php
			endwhile;
			wp_reset_postdata();
			remove_filter( 'posts_orderby', 'posts_orderby_lastname' );
			?>
		</select>
		<?php
	else :
		esc_html_e( 'No physicians Found!', 'text-domain' );
	endif;
	?>
	<script>
		// get your select element and listen for a change event on it
		jQuery(document).ready(function($) {
			$('#physicians_name').on("change", function() {
				// set the window's location property to the value of the option the user has selected
				window.location = $(this).val();
			});
		});
	</script>
	<?php
}
add_shortcode( 'physician_name_dropdown', 'search_physician_name' );


// Physician Search Dropdown By Speciality
class my_Walker_CategoryDropdown extends Walker_CategoryDropdown {

	function start_el(&$output, $data_object, $depth = 0, $args = [], $current_object_id = 0) {
		$pad = str_repeat(' ', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$output .= "\t<option class=\"level-$depth\" value=\"".$category->slug."\"";
		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '  ('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '  ' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>\n";
	}
}

