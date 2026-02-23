<?php
/**
 * Plugin Name:       DVC Testimonials Manager
 * Plugin URI:        https://digitalvisibilityconcepts.com
 * Description:       A complete testimonials management system. Register a Custom Post Type,
 *                    add custom meta fields, and display testimonials via [testimonials] shortcode.
 * Version:           1.0.0
 * Author:            Web Development Intern
 * Author URI:        https://example.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dvc-testimonials
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Tested up to:      6.4
 * Requires PHP:      7.4
 *
 * @package DVC_Testimonials
 */

// Exit if accessed directly (security hardening).
if (!defined('ABSPATH')) {
    exit;
}


// -----------------------------------------------------------------
// PART A: Register Custom Post Type — Testimonials
// -----------------------------------------------------------------

/**
 * Register the 'dvc_testimonial' custom post type.
 * Hooked into 'init' to ensure WordPress is fully loaded.
 */
function dvc_register_testimonials_cpt()
{

    // Human-friendly labels shown in the WordPress admin.
    $labels = array(
        'name' => _x('Testimonials', 'Post type general name', 'dvc-testimonials'),
        'singular_name' => _x('Testimonial', 'Post type singular name', 'dvc-testimonials'),
        'menu_name' => _x('Testimonials', 'Admin Menu text', 'dvc-testimonials'),
        'name_admin_bar' => _x('Testimonial', 'Add New on Toolbar', 'dvc-testimonials'),
        'add_new' => __('Add New', 'dvc-testimonials'),
        'add_new_item' => __('Add New Testimonial', 'dvc-testimonials'),
        'new_item' => __('New Testimonial', 'dvc-testimonials'),
        'edit_item' => __('Edit Testimonial', 'dvc-testimonials'),
        'view_item' => __('View Testimonial', 'dvc-testimonials'),
        'all_items' => __('All Testimonials', 'dvc-testimonials'),
        'search_items' => __('Search Testimonials', 'dvc-testimonials'),
        'not_found' => __('No testimonials found.', 'dvc-testimonials'),
        'not_found_in_trash' => __('No testimonials found in Trash.', 'dvc-testimonials'),
        'featured_image' => __('Client Photo', 'dvc-testimonials'),
        'set_featured_image' => __('Set client photo', 'dvc-testimonials'),
        'remove_featured_image' => __('Remove client photo', 'dvc-testimonials'),
        'use_featured_image' => __('Use as client photo', 'dvc-testimonials'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'testimonials'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 25,

        // Dashicons: speech bubble icon for testimonials.
        'menu_icon' => 'dashicons-format-quote',

        // Enable title, editor (testimonial body), and featured image (client photo).
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),

        // Gutenberg / Block Editor support (WordPress 5.0+).
        'show_in_rest' => true,
    );

    register_post_type('dvc_testimonial', $args);
}
add_action('init', 'dvc_register_testimonials_cpt');

// -----------------------------------------------------------------
// PART B: Custom Meta Box & Fields
// -----------------------------------------------------------------

/**
 * Register the custom meta box for testimonial client details.
 * Displayed on the testimonial edit screen in admin.
 */
function dvc_add_testimonial_meta_box()
{
    add_meta_box(
        'dvc_testimonial_details',            // Unique ID.
        __('Client Details', 'dvc-testimonials'), // Box title.
        'dvc_render_testimonial_meta_box',    // Callback to render HTML.
        'dvc_testimonial',                    // Post type.
        'normal',                             // Context: normal (below editor).
        'high'                                // Priority: show near the top.
    );
}
add_action('add_meta_boxes', 'dvc_add_testimonial_meta_box');

/**
 * Render the meta box HTML fields.
 * Includes a nonce field for security.
 *
 * @param WP_Post $post Current post object.
 */
function dvc_render_testimonial_meta_box($post)
{

    // Security nonce — verified on save.
    wp_nonce_field('dvc_save_testimonial_meta', 'dvc_testimonial_nonce');

    // Retrieve existing values (empty string as default).
    $client_name = get_post_meta($post->ID, '_dvc_client_name', true);
    $client_position = get_post_meta($post->ID, '_dvc_client_position', true);
    $company_name = get_post_meta($post->ID, '_dvc_company_name', true);
    $rating = get_post_meta($post->ID, '_dvc_rating', true);

    // Use 5 as the default rating if none saved yet.
    $rating = $rating ? absint($rating) : 5;
    ?>

    <style>
        /* Minimal admin styles — scoped to this meta box. */
        #dvc_testimonial_details .dvc-field-row {
            margin-bottom: 1rem;
        }

        #dvc_testimonial_details label {
            display: block;
            font-weight: 600;
            margin-bottom: .3rem;
        }

        #dvc_testimonial_details input[type="text"],
        #dvc_testimonial_details select {
            width: 100%;
            max-width: 500px;
        }

        #dvc_testimonial_details .dvc-required {
            color: #d63638;
        }
    </style>

    <div class="dvc-field-row">
        <label for="dvc_client_name">
            <?php esc_html_e('Client Name', 'dvc-testimonials'); ?>
            <span class="dvc-required" aria-label="required">*</span>
        </label>
        <input type="text" id="dvc_client_name" name="dvc_client_name" value="<?php echo esc_attr($client_name); ?>"
            placeholder="<?php esc_attr_e('e.g. Jane Smith', 'dvc-testimonials'); ?>" required aria-required="true"
            class="regular-text" />
    </div>

    <div class="dvc-field-row">
        <label for="dvc_client_position">
            <?php esc_html_e('Client Position / Title', 'dvc-testimonials'); ?>
        </label>
        <input type="text" id="dvc_client_position" name="dvc_client_position"
            value="<?php echo esc_attr($client_position); ?>"
            placeholder="<?php esc_attr_e('e.g. Marketing Director', 'dvc-testimonials'); ?>" class="regular-text" />
    </div>

    <div class="dvc-field-row">
        <label for="dvc_company_name">
            <?php esc_html_e('Company Name', 'dvc-testimonials'); ?>
        </label>
        <input type="text" id="dvc_company_name" name="dvc_company_name" value="<?php echo esc_attr($company_name); ?>"
            placeholder="<?php esc_attr_e('e.g. Acme Corp', 'dvc-testimonials'); ?>" class="regular-text" />
    </div>

    <div class="dvc-field-row">
        <label for="dvc_rating">
            <?php esc_html_e('Rating', 'dvc-testimonials'); ?>
        </label>
        <select id="dvc_rating" name="dvc_rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?php echo esc_attr($i); ?>" <?php selected($rating, $i); ?>>
                    <?php
                    echo esc_html(
                        str_repeat('★', $i) . str_repeat('☆', 5 - $i) . ' (' . $i . ' star' . ($i > 1 ? 's' : '') . ')'
                    );
                    ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <?php
}

/**
 * Save testimonial meta data when the post is saved.
 * Validates nonce, checks permissions, sanitizes all input.
 *
 * @param int $post_id The ID of the post being saved.
 */
function dvc_save_testimonial_meta($post_id)
{

    // 1. Verify nonce — prevents CSRF attacks.
    if (
        !isset($_POST['dvc_testimonial_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dvc_testimonial_nonce'])), 'dvc_save_testimonial_meta')
    ) {
        return;
    }

    // 2. Skip auto-saves to prevent unintended data loss.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 3. Check user capability.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 4. Sanitize and save each field.
    // Client Name — required; do not save empty value.
    if (isset($_POST['dvc_client_name'])) {
        $client_name = sanitize_text_field(wp_unslash($_POST['dvc_client_name']));
        if (!empty($client_name)) {
            update_post_meta($post_id, '_dvc_client_name', $client_name);
        }
    }

    if (isset($_POST['dvc_client_position'])) {
        update_post_meta(
            $post_id,
            '_dvc_client_position',
            sanitize_text_field(wp_unslash($_POST['dvc_client_position']))
        );
    }

    if (isset($_POST['dvc_company_name'])) {
        update_post_meta(
            $post_id,
            '_dvc_company_name',
            sanitize_text_field(wp_unslash($_POST['dvc_company_name']))
        );
    }

    // Rating — enforce integer between 1 and 5.
    if (isset($_POST['dvc_rating'])) {
        $rating = absint($_POST['dvc_rating']);
        $rating = max(1, min(5, $rating));
        update_post_meta($post_id, '_dvc_rating', $rating);
    }
}
add_action('save_post_dvc_testimonial', 'dvc_save_testimonial_meta');

// -----------------------------------------------------------------
// PART C & D: [testimonials] Shortcode
// -----------------------------------------------------------------

/**
 * Register the [testimonials] shortcode.
 * Supports parameters: count, orderby, order.
 * Example: [testimonials count="3" orderby="date" order="DESC"]
 */
function dvc_testimonials_shortcode($atts)
{

    // Set default attributes and parse user-supplied ones.
    $atts = shortcode_atts(
        array(
            'count' => -1,       // -1 = all testimonials.
            'orderby' => 'date',   // WordPress default.
            'order' => 'DESC',   // Newest first.
        ),
        $atts,
        'testimonials'
    );

    // Sanitize shortcode attributes.
    $count = intval($atts['count']);
    $orderby = sanitize_key($atts['orderby']);
    $order = in_array(strtoupper($atts['order']), array('ASC', 'DESC'), true)
        ? strtoupper($atts['order'])
        : 'DESC';

    // Validate orderby to allowed values only.
    $allowed_orderby = array('date', 'title', 'rand', 'menu_order', 'modified');
    if (!in_array($orderby, $allowed_orderby, true)) {
        $orderby = 'date';
    }

    // Query testimonials.
    $query_args = array(
        'post_type' => 'dvc_testimonial',
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'orderby' => $orderby,
        'order' => $order,
    );

    $testimonials_query = new WP_Query($query_args);

    // If no testimonials found, show a graceful message.
    if (!$testimonials_query->have_posts()) {
        return '<p class="dvc-no-testimonials">' . esc_html__('No testimonials found.', 'dvc-testimonials') . '</p>';
    }

    // Build output — use output buffering to keep HTML readable.
    ob_start();
    ?>
    /* ---------- Testimonials Slider ---------- */
    .dvc-testimonials-wrapper {
    position: relative;
    max-width: 860px;
    margin: 2rem auto;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .dvc-slider-track {
    overflow: hidden;
    border-radius: 16px;
    }

    .dvc-slides {
    display: flex;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform;
    }

    /* ---------- Individual Slide ---------- */
    .dvc-slide {
    min-width: 100%;
    box-sizing: border-box;
    padding: 2.5rem;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(108, 99, 255, 0.1);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    }

    /* ---------- Client Photo ---------- */
    .dvc-client-photo {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #6c63ff;
    flex-shrink: 0;
    }

    .dvc-client-photo-placeholder {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6c63ff, #ff6584);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: #fff;
    flex-shrink: 0;
    }

    /* ---------- Stars ---------- */
    .dvc-stars {
    color: #ffc107;
    font-size: 1.1rem;
    letter-spacing: 0.05em;
    }

    .dvc-stars .dvc-star-empty {
    color: #d0d0d0;
    }

    /* ---------- Testimonial Text ---------- */
    .dvc-testimonial-text {
    font-size: 1rem;
    color: #4a4a6a;
    line-height: 1.75;
    font-style: italic;
    position: relative;
    padding-left: 1.25rem;
    }

    .dvc-testimonial-text::before {
    content: '"';
    position: absolute;
    left: 0;
    top: -0.2rem;
    font-size: 2rem;
    color: #6c63ff;
    font-style: normal;
    line-height: 1;
    }

    /* ---------- Client Info ---------- */
    .dvc-slide-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    }

    .dvc-client-meta strong {
    display: block;
    font-size: 1rem;
    color: #1a1a2e;
    font-weight: 700;
    }

    .dvc-client-meta span {
    font-size: 0.85rem;
    color: #8888aa;
    }

    /* ---------- Navigation Buttons ---------- */
    .dvc-nav {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
    }

    .dvc-nav-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 2px solid #6c63ff;
    background: #fff;
    color: #6c63ff;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.25s, color 0.25s;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .dvc-nav-btn:hover {
    background: #6c63ff;
    color: #fff;
    }

    .dvc-nav-btn:disabled {
    border-color: #d0d0d0;
    color: #d0d0d0;
    cursor: not-allowed;
    }

    .dvc-counter {
    font-size: 0.875rem;
    color: #8888aa;
    min-width: 60px;
    text-align: center;
    }

    /* ---------- Dots Indicator ---------- */
    .dvc-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1rem;
    }

    .dvc-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d0d0d0;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: background 0.25s, transform 0.25s;
    }

    .dvc-dot.active {
    background: #6c63ff;
    transform: scale(1.35);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 600px) {
    .dvc-slide {
    padding: 1.5rem;
    }
    .dvc-slide-header {
    flex-direction: column;
    text-align: center;
    }
    .dvc-testimonial-text {
    font-size: 0.9rem;
    }
    }
    </style>

    <section class="dvc-testimonials-wrapper" aria-label="<?php esc_attr_e('Client Testimonials', 'dvc-testimonials'); ?>">
        <!-- Slider Track -->
        <div class="dvc-slider-track" role="region" aria-live="polite" aria-atomic="true">
            <div class="dvc-slides" id="dvc-slides-<?php echo esc_attr(wp_unique_id()); ?>">

                <?php
                $slide_index = 0;
                while ($testimonials_query->have_posts()):
                    $testimonials_query->the_post();

                    // Retrieve custom fields.
                    $client_name = get_post_meta(get_the_ID(), '_dvc_client_name', true);
                    $client_position = get_post_meta(get_the_ID(), '_dvc_client_position', true);
                    $company_name = get_post_meta(get_the_ID(), '_dvc_company_name', true);
                    $rating = absint(get_post_meta(get_the_ID(), '_dvc_rating', true));
                    $rating = max(1, min(5, $rating ? $rating : 5));

                    // Build star rating HTML.
                    $stars_html = '';
                    for ($s = 1; $s <= 5; $s++) {
                        if ($s <= $rating) {
                            $stars_html .= '<span aria-hidden="true">★</span>';
                        } else {
                            $stars_html .= '<span class="dvc-star-empty" aria-hidden="true">★</span>';
                        }
                    }
                    $aria_rating = sprintf(
                        /* translators: %d: star rating number */
                        _n('%d star rating', '%d star rating', $rating, 'dvc-testimonials'),
                        $rating
                    );

                    // Fallback label for initials avatar.
                    $initials = !empty($client_name)
                        ? strtoupper(substr($client_name, 0, 1))
                        : '?';
                    ?>

                    <article class="dvc-slide"
                        aria-label="<?php echo esc_attr(sprintf(__('Testimonial %d', 'dvc-testimonials'), $slide_index + 1)); ?>"
                        role="group">
                        <!-- Client Header -->
                        <header class="dvc-slide-header">
                            <?php if (has_post_thumbnail()): ?>
                                <img class="dvc-client-photo"
                                    src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'thumbnail')); ?>"
                                    alt="<?php echo esc_attr($client_name); ?>" width="72" height="72" />
                            <?php else: ?>
                                <div class="dvc-client-photo-placeholder" aria-hidden="true">
                                    <?php echo esc_html($initials); ?>
                                </div>
                            <?php endif; ?>

                            <div class="dvc-client-meta">
                                <strong><?php echo esc_html($client_name ?: get_the_title()); ?></strong>
                                <span>
                                    <?php
                                    $pos_parts = array_filter(array($client_position, $company_name));
                                    echo esc_html(implode(', ', $pos_parts));
                                    ?>
                                </span>
                            </div>
                        </header>

                        <!-- Star Rating -->
                        <div class="dvc-stars" aria-label="<?php echo esc_attr($aria_rating); ?>">
                            <?php echo wp_kses($stars_html, array('span' => array('aria-hidden' => true, 'class' => true))); ?>
                        </div>

                        <!-- Testimonial Text -->
                        <blockquote class="dvc-testimonial-text">
                            <?php the_content(); ?>
                        </blockquote>
                    </article>

                    <?php
                    $slide_index++;
                endwhile;
                wp_reset_postdata();
                $total_slides = $slide_index;
                ?>

            </div><!-- .dvc-slides -->
        </div><!-- .dvc-slider-track -->

        <!-- Navigation Controls -->
        <?php if ($total_slides > 1): ?>
            <nav class="dvc-nav" aria-label="<?php esc_attr_e('Testimonial navigation', 'dvc-testimonials'); ?>">
                <button class="dvc-nav-btn dvc-prev"
                    aria-label="<?php esc_attr_e('Previous testimonial', 'dvc-testimonials'); ?>" disabled>&#8592;</button>
                <span class="dvc-counter" aria-live="polite">1 / <?php echo esc_html($total_slides); ?></span>
                <button class="dvc-nav-btn dvc-next"
                    aria-label="<?php esc_attr_e('Next testimonial', 'dvc-testimonials'); ?>">&#8594;</button>
            </nav>

            <!-- Dot Indicators -->
            <div class="dvc-dots" role="group" aria-label="<?php esc_attr_e('Go to testimonial', 'dvc-testimonials'); ?>">
                <?php for ($d = 0; $d < $total_slides; $d++): ?>
                    <button class="dvc-dot<?php echo 0 === $d ? ' active' : ''; ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Testimonial %d', 'dvc-testimonials'), $d + 1)); ?>"
                        data-index="<?php echo esc_attr($d); ?>"></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </section>

    <?php

    return ob_get_clean();
}
add_shortcode('testimonials', 'dvc_testimonials_shortcode');

// -----------------------------------------------------------------
// Plugin Activation: Flush rewrite rules once to register the CPT slug.
// -----------------------------------------------------------------

/**
 * On plugin activation, flush rewrite rules so the CPT permalink works.
 * Called once on activation — do NOT call on every request.
 */
function dvc_testimonials_activate()
{
    dvc_register_testimonials_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'dvc_testimonials_activate');

/**
 * On deactivation, flush rewrite rules to clean up CPT slugs.
 */
function dvc_testimonials_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'dvc_testimonials_deactivate');
