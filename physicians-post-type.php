<?php

// register the taxonomy to only that custom post type by passing the custom post type name as argument in register_taxonomy.
function physicians_taxonomy() {  
    register_taxonomy(  
        'physician_categories',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'physicians',        //post type name
        array(  
            'hierarchical' => true,  
            'label' => 'Physician Specialty',  //Display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'physician', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before 
            )
        )  
    );  
}  
add_action( 'init', 'physicians_taxonomy');


//Then to change the permalink I have created following function
function filter_post_type_link($link, $post)
{
    if ($post->post_type != 'physicians')
        return $link;

    if ($cats = get_the_terms($post->ID, 'physician_categories'))
        $link = str_replace('%physician_categories%', array_pop($cats)->slug, $link);
    return $link;
}
add_filter('post_type_link', 'filter_post_type_link', 10, 2);

// Register Physician Post Type
function physicians_post_type() {

	$labels = array(
		'name'                  => _x( 'Physicians', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Physician', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Physicians', 'text_domain' ),
		'name_admin_bar'        => __( 'Physician', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Physician:', 'text_domain' ),
		'all_items'             => __( 'All Physicians', 'text_domain' ),
		'add_new_item'          => __( 'Add New Physician', 'text_domain' ),
		'add_new'               => __( 'Add New Physician', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Physician', 'text_domain' ),
		'update_item'           => __( 'Update Physician', 'text_domain' ),
		'view_item'             => __( 'View Physician', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Physician', 'text_domain' ),
		'not_found'             => __( 'No portfolio found', 'text_domain' ),
		'not_found_in_trash'    => __( 'No portfolio found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Physician', 'text_domain' ),
		'description'           => __( 'Physician information pages.', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', ),
		'taxonomies'            => array( 'physician_categories', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'rewrite' => array('slug' => 'physicians/%physician_categories%','with_front' => FALSE),
		'has_archive'           => 'physicians',		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'physicians', $args );

}
add_action( 'init', 'physicians_post_type', 0 );



// Auto apply Default Category to portfolio post if not defined.
// function default_taxonomy_term( $post_id, $post ) {
//     if ( 'publish' === $post->post_status ) {
//         $defaults = array(
//             'physician_categories' => array( 'all'),   //

//             );
//         $taxonomies = get_object_taxonomies( $post->post_type );
//         foreach ( (array) $taxonomies as $taxonomy ) {
//             $terms = wp_get_post_terms( $post_id, $taxonomy );
//             if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
//                 wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
//             }
//         }
//     }
// }
// add_action( 'save_post', 'default_taxonomy_term', 100, 2 );


?>