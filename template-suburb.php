<?php
/**
 * Template Name: SEO Suburb Landing Page
 * Description: Premium, SEO-optimized landing page template matching Nova Care Australia purple brand theme and fixing header positioning.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Safely load suburb data
if ( file_exists( get_template_directory() . '/includes/suburb-data.php' ) ) {
    require_once get_template_directory() . '/includes/suburb-data.php';
}

// Determine current suburb safely
global $post;
$current_suburb_key = '';

if ( is_object( $post ) && isset( $post->ID ) ) {
    $current_suburb_key = get_post_meta( $post->ID, '_suburb_key', true );
}

if ( empty( $current_suburb_key ) ) {
    $request_uri = trim( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
    $segments = explode( '/', $request_uri );
    $current_slug = end( $segments );
    
    if ( function_exists( 'novacare_get_suburbs_data' ) ) {
        $all_suburbs = novacare_get_suburbs_data();
        foreach ( $all_suburbs as $key => $sub ) {
            if ( $sub['slug'] === $current_slug || $key === $current_slug || ( is_object( $post ) && isset( $post->post_name ) && strpos( $post->post_name, $key ) !== false ) ) {
                $current_suburb_key = $key;
                break;
            }
        }
    }
}

if ( empty( $current_suburb_key ) ) {
    $current_suburb_key = 'sutherland'; // Default fallback
}

$suburb = function_exists( 'novacare_get_suburb_data' ) ? novacare_get_suburb_data( $current_suburb_key ) : false;
$all_suburbs = function_exists( 'novacare_get_suburbs_data' ) ? novacare_get_suburbs_data() : array();

if ( ! $suburb && ! empty( $all_suburbs ) ) {
    $suburb = reset( $all_suburbs );
}

$phone_display = $suburb['phone'] ?? '0426 142 162';
$phone_tel = preg_replace( '/[^0-9+]/', '', $phone_display );

get_header();

do_action( 'ava_page_template_after_header' );
do_action( 'ava_after_main_title' );
?>

<style>
/* Nova Care Purple & Emerald Brand Theme Styles */
:root {
    --nc-purple-dark: #3B0764;
    --nc-purple-main: #6B21A8;
    --nc-purple-light: #7C3AED;
    --nc-purple-soft: #F3E8FF;
    --nc-emerald: #10B981;
    --nc-emerald-hover: #059669;
    --nc-gray-light: #F9FAFB;
    --nc-gray-border: #E5E7EB;
    --nc-text-dark: #1F2937;
    --nc-text-muted: #6B7280;
}

.nc-suburb-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    color: var(--nc-text-dark);
    line-height: 1.6;
    background-color: #FFFFFF;
    width: 100%;
    clear: both;
    position: relative;
    z-index: 1;
}

.nc-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Brand Hero Section (Purple Gradient) */
.nc-hero {
    background: linear-gradient(135deg, #3B0764 0%, #6B21A8 50%, #7C3AED 100%);
    color: #FFFFFF;
    padding: 65px 0 75px;
    position: relative;
    overflow: hidden;
}

.nc-hero::after {
    content: '';
    position: absolute;
    bottom: -60px;
    right: -60px;
    width: 380px;
    height: 380px;
    background: rgba(16, 185, 129, 0.2);
    border-radius: 50%;
    filter: blur(60px);
    pointer-events: none;
}

.nc-hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(10px);
    color: #FFFFFF;
    padding: 6px 18px;
    border-radius: 30px;
    font-size: 0.92rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.nc-hero h1 {
    font-size: 2.75rem;
    font-weight: 800;
    line-height: 1.25;
    margin-bottom: 20px;
    color: #FFFFFF !important;
}

.nc-hero-sub {
    font-size: 1.2rem;
    color: #F3E8FF;
    max-width: 800px;
    margin-bottom: 30px;
    font-weight: 400;
    line-height: 1.5;
}

.nc-trust-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 35px;
}

.nc-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.12);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #F9FAFB;
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.nc-hero-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.nc-btn-emerald {
    background-color: var(--nc-emerald);
    color: #FFFFFF !important;
    padding: 14px 32px;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
    border: none;
    cursor: pointer;
}

.nc-btn-emerald:hover {
    background-color: var(--nc-emerald-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
}

.nc-btn-secondary {
    background: transparent;
    color: #FFFFFF !important;
    padding: 14px 32px;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none !important;
    border: 2px solid rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.nc-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: #FFFFFF;
}

/* Services Grid */
.nc-section {
    padding: 70px 0;
}

.nc-section-title {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 50px;
}

.nc-section-title h2 {
    font-size: 2.2rem;
    color: var(--nc-purple-dark);
    font-weight: 700;
    margin-bottom: 15px;
}

.nc-section-title p {
    font-size: 1.1rem;
    color: var(--nc-text-muted);
}

.nc-grid-4 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.nc-service-card {
    background: #FFFFFF;
    border-radius: 14px;
    padding: 30px 25px;
    box-shadow: 0 10px 30px rgba(107, 33, 168, 0.06);
    border: 1px solid var(--nc-gray-border);
    transition: all 0.3s ease;
}

.nc-service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(107, 33, 168, 0.12);
    border-color: var(--nc-purple-main);
}

.nc-icon-box {
    width: 60px;
    height: 60px;
    background: var(--nc-purple-soft);
    color: var(--nc-purple-main);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 20px;
}

.nc-service-card h3 {
    font-size: 1.3rem;
    color: var(--nc-purple-dark);
    margin-bottom: 12px;
    font-weight: 700;
}

.nc-service-card p {
    font-size: 0.95rem;
    color: var(--nc-text-dark);
    margin-bottom: 15px;
}

.nc-service-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nc-service-card ul li {
    font-size: 0.9rem;
    color: var(--nc-text-dark);
    padding: 5px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nc-service-card ul li::before {
    content: "✓";
    color: var(--nc-emerald);
    font-weight: bold;
}

/* Local Context Block */
.nc-context-block {
    background: #FFFFFF;
    border-radius: 14px;
    padding: 45px;
    box-shadow: 0 10px 30px rgba(107, 33, 168, 0.06);
    border-left: 6px solid var(--nc-purple-main);
}

.nc-context-block h3 {
    font-size: 1.6rem;
    color: var(--nc-purple-dark);
    margin-bottom: 20px;
}

.nc-landmarks-list {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
}

.nc-landmark-tag {
    background: var(--nc-purple-soft);
    color: var(--nc-purple-main);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Steps Section */
.nc-steps-bg {
    background-color: var(--nc-gray-light);
    padding: 70px 0;
}

.nc-steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.nc-step-card {
    background: #FFFFFF;
    padding: 35px 30px;
    border-radius: 14px;
    position: relative;
    box-shadow: 0 10px 30px rgba(107, 33, 168, 0.06);
    border: 1px solid var(--nc-gray-border);
}

.nc-step-num {
    position: absolute;
    top: -20px;
    left: 30px;
    width: 40px;
    height: 40px;
    background: var(--nc-purple-main);
    color: #FFFFFF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
}

.nc-step-card h4 {
    font-size: 1.25rem;
    color: var(--nc-purple-dark);
    margin: 15px 0 10px;
}

/* Interactive FAQ Accordion */
.nc-faq-accordion {
    max-width: 900px;
    margin: 0 auto;
}

.nc-faq-item {
    background: #FFFFFF;
    border: 1px solid var(--nc-gray-border);
    border-radius: 10px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.nc-faq-item.active {
    border-color: var(--nc-purple-main);
    box-shadow: 0 4px 15px rgba(107, 33, 168, 0.1);
}

.nc-faq-question {
    padding: 22px 25px;
    font-size: 1.08rem;
    font-weight: 700;
    color: var(--nc-purple-dark);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #FFFFFF;
    user-select: none;
    transition: background 0.2s ease, color 0.2s ease;
}

.nc-faq-question:hover {
    background: var(--nc-purple-soft);
    color: var(--nc-purple-main);
}

.nc-faq-icon {
    width: 28px;
    height: 28px;
    background: #F3F4F6;
    color: var(--nc-purple-dark);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
    flex-shrink: 0;
    transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease;
}

.nc-faq-item.active .nc-faq-icon {
    transform: rotate(45deg);
    background: var(--nc-purple-main);
    color: #FFFFFF;
}

.nc-faq-answer {
    display: none;
    padding: 0 25px 22px;
    font-size: 0.98rem;
    color: var(--nc-text-dark);
    line-height: 1.65;
    border-top: 1px dashed var(--nc-gray-border);
    padding-top: 15px;
}

.nc-faq-item.active .nc-faq-answer {
    display: block;
}

/* Suburb Grid Matrix */
.nc-suburbs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 30px;
}

.nc-suburb-link-card {
    background: #FFFFFF;
    border: 1px solid var(--nc-gray-border);
    border-radius: 8px;
    padding: 15px 18px;
    text-decoration: none !important;
    transition: all 0.25s ease;
    display: block;
}

.nc-suburb-link-card:hover {
    border-color: var(--nc-purple-main);
    background: var(--nc-purple-soft);
    transform: translateY(-2px);
}

.nc-suburb-link-card.active {
    border-color: var(--nc-purple-main);
    background: var(--nc-purple-main);
    color: #FFFFFF !important;
}

.nc-suburb-link-card h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--nc-purple-dark);
}

.nc-suburb-link-card.active h5 {
    color: #FFFFFF !important;
}

.nc-suburb-link-card span {
    font-size: 0.8rem;
    color: var(--nc-text-muted);
}

.nc-suburb-link-card.active span {
    color: #F3E8FF !important;
}

/* CTA Banner (Purple Brand Theme) */
.nc-cta-banner {
    background: linear-gradient(135deg, #3B0764 0%, #6B21A8 100%);
    color: #FFFFFF;
    padding: 60px 40px;
    border-radius: 16px;
    text-align: center;
    margin: 60px 0;
    box-shadow: 0 15px 35px rgba(107, 33, 168, 0.15);
}

.nc-cta-banner h2 {
    font-size: 2.2rem;
    color: #FFFFFF !important;
    margin-bottom: 15px;
    font-weight: 700;
}

.nc-cta-banner p {
    font-size: 1.15rem;
    color: #F3E8FF;
    max-width: 700px;
    margin: 0 auto 30px;
}

@media (max-width: 768px) {
    .nc-hero h1 { font-size: 2rem; }
    .nc-hero { padding: 45px 0; }
    .nc-context-block { padding: 25px; }
    .nc-cta-banner { padding: 40px 20px; }
}
</style>

<div class="nc-suburb-wrapper">
    <!-- Hero Section -->
    <header class="nc-hero">
        <div class="nc-container">
            <span class="nc-hero-badge">📍 Sutherland Shire | Postcode <?php echo esc_html($suburb['postcode'] ?? '2232'); ?></span>
            <h1><?php echo esc_html($suburb['hero_headline'] ?? 'Local NDIS Disability Support & In-Home Aged Care Services'); ?></h1>
            <p class="nc-hero-sub"><?php echo esc_html($suburb['hero_subheadline'] ?? 'Empowering individuals and families with compassionate, personalized care.'); ?></p>
            
            <div class="nc-trust-pills">
                <div class="nc-pill">🛡️ Registered NDIS Provider</div>
                <div class="nc-pill">🏡 Local Sutherland Shire Team</div>
                <div class="nc-pill">⚡ 24/7 Service Availability</div>
                <div class="nc-pill">💙 100% Person-Centered Care</div>
            </div>

            <div class="nc-hero-btns">
                <a href="tel:<?php echo esc_attr($phone_tel); ?>" class="nc-btn-emerald">📞 Call Us: <?php echo esc_html($phone_display); ?></a>
                <a href="#consultation" class="nc-btn-secondary" onclick="document.getElementById('consultation').scrollIntoView({behavior: 'smooth'}); return false;">Request Free Assessment</a>
            </div>
        </div>
    </header>

    <!-- Services Overview Grid -->
    <section class="nc-section">
        <div class="nc-container">
            <div class="nc-section-title">
                <h2>Tailored Disability Support & In-Home Aged Care in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?></h2>
                <p>Delivering high-quality, compassionate care solutions designed around your personal goals, daily routine, and health needs.</p>
            </div>

            <div class="nc-grid-4">
                <div class="nc-service-card">
                    <div class="nc-icon-box">🤝</div>
                    <h3>NDIS Support Services</h3>
                    <p>Comprehensive core & capacity supports empowering NDIS participants in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?> to achieve true autonomy.</p>
                    <ul>
                        <li>Daily Personal Hygiene Care</li>
                        <li>Social & Community Access</li>
                        <li>Life Skills Development</li>
                        <li>Support Coordination</li>
                    </ul>
                </div>

                <div class="nc-service-card">
                    <div class="nc-icon-box">🏡</div>
                    <h3>Aged Care & Home Packages</h3>
                    <p>Government funded Home Care Packages (Level 1–4) enabling seniors to live independently in their own <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?> homes.</p>
                    <ul>
                        <li>Domestic Help & Cleaning</li>
                        <li>Nutritious Meal Prep</li>
                        <li>Gardening & Home Upkeep</li>
                        <li>Personal Mobility Support</li>
                    </ul>
                </div>

                <div class="nc-service-card">
                    <div class="nc-icon-box">🩺</div>
                    <h3>Clinical & Nursing Care</h3>
                    <p>Professional nursing and healthcare management delivered in the comfort of your residence by qualified Registered Nurses.</p>
                    <ul>
                        <li>Wound Care & Dressing</li>
                        <li>Medication Management</li>
                        <li>Chronic Disease Care</li>
                        <li>Physio & OT Visits</li>
                    </ul>
                </div>

                <div class="nc-service-card">
                    <div class="nc-icon-box">🚗</div>
                    <h3>Respite & Transport</h3>
                    <p>Flexible carer relief and assisted travel to medical clinics, shopping hubs, and community gatherings across Sutherland Shire.</p>
                    <ul>
                        <li>Emergency & Scheduled Respite</li>
                        <li>Transport to Sutherland Hospital</li>
                        <li>Miranda Westfield Outings</li>
                        <li>Social Group Gatherings</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Suburb Community Context -->
    <section class="nc-section" style="background: var(--nc-gray-light);">
        <div class="nc-container">
            <div class="nc-context-block">
                <h3>Dedicated Local Care in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?> (NSW <?php echo esc_html($suburb['postcode'] ?? '2232'); ?>)</h3>
                <p><?php echo esc_html($suburb['intro_text'] ?? ''); ?></p>
                <p style="margin-top: 15px;"><?php echo esc_html($suburb['community_context'] ?? ''); ?></p>
                
                <?php if ( ! empty( $suburb['landmarks'] ) ) : ?>
                    <h4 style="margin-top: 25px; color: var(--nc-purple-dark); font-size: 1.1rem; font-weight: 700;">Key Local Area Landmarks We Cover:</h4>
                    <div class="nc-landmarks-list">
                        <?php foreach ($suburb['landmarks'] as $landmark): ?>
                            <span class="nc-landmark-tag">📍 <?php echo esc_html($landmark); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- How to Access Care Steps -->
    <section class="nc-steps-bg">
        <div class="nc-container">
            <div class="nc-section-title">
                <h2>How to Access Care Services in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?></h2>
                <p>Starting your care journey with Nova Care Australia is simple, transparent, and hassle-free.</p>
            </div>

            <div class="nc-steps-grid">
                <div class="nc-step-card">
                    <div class="nc-step-num">1</div>
                    <h4>Initial Consultation</h4>
                    <p>Contact our Sutherland Shire team to discuss your NDIS plan or Home Care Package requirements during a free consultation.</p>
                </div>

                <div class="nc-step-card">
                    <div class="nc-step-num">2</div>
                    <h4>Tailored Care Plan</h4>
                    <p>We work with you and your family in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?> to design a personalized support schedule that meets your goals.</p>
                </div>

                <div class="nc-step-card">
                    <div class="nc-step-num">3</div>
                    <h4>Care Commencement</h4>
                    <p>We match you with qualified, friendly local support workers so you can begin receiving reliable care right at home.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Localized FAQ Accordion -->
    <section class="nc-section">
        <div class="nc-container">
            <div class="nc-section-title">
                <h2>Frequently Asked Questions in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?></h2>
                <p>Common questions about NDIS funding, Home Care Packages, and service delivery in your suburb.</p>
            </div>

            <div class="nc-faq-accordion">
                <?php if ( ! empty( $suburb['faqs'] ) ) : ?>
                    <?php foreach ($suburb['faqs'] as $index => $faq): ?>
                        <div class="nc-faq-item">
                            <div class="nc-faq-question">
                                <span><?php echo esc_html(($index + 1) . '. ' . $faq['q']); ?></span>
                                <span class="nc-faq-icon">+</span>
                            </div>
                            <div class="nc-faq-answer">
                                <p><?php echo esc_html($faq['a']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Suburb Interlinking Grid -->
    <section class="nc-section" style="background: #F8FAFC;">
        <div class="nc-container">
            <div class="nc-section-title">
                <h2>Explore Care Services in Surrounding Sutherland Suburbs</h2>
                <p>Nova Care Australia proudly provides accredited NDIS disability support and in-home aged care across all 20 residential suburbs in the Sutherland Shire.</p>
            </div>

            <div class="nc-suburbs-grid">
                <?php foreach ($all_suburbs as $key => $sub_item): ?>
                    <?php 
                    $is_current = ($key === $current_suburb_key); 
                    $link = home_url( '/' . $sub_item['slug'] . '/' );
                    ?>
                    <a href="<?php echo esc_url($link); ?>" class="nc-suburb-link-card <?php echo $is_current ? 'active' : ''; ?>">
                        <h5><?php echo esc_html($sub_item['name']); ?></h5>
                        <span>Postcode <?php echo esc_html($sub_item['postcode']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Consultation CTA Banner -->
    <section class="nc-container" id="consultation">
        <div class="nc-cta-banner">
            <h2>Ready to Experience Better Care in <?php echo esc_html($suburb['name'] ?? 'Sutherland'); ?>?</h2>
            <p>Speak with our friendly Sutherland Shire care managers today. We are here to answer your questions, assist with NDIS planning, or setup your Home Care Package.</p>
            <div class="nc-hero-btns" style="justify-content: center;">
                <a href="tel:<?php echo esc_attr($phone_tel); ?>" class="nc-btn-emerald">📞 Call Us: <?php echo esc_html($phone_display); ?></a>
                <a href="/contact/" class="nc-btn-secondary">Send Online Message</a>
            </div>
        </div>
    </section>
</div>

<!-- Interactive Accordion JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var faqQuestions = document.querySelectorAll('.nc-faq-question');
    faqQuestions.forEach(function(question) {
        question.addEventListener('click', function(e) {
            e.preventDefault();
            var item = this.parentElement;
            var isActive = item.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.nc-faq-item').forEach(function(otherItem) {
                otherItem.classList.remove('active');
            });
            
            // Toggle clicked item
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
</script>

<!-- JSON-LD Schema Microdata for Google SEO -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "MedicalBusiness",
      "@id": "<?php echo esc_url(home_url('/#organization')); ?>",
      "name": "Nova Care Australia - <?php echo esc_js($suburb['name'] ?? 'Sutherland'); ?>",
      "url": "<?php echo esc_url(home_url($_SERVER['REQUEST_URI'] ?? '/')); ?>",
      "telephone": "<?php echo esc_js($phone_display); ?>",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "<?php echo esc_js($suburb['name'] ?? 'Sutherland'); ?>",
        "addressRegion": "NSW",
        "postalCode": "<?php echo esc_js($suburb['postcode'] ?? '2232'); ?>",
        "addressCountry": "AU"
      },
      "areaServed": {
        "@type": "AdministrativeArea",
        "name": "<?php echo esc_js($suburb['name'] ?? 'Sutherland'); ?>"
      }
    }
    <?php if ( ! empty( $suburb['faqs'] ) ) : ?>,
    {
      "@type": "FAQPage",
      "mainEntity": [
        <?php 
        $faq_schema_items = array();
        foreach ($suburb['faqs'] as $faq) {
            $faq_schema_items[] = '{
              "@type": "Question",
              "name": ' . json_encode($faq['q']) . ',
              "acceptedAnswer": {
                "@type": "Answer",
                "text": ' . json_encode($faq['a']) . '
              }
            }';
        }
        echo implode(',', $faq_schema_items);
        ?>
      ]
    }
    <?php endif; ?>
  ]
}
</script>

<?php get_footer(); ?>
