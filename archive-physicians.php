<?php
/*
Template Name: Full Width
Template Post Type: post
*/

get_header();

?><div class="fl-row-content-wrap physicians">
	<div class="fl-row-content fl-row-fixed-width fl-node-content">
			<?php
			//custom search value

			echo '<div class="main-physician-page">'; ?>
				
			<div class="search-dropdowns-wrapper">	
	
			    <div class="search-dropdowns one-half">

			        <!-- Search by physicians Speciality -->
			        <form method="get" action="/" class="physician-speciality">
			            <?php
			            $args = array(
			                'orderby'         => 'NAME',
			                'taxonomy'        => 'physician_categories',
			                'name'            => 'physician_categories',
			                'show_option_all' => 'Filter by Specialty',
			                'value_field'     => 'slug',
			                'selected'        => get_query_var('physician_categories'), // Get selected taxonomy term
			            );

			            wp_dropdown_categories($args);
			            ?>
			            <script type="text/javascript">
			                jQuery(document).ready(function ($) {
			                    $("#physician_categories").change(function (e) {
			                        e.preventDefault();
			                        var selected = $(this).val();
									console.log(selected);
			                        $("article.physicians").hide();
			                        $("article.physician_categories-" + selected).fadeIn();
			                    });
			                });
			            </script>

			        </form>

					<!-- Search Dropdown by Name -->
			        <?php
			        $args = array(
			            'post_type'   => 'physicians',
			            'post_status' => 'publish',
			            'posts_per_page' => -1,
			        );

			        $searchbyname = new WP_Query($args);

			        if ($searchbyname->have_posts()) : ?>
			            <select id="physicians_name">
			                <option value="https://www.americanrad.com/physicians/">Providers</option>
			                <?php
			                while ($searchbyname->have_posts()) : $searchbyname->the_post();
			                ?>
			                    <option value="<?php the_permalink(); ?>"><?php printf('%1$s %2$s', get_the_title(), get_the_content()); ?></option>
			                <?php
			                endwhile;
			                wp_reset_postdata();
			                ?>
			            </select>
			        <?php
			        else :
			            esc_html_e('No physicians Found!', 'text-domain');
			        endif;
			        ?>
			        <script>
			            // jQuery script
			            jQuery(document).ready(function ($) {
			                $('#physicians_name').bind("change keyup", function () {
			                    window.location = $(this).val();
			                });
			            });
			        </script>

			    </div>

				<!-- Search Field with Autocomplete -->
				<!-- <form method="get" class="search-form<?php echo '' != get_search_query() ? ' active' : ''; ?>" action="<?php echo esc_url(home_url('/')); ?>">
					<input type="search" class="search-field" placeholder="Search Providers Here..." value="<?php echo esc_attr(get_search_query()); ?>" name="s" autocomplete="on">
					<button type="submit" class="search-submit"><img src="<?php get_stylesheet_directory_uri() ?>/wp-content/uploads/2024/04/loupe-2.svg" class="search-icon" /></button>
					<input type="hidden" name="post_type" value="physicians" />
				</form> -->
				
				<div class="search-form">

					<?php 
					echo do_shortcode('[wpdreams_ajaxsearchlite]'); 
					?>

				</div>

			</div>
<!-- 	Updated Query with sorting -->
		
<div class="physicians-wrapper">
    <?php
    // Query all posts without ordering
    $taxonomy_args = array(
        'post_type' => 'physicians',
        'posts_per_page' => -1,
    );
    $tools_in_taxonomy_term = new WP_Query($taxonomy_args);

    // Fetch all posts
    $posts = $tools_in_taxonomy_term->posts;

    // Custom sorting function
    usort($posts, function($a, $b) {
        $a_last_name = trim(get_field('last_name_ara', $a->ID));
        $b_last_name = trim(get_field('last_name_ara', $b->ID));
        return strcasecmp($a_last_name, $b_last_name); // Case-insensitive ascending order
    });

    // Display sorted posts
    if ($posts) :
        foreach ($posts as $post) :
            setup_postdata($post);
            $categories = get_the_terms($post->ID, 'physician_categories'); // Ensure correct taxonomy
            $class_names = [];
            if (!is_wp_error($categories) && !empty($categories)) {
                foreach ($categories as $category) {
                    $class_names[] = sanitize_title($category->name);
                }
            }
            $class_names = join(' ', $class_names);
            printf('<article class="entry %s">', esc_attr($class_names));
            if (has_post_thumbnail()) { ?>
                <a class="physician-image" href="<?php the_permalink(); ?>"
                    title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('physician-main-thumb'); ?></a>
            <?php } else { ?>
                <a class="physician-image" href="<?php the_permalink(); ?>"
                    title="<?php the_title_attribute(); ?>"><img
                        src="<?php echo get_stylesheet_directory_uri() ?>/images/small-placeholder.jpg"
                        alt="<?php the_title_attribute(); ?>" /></a>
            <?php }
            ?>
                <a class="physician-title" href="<?php the_permalink(); ?>"
                    title="<?php the_title_attribute(); ?>">
					<?php if (get_field('first_name_ara')): the_field('first_name_ara');
                        endif; ?>
                    <?php if (get_field('last_name_ara')): the_field('last_name_ara');
                    endif; ?>  
                    <?php if (get_field('designation_ara')): echo ', ';
                    the_field('designation_ara');
                    endif; ?>
                </a>
            <?php
            printf('</article>');
        endforeach;
        wp_reset_postdata();
    else :
        echo '<p>No posts found</p>';
    endif;
    ?>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery('#physician_categories').change(function() {
            var selectedCategory = jQuery(this).val(); // Get the selected value

            // First, hide all articles
            jQuery('article.entry').hide();

            // Now, show only those articles that match the selected category class
            if(selectedCategory === '0'){ // If the "Filter by Specialty" option is selected, show all
                jQuery('article.entry').show();
            } else {
                jQuery('article.entry.' + selectedCategory).show();
            }
        });
    });
</script>
<?php get_footer(); ?>
