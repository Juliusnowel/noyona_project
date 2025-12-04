<?php
/**
 * Hero Banner block render.
 *
 * Provides a left-text, right-image hero layout with a gentle gradient overlay.
 *
 * @param array $attributes Block attributes.
 */

$defaults = array(
    'eyebrow'         => '',
    'titleLine1'      => 'Radiate Natural,',
    'titleLine2'      => 'Timeless Confidence.',
    'body'            => 'Noyona celebrates beauty that feels like you elevated by nature, crafted for everyday wear, and made to last. Discover effortless formulas that care for skin while enhancing your glow.',
    'buttonText'      => 'Discover Your Glow',
    'buttonUrl'       => '/shop/',
    'backgroundImage' => '',
);

$atts = wp_parse_args( $attributes, $defaults );

$bg_image = $atts['backgroundImage'];

if ( empty( $bg_image ) ) {
    $bg_image = get_stylesheet_directory_uri() . '/assets/images/makeup.jpg';
}

?>
<section
    class="wp-block-noyona-hero-banner hero-banner alignfull"
    style="--hero-banner-bg: url('<?php echo esc_url( $bg_image ); ?>');"
>
    <div class="hero-banner__inner">
        <div class="hero-banner__content">
            <?php if ( ! empty( $atts['eyebrow'] ) ) : ?>
                <p class="hero-banner__eyebrow"><?php echo esc_html( $atts['eyebrow'] ); ?></p>
            <?php endif; ?>

            <h1 class="hero-banner__title">
                <span class="hero-banner__title-line">
                    <?php
                    echo wp_kses(
                        $atts['titleLine'],
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                            'em'   => array(),
                            'strong' => array(),
                        )
                    );
                    ?>
                </span>
            </h1>

            <p class="hero-banner__body">
                <?php echo esc_html( $atts['body'] ); ?>
            </p>

            <?php if ( ! empty( $atts['buttonText'] ) && ! empty( $atts['buttonUrl'] ) ) : ?>
                <a class="hero-banner__cta" href="<?php echo esc_url( $atts['buttonUrl'] ); ?>">
                    <?php echo esc_html( $atts['buttonText'] ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
