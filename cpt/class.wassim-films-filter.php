<?php

if( ! class_exists( 'Wassim_Films_Filter_Post_Type' ) ) {

    class Wassim_Films_Filter_Post_Type {

        public function __construct() {

            add_action( 'init', array( $this, 'create_post_type' ) );
            add_action( 'init', array( $this, 'register_taxonomies' ) );
            add_action( 'init', array( $this, 'register_tags' ) );
        }

        public function create_post_type() {

            register_post_type(
                'films',
                array(
                    'label' => 'Films',
                    'description'   => 'Films',
                    'labels' => array(
                        'name'  => 'Films',
                        'singular_name' => 'Film'
                    ),
                    'public'    => true,
                    'supports'  => array( 'title', 'editor', 'thumbnail' ),
                    'hierarchical'  => false,
                    'show_ui'   => true,
                    'show_in_menu'  => true,
                    'rewrite' => [ 'slug' => 'movie' ],
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export'    => true,
                    'has_archive'   => true,
                    'exclude_from_search'   => false,
                    'publicly_queryable'    => true,
                    'show_in_rest'  => false, //New Gutenberg Editor
                    'menu_icon' => 'dashicons-format-video'
                )
            );
        }

        public function register_taxonomies() {

            register_taxonomy(
                'film_cat',
                'films',
                array(
                    'hierarchical' => true,
                    'labels' => array(
								'name' => 'categories',
								'singular_name' => 'Category',
								'menu_name' => 'categories',
								),
					'show_ui' => true,
					'show_admin_column' => true,
					'rewrite' => array( 'slug' => 'type' )
                )
            );
        }

        public function register_tags() {

            register_taxonomy(
                'film_keywords',
                'films',
                array(
                    'hierarchical' => false,
                    'labels' => array(
								'name' => 'keywords',
								'singular_name' => 'Keyword',
								'menu_name' => 'keywords',
								),
					'show_ui' => true,
					'show_admin_column' => true,
					'rewrite' => array( 'slug' => 'keyword' )
                )
            );
        }
    }
}