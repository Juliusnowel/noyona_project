<?php
// Load parent + child styles and our custom assets
add_action( 'wp_enqueue_scripts', 'woocom_ct_enqueue_assets' );
function woocom_ct_enqueue_assets() {
    // Web fonts (free Google fonts)
    wp_enqueue_style(
        'noyona-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Noto+Serif+SemiCondensed:ital,wght@0,400;0,600;0,700;1,400;1,600&display=swap',
        array(),
        null
    );

    // Parent theme CSS
    wp_enqueue_style(
        'twentytwentyfive-parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // Child theme CSS
    wp_enqueue_style(
        'woocom-ct-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'twentytwentyfive-parent-style' ),
        wp_get_theme()->get( 'Version' )
    );

    // Header CSS (assets/css/header.css)
    wp_enqueue_style(
        'woocom-ct-header',
        get_stylesheet_directory_uri() . '/assets/css/header.css',
        array( 'woocom-ct-style' ),
        wp_get_theme()->get( 'Version' )
    );

    // Product-gatherer CSS
    wp_enqueue_style(
        'woocom-ct-product-gatherer',
        get_stylesheet_directory_uri() . '/assets/css/product-gatherer.css',
        array( 'woocom-ct-style', 'woocom-ct-header' ),
        wp_get_theme()->get( 'Version' )
    );

    // Font Awesome (for icons, hearts, cart, etc.)
    wp_enqueue_style(
        'font-awesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        array(),
        '6.5.2'
    );

    // Header behavior (sticky / color change / wishlist toggle)
    wp_enqueue_script(
        'woocom-ct-header',
        get_stylesheet_directory_uri() . '/assets/js/header.js',
        array(),
        wp_get_theme()->get( 'Version' ),
        true
    );

    // Product-gatherer JS
    wp_enqueue_script(
        'woocom-ct-product-gatherer',
        get_stylesheet_directory_uri() . '/assets/js/product-gatherer.js',
        array(),
        wp_get_theme()->get( 'Version' ),
        true
    );
}

// Make sure theme declares WooCommerce support
add_action( 'after_setup_theme', function() {
    add_theme_support( 'woocommerce' );
} );

// Shortcode: [product_gatherer]
add_shortcode( 'product_gatherer', 'woocom_ct_product_gatherer_shortcode' );
function woocom_ct_product_gatherer_shortcode( $atts ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return '<p>WooCommerce is not active.</p>';
    }

    // Read filters from query string
    $search = isset( $_GET['pg_search'] ) ? sanitize_text_field( wp_unslash( $_GET['pg_search'] ) ) : '';
    $sort   = isset( $_GET['pg_sort'] ) ? sanitize_text_field( wp_unslash( $_GET['pg_sort'] ) ) : 'default';
    $cat    = isset( $_GET['pg_cat'] )  ? sanitize_text_field( wp_unslash( $_GET['pg_cat'] ) )  : '';

    // Sorting logic
    $orderby  = 'title';
    $order    = 'ASC';
    $meta_key = '';

    switch ( $sort ) {
        case 'price_asc':
            $orderby  = 'meta_value_num';
            $order    = 'ASC';
            $meta_key = '_price';
            break;
        case 'price_desc':
            $orderby  = 'meta_value_num';
            $order    = 'DESC';
            $meta_key = '_price';
            break;
        case 'latest':
            $orderby  = 'date';
            $order    = 'DESC';
            break;
        default:
            // title ASC
            break;
    }

    $paged = isset( $_GET['pg_page'] ) ? max( 1, (int) $_GET['pg_page'] ) : 1;

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12, // 4 x 3 cards per page
        'paged'          => $paged,
        'orderby'        => $orderby,
        'order'          => $order,
    );

    if ( $meta_key ) {
        $args['meta_key'] = $meta_key;
    }

    if ( $search ) {
        $args['s'] = $search;
    }

    if ( $cat ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat,
            ),
        );
    }

    $query = new WP_Query( $args );

    // Get categories for dropdown
    $categories = get_terms(
        array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
        )
    );

    ob_start();
    ?>

    <div class="pg-wrapper">
        <div class="pg-header">
            <div>
                <h2 class="pg-title">Products</h2>
                <p class="pg-subtitle">Discover our curated beauty products.</p>
            </div>

            <form class="pg-toolbar-form" method="get">
                <div class="pg-toolbar">

                    <div class="pg-search">
                        <input
                            type="text"
                            name="pg_search"
                            value="<?php echo esc_attr( $search ); ?>"
                            placeholder="Search products (e.g. lipstick)"
                        />
                        <button type="submit" class="pg-search-btn">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>

                    <div class="pg-filters">
                        <select name="pg_sort" class="pg-select">
                            <option value="default" <?php selected( $sort, 'default' ); ?>>Sort: Default</option>
                            <option value="latest" <?php selected( $sort, 'latest' ); ?>>Newest</option>
                            <option value="price_asc" <?php selected( $sort, 'price_asc' ); ?>>Price: Low to High</option>
                            <option value="price_desc" <?php selected( $sort, 'price_desc' ); ?>>Price: High to Low</option>
                        </select>

                        <select name="pg_cat" class="pg-select">
                            <option value="">All categories</option>
                            <?php foreach ( $categories as $category ) : ?>
                                <option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $cat, $category->slug ); ?>>
                                    <?php echo esc_html( $category->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="pg-grid">
            <?php
            if ( $query->have_posts() ) :
                while ( $query->have_posts() ) :
                    $query->the_post();
                    $product = wc_get_product( get_the_ID() );
                    if ( ! $product ) {
                        continue;
                    }

                    $image_url    = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                    $price_html   = $product->get_price_html();
                    $rating_html  = wc_get_rating_html( $product->get_average_rating() );
                    $add_url      = esc_url( $product->add_to_cart_url() );
                    $add_text     = esc_html( $product->add_to_cart_text() );
                    $is_on_sale   = $product->is_on_sale();
                    ?>
                    <article class="pg-card">
                        <div class="pg-card-media">
                            <?php if ( $image_url ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
                                </a>
                            <?php endif; ?>

                            <?php if ( $is_on_sale ) : ?>
                                <span class="pg-badge-sale">SALE</span>
                            <?php endif; ?>

                            <button class="pg-wishlist-btn" type="button">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>

                        <div class="pg-card-body">
                            <h3 class="pg-product-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <div class="pg-rating">
                                <?php echo $rating_html ? $rating_html : '<span class="pg-rating-placeholder">No reviews yet</span>'; ?>
                            </div>

                            <p class="pg-excerpt">
                                <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 16 ) ); ?>
                            </p>

                            <div class="pg-card-footer">
                                <div class="pg-price">
                                    <?php echo wp_kses_post( $price_html ); ?>
                                </div>
                                <a
                                    href="<?php echo $add_url; ?>"
                                    data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                                    class="pg-add-to-cart-button add_to_cart_button ajax_add_to_cart"
                                >
                                    <i class="fa fa-shopping-cart"></i>
                                    <?php echo $add_text; ?>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
            else :
                ?>
                <p class="pg-no-results">No products found for your filters.</p>
                <?php
            endif;
            ?>
        </div>

        <?php if ( $query->max_num_pages > 1 ) : ?>
            <div class="pg-pagination">
                <?php
                echo paginate_links(
                    array(
                        'current' => $paged,
                        'total'   => $query->max_num_pages,
                        'format'  => '&pg_page=%#%',
                        'type'    => 'list',
                    )
                );
                ?>
            </div>
        <?php endif; ?>

    </div><!-- .pg-wrapper -->

    <?php
    wp_reset_postdata();

    return ob_get_clean();
}

// Register custom blocks
add_action( 'init', 'woocom_ct_register_blocks' );
function woocom_ct_register_blocks() {
    register_block_type( get_stylesheet_directory() . '/blocks/search-expand' );
    register_block_type( get_stylesheet_directory() . '/blocks/hero-banner' );
    register_block_type( get_stylesheet_directory() . '/blocks/brand-carousel' );
    register_block_type( get_stylesheet_directory() . '/blocks/color-swatches' );
    register_block_type( get_stylesheet_directory() . '/blocks/product-highlight' );
    register_block_type( get_stylesheet_directory() . '/blocks/collection-grid' );
    register_block_type( get_stylesheet_directory() . '/blocks/phone-video-reviews' );
    register_block_type( get_stylesheet_directory() . '/blocks/discover-posts-carousel' );
}
