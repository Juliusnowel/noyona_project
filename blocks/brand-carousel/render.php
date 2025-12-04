<?php
/**
 * Brand / Top Reviews Carousel Template.
 */

$defaults = array(
    'items'     => array(),
    'speed'     => 40,
    'heading'   => 'Top Reviews',
    'mode'      => 'reviews', // "reviews" or "brands"
    'minRating' => 0,
);

$atts    = wp_parse_args( $attributes, $defaults );
$items   = $atts['items'];
$heading = $atts['heading'];
$mode    = $atts['mode'];
$minRating = (float) $atts['minRating'];

if ( empty( $items ) ) {
    if ( is_admin() ) {
        echo '<div class="brand-carousel-placeholder">Add items to the carousel via the sidebar.</div>';
    }
    return;
}

/**
 * If we’re in "reviews" mode and minRating > 0,
 * filter to only top-rated items.
 */
if ( 'reviews' === $mode && $minRating > 0 ) {
    $items = array_filter(
        $items,
        function ( $item ) use ( $minRating ) {
            if ( ! isset( $item['rating'] ) ) {
                return false;
            }
            return (float) $item['rating'] >= $minRating;
        }
    );
}

if ( empty( $items ) ) {
    return;
}

// Duplicate items to ensure smooth infinite scroll
$display_items = array_merge( $items, $items, $items, $items );
?>
<div class="wp-block-noyona-brand-carousel brand-carousel alignfull">

    <?php if ( $heading ) : ?>
        <div class="brand-carousel__header">
            <h2 class="brand-carousel__heading"><?php echo esc_html( $heading ); ?></h2>
        </div>
    <?php endif; ?>

    <div class="brand-carousel__track" style="--scroll-speed: <?php echo intval( $atts['speed'] ); ?>s;">
        <?php foreach ( $display_items as $item ) : ?>
            <?php
            $quote   = isset( $item['quote'] )   ? $item['quote']   : '';
            $author  = isset( $item['author'] )  ? $item['author']  : '';
            $product = isset( $item['product'] ) ? $item['product'] : '';
            $rating  = isset( $item['rating'] )  ? (float) $item['rating'] : 0;
            $avatar  = isset( $item['avatar'] )  ? $item['avatar']  : '';

            // For brand mode
            $brandName = isset( $item['brand'] ) ? $item['brand'] : '';
            $logo      = isset( $item['logo'] )  ? $item['logo']  : '';
            ?>

            <?php if ( 'brands' === $mode ) : ?>

                <!-- BRAND / PARTNER ITEM -->
                <div class="brand-carousel__item">
                    <div class="brand-carousel__image-wrapper">
                        <?php if ( $logo ) : ?>
                            <img class="brand-carousel__image"
                                 src="<?php echo esc_url( $logo ); ?>"
                                 alt="<?php echo esc_attr( $brandName ?: 'Partner/Brand' ); ?>" />
                        <?php endif; ?>
                    </div>
                    <div class="brand-carousel__text">
                        <?php echo esc_html( $brandName ?: 'PARTNER/BRAND' ); ?>
                    </div>
                </div>

            <?php else : ?>

                <!-- REVIEW CARD ITEM -->
                <div class="review-card">
                    <div class="review-card__quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>

                    <p class="review-card__text">
                        <?php echo esc_html( $quote ); ?>
                    </p>

                    <div class="review-card__rating">
                        <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                            <?php if ( $i < round( $rating ) ) : ?>
                                <i class="fas fa-star"></i>
                            <?php else : ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <div class="review-card__footer">
                        <div class="review-card__avatar">
                            <?php if ( $avatar ) : ?>
                                <img src="<?php echo esc_url( $avatar ); ?>" alt="User avatar" />
                            <?php else : ?>
                                <div class="review-card__avatar-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <div class="review-card__meta">
                            <span class="review-card__author"><?php echo esc_html( $author ); ?></span>
                            <span class="review-card__product"><?php echo esc_html( $product ); ?></span>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        <?php endforeach; ?>
    </div>
</div>
