<?php

/**
*Plugin Name: Wassim Films Filter
*Plugin URI: https://wordpress.org/wj-films-filter
*Description: My plugin's description
*Version: 1.0
*Requires at least: 5.6
*Author: Wassim Jelleli
*Author URI: https://www.linkedin.com/in/wassim-jelleli/
*Text Domain: wassim-films-filter
*Domain Path: /languages
*/

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Wassim_Films_Filter' ) ) {

    class Wassim_Films_Filter {

        public function __construct() {

            $this->define_constants();
            require_once( WJ_FILMS_FILTER_PATH . 'cpt/class.wassim-films-filter.php' );
            $wassim_films_filter_cpt = new Wassim_Films_Filter_Post_Type();
            add_filter( 'theme_page_templates', array( $this, 'my_template_register' ), 10, 3 );
            add_filter( 'template_include', array( $this, 'load_template' ), 999 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'wp_ajax_filter', array( $this, 'filter_ajax' ) );
            add_action( 'wp_ajax_nopriv_filter', array( $this, 'filter_ajax' ) );
        }

        public function define_constants() {

            define( 'WJ_FILMS_FILTER_PATH', plugin_dir_path( __FILE__ ) );
            define( 'WJ_FILMS_FILTER_URL', plugin_dir_url( __FILE__ ) );
            define( 'WJ_FILMS_FILTER_VERSION', '1.0.0' );
        }

        public function my_templates_array() {

            $temps = [];
            $temps['films-template.php'] = 'Films Template';
            return $temps;
        }

        public function my_template_register( $page_templates, $theme, $post ) {

            $templates = $this->my_templates_array();
            foreach( $templates as $tk => $tv ) {
                $page_templates[$tk] = $tv;
            }
            return $page_templates; 
        }

        public function load_template( $template ) {

            global $post, $wp_query, $wpdb;
            $page_temp_slug = get_page_template_slug( $post->ID );
            $templates = $this->my_templates_array();
            if( isset( $templates[$page_temp_slug] ) ) {
                $template = WJ_FILMS_FILTER_PATH . 'templates/' .  $page_temp_slug;
            }
            return $template;
        }

        public function enqueue_scripts() {

            wp_enqueue_style( 'wj-films-filter-style',WJ_FILMS_FILTER_URL . 'assets/frontend/style.css', array(), WJ_FILMS_FILTER_VERSION, 'all' );
            wp_enqueue_script( 'wj-films-filter-js', WJ_FILMS_FILTER_URL . 'assets/frontend/script.js', array('jquery'), WJ_FILMS_FILTER_VERSION, true );
            wp_localize_script( 'wj-films-filter-js', 'VARS', array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            ) );
        }

        public function filter_ajax() {

            $args = array(
                'post_type' => 'films',
                'posts_per_page' => -1,
                'status' => 'publish'
            );

            $title = $_POST['movie-title'];
            $type = $_POST['cat'];
            $tags = $_POST['films-keywords'];

            if( ! empty( $title ) ) {
                $args['s'] = $title;
            }

            if( ! empty( $type ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'film_cat',
                    'field' => 'slug',
                    'terms' => array($type)
                );
            }
            if( ! empty( $tags ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'film_keywords',
                    'field' => 'slug',
                    'terms' => $tags
                );
            }
            $films = new WP_Query( $args );
            if( $films->have_posts() ) : ?>
                <div class="wj-films">
                    <?php while( $films->have_posts() ) : $films->the_post(); ?>
                    <article class="film">
                        <?php if( has_post_thumbnail() ) { ?>
                            <picture><a href="<?php the_permalink(); ?>"><img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" class="img-fluid"></a></picture>
                        <?php } ?>
                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <?php $cats = get_the_terms( get_the_ID(), 'film_cat' ); 
                            if( ! empty( $cats ) ) {
                                foreach( $cats as $cat ) { ?>
                                    <span><b>Category:</b> <a href="<?php echo get_term_link( $cat, 'film_cat' ); ?>"><?php echo $cat->name; ?></a></span>
                                <?php }
                            }
                        ?>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php endif; wp_die();
                
        }
        

        public static function activate() {

            update_option( 'rewrite_rules', '' );
        }

        public static function deactivate() {

            flush_rewrite_rules();
            unregister_post_type( 'films' );
        }

        public static function uninstall() {
            
        }
    }
}


if( class_exists( 'Wassim_Films_Filter' ) ) {

    register_activation_hook( __FILE__, array( 'Wassim_Films_Filter', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'Wassim_Films_Filter', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'Wassim_Films_Filter', 'uninstall' ) );

    $wassim_films_filter = new Wassim_Films_Filter();
}

?>