<?php
define('RECIPIENT_EMAIL', 'reservations@medorahotels.com');
define('EMAIL_SUBJECT',   'New Proposal Request — Medora Hotels');
define('SUCCESS_MESSAGE',  'Thank you! Your request has been submitted. We'll be in touch within 48 hours.');
define('ERROR_MESSAGE',    'Something went wrong. Please try again or contact us directly at ' . RECIPIENT_EMAIL);
define('MAX_SUBMISSIONS',  3);
define('RATE_WINDOW',      600);
define('RATE_MESSAGE',     'You've submitted too many requests. Please wait a few minutes and try again.');

session_start();

$status  = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['form_submissions'])) {
        $_SESSION['form_submissions'] = [];
    }
    $now = time();
    $_SESSION['form_submissions'] = array_filter(
        $_SESSION['form_submissions'],
        function ($ts) use ($now) { return ($now - $ts) < RATE_WINDOW; }
    );

    if (count($_SESSION['form_submissions']) >= MAX_SUBMISSIONS) {
        $status = RATE_MESSAGE;
    } elseif (!empty($_POST['website'])) {
        $status  = SUCCESS_MESSAGE;
        $success = true;
    } else {
        $name         = htmlspecialchars(trim($_POST['name'] ?? ''));
        $organisation = htmlspecialchars(trim($_POST['organisation'] ?? ''));
        $email        = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $program_type = htmlspecialchars(trim($_POST['program_type'] ?? ''));
        $group_size   = htmlspecialchars(trim($_POST['group_size'] ?? ''));
        $dates        = htmlspecialchars(trim($_POST['preferred_dates'] ?? ''));
        $details      = htmlspecialchars(trim($_POST['program_details'] ?? ''));

        $errors = [];
        if ($name === '')                               $errors[] = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if ($program_type === '')                        $errors[] = 'Please select a program type.';

        if (empty($errors)) {
            $body  = "NEW PROPOSAL REQUEST\n";
            $body .= str_repeat('-', 40) . "\n\n";
            $body .= "Name:            {$name}\n";
            $body .= "Organisation:    {$organisation}\n";
            $body .= "Email:           {$email}\n";
            $body .= "Program Type:    {$program_type}\n";
            $body .= "Group Size:      {$group_size}\n";
            $body .= "Preferred Dates: {$dates}\n\n";
            $body .= "Program Details:\n{$details}\n";

            $headers  = "From: {$name} <{$email}>\r\n";
            $headers .= "Reply-To: {$email}\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            if (mail(RECIPIENT_EMAIL, EMAIL_SUBJECT, $body, $headers)) {
                $_SESSION['form_submissions'][] = time();
                $status  = SUCCESS_MESSAGE;
                $success = true;
            } else {
                $status = ERROR_MESSAGE;
            }
        } else {
            $status = implode(' ', $errors);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medora — Retreats, Camps & Brand Stays</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;1,400;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
.lang-switcher {
  display: flex;
  gap: 0.25rem;
  align-items: center;
  margin-left: 1rem;
}
.lang-btn {
  background: none;
  border: 1px solid transparent;
  color: var(--ink-light);
  font-family: 'Jost', sans-serif;
  font-size: 0.68rem;
  letter-spacing: 0.1em;
  padding: 0.25rem 0.45rem;
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.2s;
  text-transform: uppercase;
}
.lang-btn:hover { color: var(--sea); border-color: var(--sand); }
.lang-btn.active { color: var(--sea); border-color: var(--sea); font-weight: 500; }
@media (max-width: 900px) {
  .lang-switcher { margin-left: 0.4rem; gap: 0.15rem; }
  .lang-btn { font-size: 0.58rem; padding: 0.2rem 0.35rem; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="#" class="nav-logo">Medora <span>Hotels & Resorts</span></a>
  <ul class="nav-links">
    <li><a href="#properties" data-i18n="nav_properties">Properties</a></li>
    <li><a href="#programs" data-i18n="nav_programs">Programs</a></li>
    <li><a href="#location" data-i18n="nav_location">Location</a></li>
    <li><a href="#why" data-i18n="nav_why">Why Medora</a></li>
    <li><a href="#contact" class="nav-cta" data-i18n="nav_cta">Get a Proposal</a></li>
  </ul>
  <a href="#contact" class="nav-mobile-cta" data-i18n="nav_cta">Get a Proposal</a>
  <div class="lang-switcher">
    <button class="lang-btn active" onclick="setLang('en')">EN</button>
    <button class="lang-btn" onclick="setLang('hr')">HR</button>
    <button class="lang-btn" onclick="setLang('de')">DE</button>
    <button class="lang-btn" onclick="setLang('pl')">PL</button>
  </div>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="hero-bg">
    <div class="hero-split-desktop">
      <div style="overflow:hidden;position:relative;">
        <img src="HOTEL.jpg" alt="Medora Auri Hotel" class="hero-img" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:0.65;">
      </div>
      <div style="overflow:hidden;position:relative;">
        <img src="aerial camp.jpg" alt="Medora Orbis Camp" class="hero-img" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:0.65;">
      </div>
    </div>
    <div class="hero-fade-mobile">
      <img src="HOTEL.jpg" alt="Medora Auri Hotel" class="hero-fade-img active">
      <img src="aerial camp.jpg" alt="Medora Orbis Camp" class="hero-fade-img">
    </div>
    <div class="hero-overlay"></div>
  </div>
  <div class="hero-content">
    <p class="hero-eyebrow" data-i18n="hero_eyebrow">Makarska Riviera · Dalmatia · Croatia</p>
    <h1 data-i18n-html="hero_h1">Host your program<br>on the <em>Makarska Riviera.</em></h1>
    <p class="hero-sub" data-i18n-html="hero_sub">A venue for unforgettable stays, retreats and group experiences.<br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>A beachfront hotel and premium campsite in Podgora.</p>
    <div class="hero-actions">
      <a href="#contact" class="btn-primary" data-i18n="nav_cta">Get a Proposal</a>
      <a href="#properties" class="btn-outline" data-i18n="btn_explore">Explore the venues</a>
    </div>
  </div>
</div>

<!-- INTRO STRIP -->
<div class="intro-strip">
  <div class="intro-item">
    <div class="intro-label" data-i18n="intro_label_1">Accommodation</div>
    <p class="intro-text" data-i18n="intro_text_1">Hotel rooms, mobile homes, or a combination. Flexible setups for any group size and format.</p>
  </div>
  <div class="intro-item">
    <div class="intro-label" data-i18n="intro_label_2">Food & Beverage</div>
    <p class="intro-text" data-i18n="intro_text_2">Reliable catering from daily half board to fully tailored group menus. No coordination required on your end.</p>
  </div>
  <div class="intro-item">
    <div class="intro-label" data-i18n="intro_label_3">Spaces & Logistics</div>
    <p class="intro-text" data-i18n="intro_text_3">Indoor and outdoor areas ready for use. Activities, transfers and event support all available through us.</p>
  </div>
</div>

<!-- PROPERTIES -->
<div id="properties"></div>
<div style="background: var(--white);">
  <div class="section-full-inner" style="padding-top: 6rem; padding-bottom: 2rem;">
    <p class="section-tag" data-i18n="venues_tag">The Venues</p>
    <h2 data-i18n-html="venues_h2">Two properties,<br>one organisation.</h2>
    <p style="font-size:0.85rem;letter-spacing:0.08em;color:var(--ink-light);margin-bottom:1rem;text-transform:uppercase;" data-i18n="venues_names">Medora Auri Hotel and Medora Orbis Camp</p>
    <p class="lead" data-i18n="venues_lead">Medora Auri Hotel and Medora Orbis Camp sit side by side in Podgora, on the Makarska Riviera. Use one, the other, or both — depending on what your program needs.</p>
  </div>
  <div class="venue-tabs">
    <button class="venue-tab active" data-index="0">Medora Auri Hotel</button>
    <button class="venue-tab" data-index="1">Medora Orbis Camp</button>
  </div>
  <div class="properties-grid" id="propertiesGrid">
    <div class="property-card venue-slide active">
      <div class="property-img">
        <img src="Medora Auri Pool & Beach 11.jpg" alt="Medora Auri Hotel">
      </div>
      <div class="property-info">
        <div class="property-stars" data-i18n="hotel_stars">4★ Beachfront Hotel</div>
        <h3>Medora Auri Hotel</h3>
        <p class="property-desc" data-i18n="hotel_desc">A modern hotel directly on the sea. Suitable for wellness retreats, corporate offsites, brand stays and groups that want a more structured, hotel-grade experience.</p>
        <ul class="amenities">
          <li data-i18n="hotel_a1">Restaurant — group dining and events</li>
          <li data-i18n="hotel_a2">Outdoor heated pools</li>
          <li data-i18n="hotel_a3">Spa & wellness facilities</li>
          <li data-i18n="hotel_a4">Sea-view fitness centre</li>
          <li data-i18n="hotel_a5">Yoga & activity spaces</li>
          <li data-i18n="hotel_a6">Conference and meeting rooms</li>
        </ul>
      </div>
    </div>
    <div class="property-card venue-slide">
      <div class="property-img">
        <img src="aerial camp.jpg" alt="Medora Orbis Camp">
      </div>
      <div class="property-info">
        <div class="property-stars" data-i18n="camp_stars">Premium Camping</div>
        <h3>Medora Orbis Camping</h3>
        <p class="property-desc" data-i18n="camp_desc">Deluxe mobile homes and camping pitches, some units with private heated pools. A more relaxed, outdoor-oriented base, well suited to sports camps, active retreats and groups that prefer a natural setting.</p>
        <ul class="amenities">
          <li data-i18n="camp_a1">Deluxe mobile homes & camping pitches</li>
          <li data-i18n="camp_a2">Private heated pools on selected mobile homes</li>
          <li data-i18n="camp_a3">Outdoor terraces & BBQ areas</li>
          <li data-i18n="camp_a4">Bicycles & active-use spaces</li>
          <li data-i18n="camp_a5">Social outdoor zones</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- GALLERY -->
<div class="gallery-section" id="gallery">
  <div class="gallery-header">
    <p class="section-tag" data-i18n="gallery_tag">A Look Around</p>
    <h2 data-i18n="gallery_h2">The spaces & facilities</h2>
  </div>
  <div class="gallery-grid">
    <div class="gallery-item wide"><img src="Medora Auri night photo pool.jpg" alt="Pool at night"></div>
    <div class="gallery-item"><img src="Medora Auri Wellness 4.jpg" alt="Wellness"></div>
    <div class="gallery-item"><img src="Medora Auri Wellness 7.jpg" alt="Spa"></div>
    <div class="gallery-item"><img src="Medora Auri hotel fitness.jpg" alt="Fitness"></div>
    <div class="gallery-item"><img src="lobby bar.jpg" alt="Lobby bar"></div>
    <div class="gallery-item wide"><img src="F&B.jpg" alt="Food and beverage"></div>
    <div class="gallery-item"><img src="F&B_.jpg" alt="Group dining"></div>
    <div class="gallery-item"><img src="f&Bb.jpg" alt="Catering"></div>
    <div class="gallery-item"><img src="outside active.jpg" alt="Outdoor spaces"></div>
    <div class="gallery-item"><img src="Pool.jpg" alt="Pool"></div>
    <div class="gallery-item wide"><img src="plaža.jpg" alt="Beach"></div>
    <div class="gallery-item"><img src="camp2.jpg" alt="Camp"></div>
    <div class="gallery-item"><img src="camp3.jpg" alt="Camp outdoor"></div>
  </div>
</div>

<!-- PROGRAMS -->
<div style="background: var(--white);">
  <section id="programs">
    <p class="section-tag" data-i18n="prog_tag">What We Host</p>
    <h2 data-i18n="prog_h2">Programs we host.</h2>
    <p class="lead" data-i18n="prog_lead">We work with organizers, brands and teams that need a venue that handles the infrastructure, so they can focus on their program.</p>
    <div class="programs-grid">
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title" data-i18n="prog1_title">Creator & Brand Partnerships</div>
        <p class="program-desc" data-i18n="prog1_desc">Hosted stays, content production and brand activations. The location works visually and logistically: sea, mountains, outdoor light and facilities that photograph well.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title" data-i18n="prog2_title">Wellness & Lifestyle Retreats</div>
        <p class="program-desc" data-i18n="prog2_desc">Yoga, fitness and recovery retreats with ready-to-use spaces, structured meal options and a calm, natural setting away from the summer crowds.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title" data-i18n="prog3_title">Active & Sports Camps</div>
        <p class="program-desc" data-i18n="prog3_desc">Football camps, dance groups, fitness teams and youth programs. Flexible accommodation, outdoor training areas and full F&B support built in.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title" data-i18n="prog4_title">Corporate & Team Events</div>
        <p class="program-desc" data-i18n="prog4_desc">Offsites, workshops and strategy sessions. Meeting rooms, catering and accommodation in one place, no need to coordinate multiple suppliers.</p>
      </div>
    </div>
  </section>
</div>

<!-- REVIEWS -->
<div class="reviews-section">
  <div class="reviews-inner">
    <p class="section-tag" data-i18n="reviews_tag">Guest Ratings</p>
    <h2 data-i18n-html="reviews_h2">Consistently top-rated<br>on the Riviera.</h2>
    <p class="lead" data-i18n="reviews_lead">Rated among the best hotels and campsites on the Makarska Riviera across Google and Booking.com.</p>
    <div class="reviews-grid">
      <div class="review-property-block">
        <div class="review-property-name">Medora Auri Hotel</div>
        <div class="review-pair">
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Booking.com</div></div>
            <div class="review-score">9.2 <span>/ 10</span></div>
            <div class="review-divider"></div>
            <div class="review-stars" data-i18n="review_superb_3016">Superb · 3,016 reviews</div>
          </div>
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Google</div></div>
            <div class="review-score">4.7 <span>/ 5</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">3,664 <span data-i18n="reviews_word">reviews</span></div>
          </div>
        </div>
      </div>
      <div class="review-property-block">
        <div class="review-property-name">Medora Orbis Camp</div>
        <div class="review-pair">
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Booking.com</div></div>
            <div class="review-score">9.6 <span>/ 10</span></div>
            <div class="review-divider"></div>
            <div class="review-stars" data-i18n="review_superb_367">Superb · 367 reviews</div>
          </div>
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Google</div></div>
            <div class="review-score">4.7 <span>/ 5</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">1,175 <span data-i18n="reviews_word">reviews</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- LOCATION -->
<section id="location">
  <p class="section-tag" data-i18n="loc_tag">Getting Here</p>
  <h2 data-i18n-html="loc_h2">Podgora, Makarska Riviera,<br>Dalmatia, Croatia.</h2>
  <p class="lead" data-i18n="loc_lead">Between the Biokovo mountains and the Adriatic, 10 minutes from Makarska town. Straightforward to reach from Split Airport and well connected to major EU cities.</p>
  <div class="location-grid">
    <div class="location-map">
      <img src="MAPA.png" alt="Map">
    </div>
    <div class="location-details">
      <div class="location-block">
        <h4 data-i18n="loc_tag">Getting Here</h4>
        <ul class="route-list">
          <li>Split Airport (SPU) <span data-i18n="loc_spu_time">105 km · ~1h 30 min</span></li>
          <li>Dubrovnik Airport (DBV) <span data-i18n="loc_dbv_time">170 km · ~2h 30 min</span></li>
          <li><span data-i18n="loc_highway">Highway exit Zagvozd (A1)</span> <span>20 km · ~25 min</span></li>
        </ul>
      </div>
      <div class="location-block">
        <h4 data-i18n="loc_transfers_h">Transfer Services</h4>
        <ul class="transfer-list">
          <li data-i18n="loc_t1">Airport transfers (Split ↔ Podgora, Dubrovnik ↔ Podgora)</li>
          <li data-i18n="loc_t2">Group minibus (8–20 passengers)</li>
          <li data-i18n="loc_t3">Private day trips (Makarska, Split, Dubrovnik & islands)</li>
        </ul>
      </div>
      <div class="location-block">
        <h4 data-i18n="loc_nearby_h">Nearby</h4>
        <ul class="nearby-list">
          <li><span data-i18n="loc_near1">Makarska</span> <span>10 min</span></li>
          <li>Split <span>1h 30 min</span></li>
          <li>Dubrovnik <span>2h 30 min</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ATTRACTIONS -->
<div class="attractions-section">
  <div class="attractions-inner">
    <p class="section-tag" data-i18n="attr_tag">The Region</p>
    <h2 data-i18n="attr_h2">The surrounding region.</h2>
    <p class="lead" style="margin-bottom:0;" data-i18n="attr_lead">The Makarska Riviera sits at a crossroads of mountains, coast and islands. Day trips make a real difference to the overall program experience.</p>
    <div class="attractions-grid">
      <div class="attraction-card">
        <img src="Biokovo 1.jpg" alt="Biokovo">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr1_name">Biokovo Mountain</span>
          <span class="attraction-time" data-i18n="attr1_time">15–20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="skywalk.jpg" alt="Skywalk">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr2_name">Skywalk Viewpoint</span>
          <span class="attraction-time" data-i18n="attr2_time">20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Cetina 1.jpg" alt="Cetina">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr3_name">Cetina River Canyon</span>
          <span class="attraction-time" data-i18n="attr3_time">40 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Bacinska jezera.jpg" alt="Baćina Lakes">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr4_name">Baćina Lakes</span>
          <span class="attraction-time" data-i18n="attr4_time">5–10 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Crveno Jezero.jpg" alt="Crveno Jezero">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr5_name">Crveno Jezero</span>
          <span class="attraction-time" data-i18n="attr_imotski">1.5 hrs · Imotski</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Modro Jezero 1.jpg" alt="Modro Jezero">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr6_name">Modro Jezero</span>
          <span class="attraction-time" data-i18n="attr_imotski">1.5 hrs · Imotski</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Dubrovnik.jpg" alt="Dubrovnik">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr7_name">Dubrovnik</span>
          <span class="attraction-time" data-i18n="attr_dubrovnik_time">2 hrs 20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Wine and olive oil tasting from local producers.jpg" alt="Wine & Olive Oil">
        <div class="attraction-label">
          <span class="attraction-name" data-i18n="attr8_name">Local Wine & Olive Oil</span>
          <span class="attraction-time" data-i18n="attr8_time">Regional producers</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- WHY -->
<section id="why">
  <p class="section-tag" data-i18n="why_tag">Why Medora</p>
  <h2 data-i18n-html="why_h2">Why organizers<br>choose Medora.</h2>
  <p class="lead" data-i18n="why_lead">The main reasons organizers, brands and teams come back.</p>
  <div class="why-grid">
    <div class="why-card">
      <div class="why-number">01</div>
      <div class="why-title" data-i18n="why1_title">Flexible & Scalable</div>
      <p class="why-desc" data-i18n="why1_desc">Competitive pricing in shoulder season (April–June and September–October), with packages adapted to your group size, format and budget.</p>
    </div>
    <div class="why-card">
      <div class="why-number">02</div>
      <div class="why-title" data-i18n="why2_title">Everything Already in Place</div>
      <p class="why-desc" data-i18n="why2_desc">Spa, wellness, fitness, healthy menus, outdoor activities and meeting spaces. All the infrastructure needed for a complete program, without extra coordination.</p>
    </div>
    <div class="why-card">
      <div class="why-number">03</div>
      <div class="why-title" data-i18n="why3_title">Proven for Large Groups</div>
      <p class="why-desc" data-i18n="why3_desc">Experience handling large capacities, backed by an organised operational team that knows how to run group programs efficiently.</p>
    </div>
    <div class="why-card">
      <div class="why-number">04</div>
      <div class="why-title" data-i18n="why4_title">Recognised Quality</div>
      <p class="why-desc" data-i18n="why4_desc">Top-rated across all major OTA platforms with a strong, stable presence on the regional market. Your participants arrive with positive expectations.</p>
    </div>
    <div class="why-card">
      <div class="why-number">05</div>
      <div class="why-title" data-i18n="why5_title">Long-Term Partnership</div>
      <p class="why-desc" data-i18n="why5_desc">We focus on building ongoing relationships with preferred partners: dedicated support, priority availability and better rates for repeat programs.</p>
    </div>
    <div class="why-card">
      <div class="why-number">06</div>
      <div class="why-title" data-i18n="why6_title">Simple from Start to Finish</div>
      <p class="why-desc" data-i18n="why6_desc">One point of contact, clear communication and a tailored proposal within 48 hours. No back-and-forth with multiple suppliers.</p>
    </div>
  </div>
</section>

<!-- CONTACT -->
<div class="contact-section" id="contact">
  <div class="contact-inner">
    <div class="contact-info">
      <p class="section-tag" data-i18n="contact_tag">Get in Touch</p>
      <h2 data-i18n="contact_h2">Let's build the right package for your group.</h2>
      <p class="lead" style="margin-bottom:2rem;" data-i18n="contact_lead">Use this form for retreat planning, brand collaborations, sports camps and corporate offsites. We'll reply with a tailored proposal within 48 hours.</p>
      <ul class="contact-list">
        <li data-i18n="contact_li1">We'll get back to you with a tailored proposal within 48 hours</li>
        <li data-i18n="contact_li2">Full support in organizing your program: you lead, or we connect you with a trusted agency</li>
        <li data-i18n="contact_li3">Accommodation across hotel and campsite, adapted to your group size and concept</li>
        <li data-i18n="contact_li4">All logistics handled in one place: stay, meals, spaces, activities</li>
        <li data-i18n="contact_li5">Additional services available on request: transfers, excursions, special dining</li>
      </ul>
    </div>
    <div>
      <?php if ($status): ?>
        <div class="form-status-msg <?= $success ? 'form-status-success' : 'form-status-error' ?>">
          <?= htmlspecialchars($status) ?>
        </div>
      <?php endif; ?>
      <?php if (!$success): ?>
      <form class="contact-form" method="POST" action="#contact" novalidate>
        <div style="position:absolute;left:-9999px;opacity:0;height:0;width:0;overflow:hidden;">
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="name" data-i18n="form_name">Name</label>
            <input type="text" id="name" name="name" data-i18n-placeholder="form_name_ph" placeholder="Your name" required>
          </div>
          <div class="form-group">
            <label for="organisation" data-i18n="form_org">Organisation</label>
            <input type="text" id="organisation" name="organisation" data-i18n-placeholder="form_org_ph" placeholder="Company or agency">
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="your@email.com" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="program_type" data-i18n="form_type">Program Type</label>
            <select id="program_type" name="program_type" required>
              <option value="" data-i18n="form_select">Select a type</option>
              <option value="Creator / Brand Stay" data-i18n="form_opt1">Creator / Brand Stay</option>
              <option value="Wellness / Yoga Retreat" data-i18n="form_opt2">Wellness / Yoga Retreat</option>
              <option value="Active / Sports Camp" data-i18n="form_opt3">Active / Sports Camp</option>
              <option value="Corporate Events" data-i18n="form_opt4">Corporate Events</option>
              <option value="Other" data-i18n="form_opt5">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="group_size" data-i18n="form_size">Group Size</label>
            <input type="text" id="group_size" name="group_size" data-i18n-placeholder="form_size_ph" placeholder="Approx. number of people">
          </div>
        </div>
        <div class="form-group">
          <label for="preferred_dates" data-i18n="form_dates">Preferred Dates</label>
          <input type="text" id="preferred_dates" name="preferred_dates" data-i18n-placeholder="form_dates_ph" placeholder="e.g. April–May 2025, flexible">
        </div>
        <div class="form-group">
          <label for="program_details" data-i18n="form_details">Program Details</label>
          <textarea id="program_details" name="program_details" data-i18n-placeholder="form_details_ph" placeholder="Brief description of what you're planning. We'll take it from there."></textarea>
        </div>
        <button type="submit" class="form-submit" data-i18n="form_submit">Request a Proposal</button>
        <p class="form-consent" data-i18n="form_consent">By submitting this form, you agree to be contacted by Medora Hotels & Resorts regarding your enquiry. Your information will not be shared with third parties.</p>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">Medora Hotels & Resorts · Podgora, <span data-i18n="footer_riviera">Makarska Riviera</span> · <span data-i18n="footer_country">Croatia</span></div>
  <div class="footer-copy" data-i18n="footer_copy">Partnership & Group Enquiries</div>
</footer>

<script src="main.js"></script>
<script>
const i18n = {
  en: {
    nav_properties:'Properties',nav_programs:'Programs',nav_location:'Location',nav_why:'Why Medora',nav_cta:'Get a Proposal',btn_explore:'Explore the venues',
    hero_eyebrow:'Makarska Riviera · Dalmatia · Croatia',
    hero_h1:'Host your program<br>on the <em>Makarska Riviera.</em>',
    hero_sub:'<strong>A venue for unforgettable stays, retreats and group experiences.</strong><br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>A beachfront hotel and premium campsite in Podgora.',
    intro_label_1:'Accommodation',intro_label_2:'Food & Beverage',intro_label_3:'Spaces & Logistics',
    intro_text_1:'Hotel rooms, mobile homes, or a combination. Flexible setups for any group size and format.',
    intro_text_2:'Reliable catering from daily half board to fully tailored group menus. No coordination required on your end.',
    intro_text_3:'Indoor and outdoor areas ready for use. Activities, transfers and event support all available through us.',
    venues_tag:'The Venues',venues_h2:'Two properties,<br>one organisation.',
    venues_names:'Medora Auri Hotel and Medora Orbis Camp',
    venues_lead:'Medora Auri Hotel and Medora Orbis Camp sit side by side in Podgora, on the Makarska Riviera. Use one, the other, or both — depending on what your program needs.',
    hotel_stars:'4★ Beachfront Hotel',
    hotel_desc:'A modern hotel directly on the sea. Suitable for wellness retreats, corporate offsites, brand stays and groups that want a more structured, hotel-grade experience.',
    hotel_a1:'Restaurant — group dining and events',hotel_a2:'Outdoor heated pools',hotel_a3:'Spa & wellness facilities',hotel_a4:'Sea-view fitness centre',hotel_a5:'Yoga & activity spaces',hotel_a6:'Conference and meeting rooms',
    camp_stars:'Premium Camping',
    camp_desc:'Deluxe mobile homes and camping pitches, some units with private heated pools. A more relaxed, outdoor-oriented base, well suited to sports camps, active retreats and groups that prefer a natural setting.',
    camp_a1:'Deluxe mobile homes & camping pitches',camp_a2:'Private heated pools on selected mobile homes',camp_a3:'Outdoor terraces & BBQ areas',camp_a4:'Bicycles & active-use spaces',camp_a5:'Social outdoor zones',
    gallery_tag:'A Look Around',gallery_h2:'The spaces & facilities',
    prog_tag:'What We Host',prog_h2:'Programs we host.',
    prog_lead:'We work with organizers, brands and teams that need a venue that handles the infrastructure, so they can focus on their program.',
    prog1_title:'Creator & Brand Partnerships',prog1_desc:'Hosted stays, content production and brand activations. The location works visually and logistically: sea, mountains, outdoor light and facilities that photograph well.',
    prog2_title:'Wellness & Lifestyle Retreats',prog2_desc:'Yoga, fitness and recovery retreats with ready-to-use spaces, structured meal options and a calm, natural setting away from the summer crowds.',
    prog3_title:'Active & Sports Camps',prog3_desc:'Football camps, dance groups, fitness teams and youth programs. Flexible accommodation, outdoor training areas and full F&B support built in.',
    prog4_title:'Corporate & Team Events',prog4_desc:'Offsites, workshops and strategy sessions. Meeting rooms, catering and accommodation in one place, no need to coordinate multiple suppliers.',
    reviews_tag:'Guest Ratings',reviews_h2:'Consistently top-rated<br>on the Riviera.',
    reviews_lead:'Rated among the best hotels and campsites on the Makarska Riviera across Google and Booking.com.',
    review_superb_3016:'3,016 reviews',review_superb_367:'367 reviews',reviews_word:'reviews',
    loc_tag:'Getting Here',loc_h2:'Podgora, Makarska Riviera,<br>Dalmatia, Croatia.',
    loc_lead:'Between the Biokovo mountains and the Adriatic, 10 minutes from Makarska town. Straightforward to reach from Split Airport and well connected to major EU cities.',
    loc_highway:'Highway exit Zagvozd (A1)',loc_transfers_h:'Transfer Services',loc_nearby_h:'Nearby',
    loc_t1:'Airport transfers (Split ↔ Podgora, Dubrovnik ↔ Podgora)',loc_t2:'Group minibus (8–20 passengers)',loc_t3:'Private day trips (Makarska, Split, Dubrovnik & islands)',
    attr_tag:'The Region',attr_h2:'The surrounding region.',
    attr_lead:'The Makarska Riviera sits at a crossroads of mountains, coast and islands. Day trips make a real difference to the overall program experience.',
    attr1_name:'Biokovo Mountain',attr2_name:'Skywalk Viewpoint',attr3_name:'Cetina River Canyon',attr8_name:'Local Wine & Olive Oil',attr8_time:'Regional producers',
    why_tag:'Why Medora',why_h2:'Why organizers<br>choose Medora.',why_lead:'The main reasons organizers, brands and teams come back.',
    why1_title:'Flexible & Scalable',why1_desc:'Competitive pricing in shoulder season (April–June and September–October), with packages adapted to your group size, format and budget.',
    why2_title:'Everything Already in Place',why2_desc:'Spa, wellness, fitness, healthy menus, outdoor activities and meeting spaces. All the infrastructure needed for a complete program, without extra coordination.',
    why3_title:'Proven for Large Groups',why3_desc:'Experience handling large capacities, backed by an organised operational team that knows how to run group programs efficiently.',
    why4_title:'Recognised Quality',why4_desc:'Top-rated across all major OTA platforms with a strong, stable presence on the regional market. Your participants arrive with positive expectations.',
    why5_title:'Long-Term Partnership',why5_desc:'We focus on building ongoing relationships with preferred partners: dedicated support, priority availability and better rates for repeat programs.',
    why6_title:'Simple from Start to Finish',why6_desc:'One point of contact, clear communication and a tailored proposal within 48 hours. No back-and-forth with multiple suppliers.',
    contact_tag:'Get in Touch',contact_h2:"Let's build the right package for your group.",
    contact_lead:"Use this form for retreat planning, brand collaborations, sports camps and corporate offsites. We'll reply with a tailored proposal within 48 hours.",
    contact_li1:"We'll get back to you with a tailored proposal within 48 hours",
    contact_li2:'Full support in organizing your program: you lead, or we connect you with a trusted agency',
    contact_li3:'Accommodation across hotel and campsite, adapted to your group size and concept',
    contact_li4:'All logistics handled in one place: stay, meals, spaces, activities',
    contact_li5:'Additional services available on request: transfers, excursions, special dining',
    form_name:'Name',form_name_ph:'Your name',form_org:'Organisation',form_org_ph:'Company or agency',
    form_type:'Program Type',form_select:'Select a type',form_size:'Group Size',form_size_ph:'Approx. number of people',
    form_dates:'Preferred Dates',form_dates_ph:'e.g. April–May 2025, flexible',
    form_details:'Program Details',form_details_ph:"Brief description of what you're planning. We'll take it from there.",
    form_opt1:'Creator / Brand Stay',form_opt2:'Wellness / Yoga Retreat',form_opt3:'Active / Sports Camp',form_opt4:'Corporate Events',form_opt5:'Other',
    form_submit:'Request a Proposal',
    form_consent:"By submitting this form, you agree to be contacted by Medora Hotels & Resorts regarding your enquiry. Your information will not be shared with third parties.",
    footer_copy:'Partnership & Group Enquiries',
    attr4_name:'Baćina Lakes',attr5_name:'Crveno Jezero',attr6_name:'Modro Jezero',attr7_name:'Dubrovnik',
    attr1_time:'15–20 min',attr2_time:'20 min',attr3_time:'40 min',attr4_time:'5–10 min',
    attr_imotski:'1.5 hrs · Imotski',attr_dubrovnik_time:'2 hrs 20 min',
    loc_near1:'Makarska',loc_spu_time:'105 km · ~1h 30 min',loc_dbv_time:'170 km · ~2h 30 min',
    footer_riviera:'Makarska Riviera',footer_country:'Croatia',
  },
  hr: {
    nav_properties:'Objekti',nav_programs:'Programi',nav_location:'Lokacija',nav_why:'Zašto Medora',nav_cta:'Zatražite ponudu',btn_explore:'Pogledajte objekte',
    hero_eyebrow:'Makarška rivijera · Dalmacija · Hrvatska',
    hero_h1:'Organizirajte svoj program<br>na <em>Makarskoj rivijeri.</em>',
    hero_sub:'<strong>Prostor za nezaboravne boravke, retreate i grupna iskustva.</strong><br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>Hotel uz more i premium kamp u Podgori.',
    intro_label_1:'Smještaj',intro_label_2:'Hrana i piće',intro_label_3:'Prostori i logistika',
    intro_text_1:'Hotelske sobe, mobilne kućice ili kombinacija. Fleksibilni rasporedi za svaku veličinu i format grupe.',
    intro_text_2:'Pouzdano ugostiteljstvo od dnevnog polupansiona do potpuno prilagođenih grupnih menija. Bez koordinacije s vaše strane.',
    intro_text_3:'Unutarnji i vanjski prostori spremni za korištenje. Aktivnosti, transferi i organizacijska podrška dostupni su putem nas.',
    venues_tag:'Objekti',venues_h2:'Dva objekta,<br>jedna organizacija.',
    venues_names:'Medora Auri Hotel i Medora Orbis Camp',
    venues_lead:'Medora Auri Hotel i Medora Orbis Camp nalaze se jedan pored drugog u Podgori, na Makarskoj rivijeri. Koristite jedan, drugi ili oba — ovisno o potrebama vašeg programa.',
    hotel_stars:'4★ Hotel uz more',
    hotel_desc:'Moderan hotel neposredno uz more. Pogodan za wellness retreate, korporativne evente, brand boravke i grupe koje žele strukturirano hotelsko iskustvo.',
    hotel_a1:'Restoran — grupne večere i eventi',hotel_a2:'Vanjski grijani bazeni',hotel_a3:'Spa i wellness sadržaji',hotel_a4:'Fitness centar s pogledom na more',hotel_a5:'Prostori za jogu i aktivnosti',hotel_a6:'Konferencijske i meeting sobe',
    camp_stars:'Premium kamp',
    camp_desc:'Deluxe mobilne kućice i kamp parcele, neke s privatnim grijanim bazenima. Opuštenija, outdoor baza, idealna za sportske kampove, aktivne retreate i grupe koje preferiraju prirodno okruženje.',
    camp_a1:'Deluxe mobilne kućice i kamp parcele',camp_a2:'Privatni grijani bazeni na odabranim mobilnim kućicama',camp_a3:'Vanjske terase i BBQ zone',camp_a4:'Bicikli i prostori za aktivnosti',camp_a5:'Društvene vanjske zone',
    gallery_tag:'Pogledajte',gallery_h2:'Prostori i sadržaji',
    prog_tag:'Što nudimo',prog_h2:'Programi koje organiziramo.',
    prog_lead:'Surađujemo s organizatorima, brendovima i timovima kojima je potreban prostor koji se brine o infrastrukturi — kako bi se mogli fokusirati na program.',
    prog1_title:'Kreatori i brendovi',prog1_desc:'Organizirani boravci, produkcija sadržaja i brand aktivacije. Lokacija funkcionira vizualno i logistički: more, planine, vanjsko svjetlo i sadržaji koji dobro izgledaju na fotografijama.',
    prog2_title:'Wellness i lifestyle retreati',prog2_desc:'Yoga, fitness i retreati oporavka s prostorima spremnim za korištenje, strukturiranim obrocima i mirnim, prirodnim okruženjem izvan ljetnih gužvi.',
    prog3_title:'Aktivni kampovi i sport',prog3_desc:'Nogometni kampovi, plesne grupe, fitness timovi i programi za mlade. Fleksibilan smještaj, vanjske zone za trening i puna F&B podrška.',
    prog4_title:'Korporativni eventi i timovi',prog4_desc:'Offsiteovi, radionice i strateške sesije. Sobe za sastanke, catering i smještaj na jednom mjestu — bez koordinacije više dobavljača.',
    reviews_tag:'Ocjene gostiju',reviews_h2:'Dosljedno vrhunski ocijenjeni<br>na Rivijeri.',
    reviews_lead:'Ocijenjeni među najboljim hotelima i kampovima na Makarskoj rivijeri na Googleu i Booking.com-u.',
    review_superb_3016:'3.016 recenzija',review_superb_367:'367 recenzija',reviews_word:'recenzija',
    loc_tag:'Kako doći',loc_h2:'Podgora, Makarška rivijera,<br>Dalmacija, Hrvatska.',
    loc_lead:'Između planine Biokovo i Jadranskog mora, 10 minuta od Makarske. Jednostavan dolazak iz Splitske zračne luke i dobra povezanost s glavnim europskim gradovima.',
    loc_highway:'Izlaz autoceste Zagvozd (A1)',loc_transfers_h:'Transferi',loc_nearby_h:'U blizini',
    loc_t1:'Transferi s aerodroma (Split ↔ Podgora, Dubrovnik ↔ Podgora)',loc_t2:'Grupni minibus (8–20 putnika)',loc_t3:'Privatni izleti (Makarska, Split, Dubrovnik i otoci)',
    attr_tag:'Regija',attr_h2:'Okolica.',
    attr_lead:'Makarška rivijera smještena je na raskrižju planina, mora i otoka. Izleti značajno obogaćuju ukupni doživljaj programa.',
    attr1_name:'Planina Biokovo',attr2_name:'Vidikovac Skywalk',attr3_name:'Kanjon rijeke Cetine',attr8_name:'Lokalna vina i maslinovo ulje',attr8_time:'Lokalni proizvođači',
    why_tag:'Zašto Medora',why_h2:'Zašto organizatori<br>biraju Medoru.',why_lead:'Glavni razlozi zbog kojih se organizatori, brendovi i timovi vraćaju.',
    why1_title:'Fleksibilno i skalabilno',why1_desc:'Konkurentne cijene u predsezoni i postsezoni (travanj–lipanj i rujan–listopad), s paketima prilagođenim veličini grupe, formatu i budžetu.',
    why2_title:'Sve je već na mjestu',why2_desc:'Spa, wellness, fitness, zdravi meniji, vanjske aktivnosti i prostori za sastanke. Sva infrastruktura potrebna za kompletan program, bez dodatne koordinacije.',
    why3_title:'Iskustvo s velikim grupama',why3_desc:'Iskustvo u radu s velikim kapacitetima, uz organizirani operativni tim koji zna kako efikasno voditi grupne programe.',
    why4_title:'Prepoznata kvaliteta',why4_desc:'Vrhunski ocijenjeni na svim glavnim OTA platformama sa snažnom i stabilnom prisutnošću na regionalnom tržištu. Vaši sudionici dolaze s pozitivnim očekivanjima.',
    why5_title:'Dugoročno partnerstvo',why5_desc:'Fokusiramo se na izgradnju dugoročnih odnosa s preferiranim partnerima: posvećena podrška, prioritetna dostupnost i bolje cijene za ponovljene programe.',
    why6_title:'Jednostavno od početka do kraja',why6_desc:'Jedna kontakt osoba, jasna komunikacija i prilagođena ponuda u roku 48 sati. Bez pregovaranja s više dobavljača.',
    contact_tag:'Kontaktirajte nas',contact_h2:'Izgradimo pravi paket za vašu grupu.',
    contact_lead:'Koristite ovaj obrazac za planiranje retreata, brand suradnje, sportske kampove i korporativne evente. Odgovorit ćemo prilagođenom ponudom u roku 48 sati.',
    contact_li1:'Odgovorit ćemo prilagođenom ponudom u roku 48 sati',
    contact_li2:'Puna podrška u organizaciji programa: vi vodite, ili vas povezujemo s provjerenom agencijom',
    contact_li3:'Smještaj u hotelu i kampu, prilagođen veličini i konceptu vaše grupe',
    contact_li4:'Sva logistika na jednom mjestu: smještaj, obroci, prostori, aktivnosti',
    contact_li5:'Dodatne usluge dostupne na upit: transferi, izleti, posebne večere',
    form_name:'Ime',form_name_ph:'Vaše ime',form_org:'Organizacija',form_org_ph:'Tvrtka ili agencija',
    form_type:'Vrsta programa',form_select:'Odaberite vrstu',form_size:'Veličina grupe',form_size_ph:'Otprilike broj osoba',
    form_dates:'Željeni termini',form_dates_ph:'npr. travanj–svibanj 2025, fleksibilno',
    form_details:'Detalji programa',form_details_ph:'Kratki opis onoga što planirate. Mi ćemo se pobrinuti za ostalo.',
    form_opt1:'Kreator / Brand boravak',form_opt2:'Wellness / Yoga retreat',form_opt3:'Aktivni / Sportski kamp',form_opt4:'Korporativni eventi',form_opt5:'Ostalo',
    form_submit:'Zatražite ponudu',
    form_consent:'Slanjem ovog obrasca pristajete da vas Medora Hotels & Resorts kontaktira u vezi vašeg upita. Vaši podaci neće biti dijeljeni s trećim stranama.',
    footer_copy:'Upiti za partnerstva i grupe',
    attr4_name:'Baćinska jezera',attr5_name:'Crveno jezero',attr6_name:'Modro jezero',attr7_name:'Dubrovnik',
    attr1_time:'15–20 min',attr2_time:'20 min',attr3_time:'40 min',attr4_time:'5–10 min',
    attr_imotski:'1,5 sati · Imotski',attr_dubrovnik_time:'2 sata 20 min',
    loc_near1:'Makarska',loc_spu_time:'105 km · ~1h 30 min',loc_dbv_time:'170 km · ~2h 30 min',
    footer_riviera:'Makarška rivijera',footer_country:'Hrvatska',
  },
  de: {
    nav_properties:'Unterkünfte',nav_programs:'Programme',nav_location:'Anreise',nav_why:'Warum Medora',nav_cta:'Angebot anfragen',btn_explore:'Objekte entdecken',
    hero_eyebrow:'Makarska Riviera · Dalmatien · Kroatien',
    hero_h1:'Ihr Programm an der<br><em>Makarska Riviera.</em>',
    hero_sub:'<strong>Ein Veranstaltungsort für unvergessliche Aufenthalte, Retreats und Gruppenreisen.</strong><br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>Ein Strandhotel und Premium-Campingplatz in Podgora.',
    intro_label_1:'Unterkunft',intro_label_2:'Verpflegung',intro_label_3:'Räume & Logistik',
    intro_text_1:'Hotelzimmer, Mobilhäuser oder eine Kombination. Flexible Optionen für jede Gruppengröße.',
    intro_text_2:'Zuverlässige Verpflegung von täglicher Halbpension bis zu individuellen Gruppenmenüs.',
    intro_text_3:'Innen- und Außenbereiche sofort nutzbar. Aktivitäten, Transfers und Veranstaltungsunterstützung inklusive.',
    venues_tag:'Die Unterkünfte',venues_h2:'Zwei Objekte,<br>eine Organisation.',
    venues_names:'Medora Auri Hotel und Medora Orbis Camp',
    venues_lead:'Das Medora Auri Hotel und der Medora Orbis Camp liegen in Podgora an der Makarska Riviera. Nutzen Sie eines, das andere oder beide — je nach Bedarf Ihres Programms.',
    hotel_stars:'4★ Strandhotel',
    hotel_desc:'Ein modernes Hotel direkt am Meer. Ideal für Wellness-Retreats, Firmenveranstaltungen, Markenaufenthalte und Gruppen, die ein strukturiertes Hotelerlebnis bevorzugen.',
    hotel_a1:'Restaurant — Gruppenessen und Events',hotel_a2:'Beheizte Außenpools',hotel_a3:'Spa & Wellness-Einrichtungen',hotel_a4:'Fitnesscenter mit Meerblick',hotel_a5:'Yoga- und Aktivitätsbereiche',hotel_a6:'Konferenz- und Besprechungsräume',
    camp_stars:'Premium Camping',
    camp_desc:'Deluxe-Mobilhäuser und Campingstellplätze, einige mit privatem beheiztem Pool. Eine entspannte, naturnahe Basis für Sportcamps, aktive Retreats und naturverbundene Gruppen.',
    camp_a1:'Deluxe-Mobilhäuser & Campingstellplätze',camp_a2:'Private beheizte Pools in ausgewählten Mobilhäusern',camp_a3:'Außenterrassen & BBQ-Bereiche',camp_a4:'Fahrräder & Aktivitätsbereiche',camp_a5:'Gesellige Außenzonen',
    gallery_tag:'Einblicke',gallery_h2:'Räume & Einrichtungen',
    prog_tag:'Was wir anbieten',prog_h2:'Unsere Programme.',
    prog_lead:'Wir arbeiten mit Veranstaltern, Marken und Teams zusammen, die einen Veranstaltungsort benötigen, der die Infrastruktur übernimmt.',
    prog1_title:'Creator & Markenpartnerschaften',prog1_desc:'Organisierte Aufenthalte, Content-Produktion und Markenaktivierungen. Der Standort überzeugt visuell und logistisch: Meer, Berge, natürliches Licht.',
    prog2_title:'Wellness & Lifestyle Retreats',prog2_desc:'Yoga-, Fitness- und Erholungsretreat mit einsatzbereiten Räumen, strukturierten Mahlzeiten und einer ruhigen Naturkulisse abseits der Sommermassen.',
    prog3_title:'Aktive Camps & Sport',prog3_desc:'Fußballcamps, Tanzgruppen, Fitnessteams und Jugendprogramme. Flexible Unterkunft, Outdoor-Trainingsgebiete und volle F&B-Unterstützung.',
    prog4_title:'Corporate & Team Events',prog4_desc:'Offsite-Veranstaltungen, Workshops und Strategiesitzungen. Besprechungsräume, Catering und Unterkunft an einem Ort.',
    reviews_tag:'Gästebewertungen',reviews_h2:'Dauerhaft top bewertet<br>an der Riviera.',
    reviews_lead:'Zu den bestbewerteten Hotels und Campingplätzen an der Makarska Riviera auf Google und Booking.com.',
    review_superb_3016:'3.016 Bewertungen',review_superb_367:'367 Bewertungen',reviews_word:'Bewertungen',
    loc_tag:'Anreise',loc_h2:'Podgora, Makarska Riviera,<br>Dalmatien, Kroatien.',
    loc_lead:'Zwischen dem Biokovo-Gebirge und der Adria, 10 Minuten von Makarska entfernt. Gut erreichbar vom Flughafen Split und bestens an europäische Städte angebunden.',
    loc_highway:'Autobahnausfahrt Zagvozd (A1)',loc_transfers_h:'Transferservice',loc_nearby_h:'In der Nähe',
    loc_t1:'Flughafentransfers (Split ↔ Podgora, Dubrovnik ↔ Podgora)',loc_t2:'Gruppenminibus (8–20 Personen)',loc_t3:'Private Tagesausflüge (Makarska, Split, Dubrovnik & Inseln)',
    attr_tag:'Die Region',attr_h2:'Die Umgebung.',
    attr_lead:'Die Makarska Riviera liegt am Schnittpunkt von Bergen, Küste und Inseln. Tagesausflüge bereichern das Programmerlebnis erheblich.',
    attr1_name:'Biokovo-Gebirge',attr2_name:'Skywalk Aussichtspunkt',attr3_name:'Cetina-Flussschlucht',attr8_name:'Lokale Weine & Olivenöl',attr8_time:'Regionale Erzeuger',
    why_tag:'Warum Medora',why_h2:'Warum Veranstalter<br>Medora wählen.',why_lead:'Die Hauptgründe, warum Veranstalter, Marken und Teams wiederkommen.',
    why1_title:'Flexibel & Skalierbar',why1_desc:'Wettbewerbsfähige Preise in der Vor- und Nachsaison (April–Juni und September–Oktober), mit Paketen für jede Gruppengröße und jedes Budget.',
    why2_title:'Alles bereits vorhanden',why2_desc:'Spa, Wellness, Fitness, gesunde Menüs, Outdoor-Aktivitäten und Besprechungsräume. Alle Infrastruktur für ein vollständiges Programm.',
    why3_title:'Bewährt für große Gruppen',why3_desc:'Erfahrung mit großen Kapazitäten, unterstützt durch ein organisiertes Betriebsteam, das Gruppenprogramme effizient durchführt.',
    why4_title:'Anerkannte Qualität',why4_desc:'Top-bewertet auf allen großen OTA-Plattformen mit starker, stabiler Präsenz auf dem regionalen Markt.',
    why5_title:'Langfristige Partnerschaft',why5_desc:'Wir konzentrieren uns auf den Aufbau dauerhafter Beziehungen mit bevorzugten Partnern: persönlicher Support, Prioritätsverfügbarkeit und bessere Konditionen.',
    why6_title:'Einfach von Anfang bis Ende',why6_desc:'Ein Ansprechpartner, klare Kommunikation und ein maßgeschneidertes Angebot innerhalb von 48 Stunden.',
    contact_tag:'Kontakt',contact_h2:'Lassen Sie uns das richtige Paket für Ihre Gruppe zusammenstellen.',
    contact_lead:'Nutzen Sie dieses Formular für Retreat-Planung, Markenkooperationen, Sportcamps und Firmenevents. Wir antworten mit einem maßgeschneiderten Angebot innerhalb von 48 Stunden.',
    contact_li1:'Wir melden uns innerhalb von 48 Stunden mit einem maßgeschneiderten Angebot',
    contact_li2:'Vollständige Unterstützung bei der Programmorganisation: Sie leiten, oder wir vermitteln eine vertrauenswürdige Agentur',
    contact_li3:'Unterkunft in Hotel und Camp, angepasst an Ihre Gruppengröße und Ihr Konzept',
    contact_li4:'Alle Logistik an einem Ort: Unterkunft, Mahlzeiten, Räume, Aktivitäten',
    contact_li5:'Zusätzliche Services auf Anfrage: Transfers, Ausflüge, besondere Dinner',
    form_name:'Name',form_name_ph:'Ihr Name',form_org:'Organisation',form_org_ph:'Unternehmen oder Agentur',
    form_type:'Programmtyp',form_select:'Typ auswählen',form_size:'Gruppengröße',form_size_ph:'Ca. Personenanzahl',
    form_dates:'Gewünschte Termine',form_dates_ph:'z.B. April–Mai 2025, flexibel',
    form_details:'Programmdetails',form_details_ph:'Kurze Beschreibung Ihres Plans. Wir übernehmen den Rest.',
    form_opt1:'Creator / Markenaufenthalt',form_opt2:'Wellness / Yoga Retreat',form_opt3:'Aktives / Sportcamp',form_opt4:'Corporate Events',form_opt5:'Sonstiges',
    form_submit:'Angebot anfragen',
    form_consent:'Mit dem Absenden dieses Formulars stimmen Sie zu, von Medora Hotels & Resorts bezüglich Ihrer Anfrage kontaktiert zu werden. Ihre Daten werden nicht an Dritte weitergegeben.',
    footer_copy:'Anfragen für Partnerschaften & Gruppen',
    attr4_name:'Baćina-Seen',attr5_name:'Crveno Jezero',attr6_name:'Modro Jezero',attr7_name:'Dubrovnik',
    attr1_time:'15–20 Min.',attr2_time:'20 Min.',attr3_time:'40 Min.',attr4_time:'5–10 Min.',
    attr_imotski:'1,5 Std. · Imotski',attr_dubrovnik_time:'2 Std. 20 Min.',
    loc_near1:'Makarska',loc_spu_time:'105 km · ~1h 30 Min.',loc_dbv_time:'170 km · ~2h 30 Min.',
    footer_riviera:'Makarska Riviera',footer_country:'Kroatien',
  },
  pl: {
    nav_properties:'Obiekty',nav_programs:'Programy',nav_location:'Dojazd',nav_why:'Dlaczego Medora',nav_cta:'Zapytaj o ofertę',btn_explore:'Zobacz obiekty',
    hero_eyebrow:'Riwiera Makarska · Dalmacja · Chorwacja',
    hero_h1:'Zorganizuj swój program<br>na <em>Riwierze Makarskiej.</em>',
    hero_sub:'<strong>Miejsce na niezapomniane pobyty, retreaty i wyjazdy grupowe.</strong><br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>Hotel przy plaży i kempink premium w Podgorze.',
    intro_label_1:'Zakwaterowanie',intro_label_2:'Wyżywienie',intro_label_3:'Przestrzenie i logistyka',
    intro_text_1:'Pokoje hotelowe, domki mobilne lub kombinacja. Elastyczne opcje dla każdej wielkości grupy.',
    intro_text_2:'Niezawodne wyżywienie — od codziennego półpensjonatu po w pełni dopasowane menu grupowe.',
    intro_text_3:'Przestrzenie wewnętrzne i zewnętrzne gotowe do użytku. Aktywności, transfery i wsparcie organizacyjne.',
    venues_tag:'Obiekty',venues_h2:'Dwa obiekty,<br>jedna organizacja.',
    venues_names:'Medora Auri Hotel i Medora Orbis Camp',
    venues_lead:'Medora Auri Hotel i Medora Orbis Camp znajdują się obok siebie w Podgorze na Riwierze Makarskiej. Korzystaj z jednego, drugiego lub obu — zależnie od potrzeb programu.',
    hotel_stars:'4★ Hotel przy plaży',
    hotel_desc:'Nowoczesny hotel bezpośrednio nad morzem. Idealny na retreaty wellness, eventy firmowe, pobyty brandowe i grupy preferujące ustrukturyzowane doświadczenie hotelowe.',
    hotel_a1:'Restauracja — obiady grupowe i eventy',hotel_a2:'Podgrzewane baseny zewnętrzne',hotel_a3:'Spa i wellness',hotel_a4:'Centrum fitness z widokiem na morze',hotel_a5:'Przestrzenie do jogi i aktywności',hotel_a6:'Sale konferencyjne i spotkań',
    camp_stars:'Kempink premium',
    camp_desc:'Luksusowe domki mobilne i miejsca campingowe, niektóre z prywatnymi podgrzewanymi basenami. Idealna baza dla obozów sportowych, aktywnych retretów i grup preferujących naturę.',
    camp_a1:'Luksusowe domki mobilne i miejsca campingowe',camp_a2:'Prywatne podgrzewane baseny w wybranych domkach',camp_a3:'Tarasy zewnętrzne i strefy BBQ',camp_a4:'Rowery i przestrzenie aktywności',camp_a5:'Towarzyskie strefy zewnętrzne',
    gallery_tag:'Galeria',gallery_h2:'Przestrzenie i udogodnienia',
    prog_tag:'Co organizujemy',prog_h2:'Nasze programy.',
    prog_lead:'Współpracujemy z organizatorami, markami i zespołami potrzebującymi miejsca, które zajmuje się infrastrukturą — by mogli skupić się na programie.',
    prog1_title:'Twórcy i partnerstwa marek',prog1_desc:'Zorganizowane pobyty, produkcja treści i aktywacje marek. Lokalizacja sprawdza się wizualnie i logistycznie: morze, góry, naturalne światło.',
    prog2_title:'Retreaty wellness i lifestyle',prog2_desc:'Retreaty jogi, fitness i regeneracji z gotowymi przestrzeniami, ustrukturyzowanymi posiłkami i spokojnym, naturalnym otoczeniem.',
    prog3_title:'Obozy aktywne i sportowe',prog3_desc:'Obozy piłkarskie, grupy taneczne, zespoły fitness i programy młodzieżowe. Elastyczne zakwaterowanie, strefy treningowe i pełne wsparcie F&B.',
    prog4_title:'Eventy firmowe i teamowe',prog4_desc:'Wyjazdowe spotkania, warsztaty i sesje strategiczne. Sale spotkań, catering i zakwaterowanie w jednym miejscu.',
    reviews_tag:'Oceny gości',reviews_h2:'Stale najwyżej oceniani<br>na Riwierze.',
    reviews_lead:'Oceniani jako jedne z najlepszych hoteli i kempingów na Riwierze Makarskiej w Google i Booking.com.',
    review_superb_3016:'3 016 opinii',review_superb_367:'367 opinii',reviews_word:'opinii',
    loc_tag:'Dojazd',loc_h2:'Podgora, Riwiera Makarska,<br>Dalmacja, Chorwacja.',
    loc_lead:'Między górami Biokovo a Adriatykiem, 10 minut od Makarskiej. Łatwy dojazd z lotniska Split i dobre połączenia z głównymi miastami UE.',
    loc_highway:'Zjazd autostradowy Zagvozd (A1)',loc_transfers_h:'Transfery',loc_nearby_h:'W pobliżu',
    loc_t1:'Transfery lotniskowe (Split ↔ Podgora, Dubrownik ↔ Podgora)',loc_t2:'Minibus grupowy (8–20 osób)',loc_t3:'Prywatne wycieczki (Makarska, Split, Dubrownik i wyspy)',
    attr_tag:'Region',attr_h2:'Okolice.',
    attr_lead:'Riwiera Makarska leży na skrzyżowaniu gór, wybrzeża i wysp. Wycieczki jednodniowe znacznie wzbogacają doświadczenie programu.',
    attr1_name:'Góra Biokovo',attr2_name:'Punkt widokowy Skywalk',attr3_name:'Kanion rzeki Cetiny',attr8_name:'Lokalne wino i oliwa z oliwek',attr8_time:'Lokalni producenci',
    why_tag:'Dlaczego Medora',why_h2:'Dlaczego organizatorzy<br>wybierają Medorę.',why_lead:'Główne powody, dla których organizatorzy, marki i zespoły wracają.',
    why1_title:'Elastyczność i skalowalność',why1_desc:'Konkurencyjne ceny w sezonie wiosennym i jesiennym (kwiecień–czerwiec i wrzesień–październik), z pakietami dopasowanymi do wielkości grupy i budżetu.',
    why2_title:'Wszystko już na miejscu',why2_desc:'Spa, wellness, fitness, zdrowe menu, aktywności na zewnątrz i sale spotkań. Cała infrastruktura potrzebna do pełnego programu.',
    why3_title:'Sprawdzeni przy dużych grupach',why3_desc:'Doświadczenie w obsłudze dużych pojemności, wspierane przez zorganizowany zespół operacyjny.',
    why4_title:'Uznana jakość',why4_desc:'Najwyżej oceniani na wszystkich głównych platformach OTA z silną, stabilną obecnością na rynku regionalnym.',
    why5_title:'Długoterminowe partnerstwo',why5_desc:'Koncentrujemy się na budowaniu długotrwałych relacji z preferowanymi partnerami: dedykowane wsparcie, priorytetowa dostępność i lepsze stawki.',
    why6_title:'Prosto od początku do końca',why6_desc:'Jeden punkt kontaktu, jasna komunikacja i dopasowana oferta w ciągu 48 godzin. Bez negocjacji z wieloma dostawcami.',
    contact_tag:'Kontakt',contact_h2:'Stwórzmy odpowiedni pakiet dla Twojej grupy.',
    contact_lead:'Użyj tego formularza do planowania retreatu, współpracy z marką, obozów sportowych i eventów firmowych. Odpowiemy dopasowaną ofertą w ciągu 48 godzin.',
    contact_li1:'Odpowiemy dopasowaną ofertą w ciągu 48 godzin',
    contact_li2:'Pełne wsparcie w organizacji programu: Ty prowadzisz, lub łączymy Cię ze sprawdzoną agencją',
    contact_li3:'Zakwaterowanie w hotelu i kempingu, dostosowane do wielkości i koncepcji Twojej grupy',
    contact_li4:'Cała logistyka w jednym miejscu: pobyt, posiłki, przestrzenie, aktywności',
    contact_li5:'Dodatkowe usługi na żądanie: transfery, wycieczki, specjalne kolacje',
    form_name:'Imię i nazwisko',form_name_ph:'Twoje imię',form_org:'Organizacja',form_org_ph:'Firma lub agencja',
    form_type:'Typ programu',form_select:'Wybierz typ',form_size:'Wielkość grupy',form_size_ph:'Przybliżona liczba osób',
    form_dates:'Preferowane terminy',form_dates_ph:'np. kwiecień–maj 2025, elastycznie',
    form_details:'Szczegóły programu',form_details_ph:'Krótki opis planu. Resztą się zajmiemy.',
    form_opt1:'Twórca / Pobyt brandowy',form_opt2:'Wellness / Retreat jogi',form_opt3:'Aktywny / Obóz sportowy',form_opt4:'Eventy firmowe',form_opt5:'Inne',
    form_submit:'Zapytaj o ofertę',
    form_consent:'Przesyłając ten formularz, wyrażasz zgodę na kontakt ze strony Medora Hotels & Resorts w sprawie Twojego zapytania. Twoje dane nie będą udostępniane osobom trzecim.',
    footer_copy:'Zapytania o partnerstwa i grupy',
    attr4_name:'Jeziora Baćina',attr5_name:'Crveno Jezero',attr6_name:'Modro Jezero',attr7_name:'Dubrownik',
    attr1_time:'15–20 min',attr2_time:'20 min',attr3_time:'40 min',attr4_time:'5–10 min',
    attr_imotski:'1,5 godz. · Imotski',attr_dubrovnik_time:'2 godz. 20 min',
    loc_near1:'Makarska',loc_spu_time:'105 km · ~1h 30 min',loc_dbv_time:'170 km · ~2h 30 min',
    footer_riviera:'Riwiera Makarska',footer_country:'Chorwacja',
  }
};

function setLang(lang) {
  const t = i18n[lang];
  if (!t) return;
  document.documentElement.lang = lang;
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.textContent.toLowerCase() === lang);
  });
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (t[key] !== undefined) el.textContent = t[key];
  });
  document.querySelectorAll('[data-i18n-html]').forEach(el => {
    const key = el.getAttribute('data-i18n-html');
    if (t[key] !== undefined) el.innerHTML = t[key];
  });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    if (t[key] !== undefined) el.placeholder = t[key];
  });
  const titles = {en:'Medora — Retreats, Camps & Brand Stays',hr:'Medora — Retreati, kampovi i grupni boravci',de:'Medora — Retreats, Camps & Markenaufenthalte',pl:'Medora — Retreaty, obozy i pobyty brandowe'};
  document.title = titles[lang] || titles.en;
  localStorage.setItem('medora_lang', lang);
}

(function() {
  const saved = localStorage.getItem('medora_lang');
  if (saved && i18n[saved]) setLang(saved);
})();
</script>
</body>
</html>
