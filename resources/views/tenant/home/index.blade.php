@extends('layouts.school-public')

@section('title', ($schoolName ?? 'School') . ' — Official Website')

@section('extra-style')
<style>
/* ═══════════════════════════════════════
   NAVBAR
═══════════════════════════════════════ */
.school-nav {
    background: var(--primary);
    position: sticky; top: 0; z-index: 1000;
    box-shadow: 0 2px 20px rgba(0,0,0,.18);
}
.school-nav .navbar-brand {
    display: flex; align-items: center; gap: 12px;
}
.nav-logo-circle {
    width: 44px; height: 44px; border-radius: 50%;
    background: #fff; display: flex; align-items: center;
    justify-content: center; font-family: var(--font-display);
    font-weight: 700; font-size: 16px; color: var(--primary);
    flex-shrink: 0;
}
.nav-school-name {
    font-family: var(--font-display);
    font-size: 15px; font-weight: 600; color: #fff;
    line-height: 1.2;
}
.nav-school-name small {
    display: block; font-family: var(--font-body);
    font-size: 11px; color: #a8c4e4; font-weight: 300;
}
.school-nav .nav-link {
    color: #c8dff5 !important; font-size: 13.5px;
    font-weight: 400; padding: 0.5rem 0.9rem !important;
    transition: color .2s;
}
.school-nav .nav-link:hover { color: #fff !important; }
.nav-login-btn {
    background: var(--accent); color: #fff !important;
    border-radius: 6px; padding: 7px 18px !important;
    font-weight: 500; font-size: 13px;
    transition: background .2s !important;
}
.nav-login-btn:hover { background: var(--accent-dark) !important; }
.navbar-toggler { border-color: rgba(255,255,255,.3); }
.navbar-toggler-icon { filter: invert(1); }

/* Ticker */
.news-ticker {
    background: var(--accent); color: #fff;
    font-size: 12.5px; overflow: hidden; white-space: nowrap;
    display: flex; align-items: center; height: 32px;
}
.ticker-label {
    background: var(--primary-dark); padding: 0 16px;
    font-weight: 500; height: 100%;
    display: flex; align-items: center; flex-shrink: 0;
    font-size: 11px; letter-spacing: .5px; text-transform: uppercase;
}
.ticker-track {
    flex: 1; overflow: hidden;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
}
.ticker-inner {
    display: inline-block;
    animation: ticker-scroll 30s linear infinite;
    padding-left: 100%;
}
.ticker-inner span { margin-right: 60px; }
@keyframes ticker-scroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* ═══════════════════════════════════════
   HERO
═══════════════════════════════════════ */
.hero-section {
    background: var(--primary);
    padding: 64px 0 0;
    overflow: hidden;
    position: relative;
}
.hero-section::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 70% 50%, rgba(232,145,26,.08) 0%, transparent 60%);
    pointer-events: none;
}
.hero-badge {
    display: inline-block;
    background: rgba(232,145,26,.15);
    border: 1px solid rgba(232,145,26,.4);
    color: #f5b85a; font-size: 11.5px;
    padding: 5px 14px; border-radius: 20px;
    font-weight: 500; letter-spacing: .3px;
    margin-bottom: 1.25rem;
}
.hero-title {
    font-size: clamp(28px, 4vw, 48px);
    font-weight: 700; color: #fff;
    line-height: 1.2; margin-bottom: 1.25rem;
}
.hero-title span { color: var(--accent); }
.hero-desc {
    font-size: 15px; color: #9bbfdc; line-height: 1.8;
    max-width: 480px; margin-bottom: 2rem;
}
.hero-cta-group { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-hero-primary {
    background: var(--accent); color: #fff;
    border: none; padding: 12px 28px;
    border-radius: 8px; font-size: 14px; font-weight: 500;
    cursor: pointer; transition: background .2s, transform .1s;
    display: inline-flex; align-items: center; gap: 8px;
}
.btn-hero-primary:hover { background: var(--accent-dark); transform: translateY(-1px); }
.btn-hero-outline {
    background: transparent; color: #fff;
    border: 1.5px solid rgba(255,255,255,.3);
    padding: 12px 28px; border-radius: 8px;
    font-size: 14px; cursor: pointer;
    transition: border-color .2s, background .2s;
    display: inline-flex; align-items: center; gap: 8px;
}
.btn-hero-outline:hover { border-color: #fff; background: rgba(255,255,255,.05); }
.hero-stats-strip {
    display: flex; gap: 2rem; margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,.1);
}
.hero-stat { text-align: left; }
.hero-stat .num {
    font-family: var(--font-display);
    font-size: 26px; font-weight: 700; color: var(--accent);
}
.hero-stat .lbl {
    font-size: 11.5px; color: #7ea8cc;
    text-transform: uppercase; letter-spacing: .5px;
}

/* Carousel */
.hero-carousel-wrap {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 16px 16px 0 0;
    overflow: hidden; height: 100%; min-height: 340px;
}
.carousel-inner { height: 100%; }
.carousel-item { height: 340px; }
.carousel-slide-img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-body); font-size: 13px;
    color: rgba(255,255,255,.4);
}
.slide-placeholder {
    width: 100%; height: 100%;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 12px;
    color: rgba(255,255,255,.35); font-size: 13px;
}
.slide-icon-wrap {
    width: 56px; height: 56px; border-radius: 50%;
    background: rgba(255,255,255,.08);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
}
.carousel-caption-custom {
    background: rgba(0,0,0,.5);
    padding: 8px 16px;
    position: absolute; bottom: 0; left: 0; right: 0;
}
.carousel-caption-custom p {
    font-size: 12px; color: rgba(255,255,255,.85); margin: 0;
}
.carousel-indicators [data-bs-target] {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,.4);
}
.carousel-indicators .active { background: var(--accent) !important; }

/* ═══════════════════════════════════════
   STATS BAR
═══════════════════════════════════════ */
.stats-bar {
    background: #fff;
    border-bottom: 1px solid var(--border);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
}
.stats-bar-inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.stat-box {
    padding: 1.5rem 1rem; text-align: center;
    border-right: 1px solid var(--border);
    transition: background .2s;
}
.stat-box:last-child { border-right: none; }
.stat-box:hover { background: var(--light-bg); }
.stat-box .s-num {
    font-family: var(--font-display);
    font-size: 30px; font-weight: 700; color: var(--primary);
    line-height: 1;
}
.stat-box .s-lbl {
    font-size: 12px; color: var(--text-muted);
    margin-top: 4px; text-transform: uppercase; letter-spacing: .5px;
}

/* ═══════════════════════════════════════
   SECTION HEADER
═══════════════════════════════════════ */
.sec-badge {
    display: inline-block;
    background: #EEF4FF; color: var(--primary);
    font-size: 11px; padding: 4px 14px;
    border-radius: 20px; font-weight: 500;
    letter-spacing: .4px; text-transform: uppercase;
    margin-bottom: 0.75rem;
}
.sec-title {
    font-size: clamp(22px, 3vw, 32px);
    font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;
}
.sec-subtitle {
    font-size: 15px; color: var(--text-muted);
    max-width: 520px; margin: 0 auto;
}

/* ═══════════════════════════════════════
   ABOUT
═══════════════════════════════════════ */
.about-section { padding: 80px 0; background: #fff; }
.about-img-wrap {
    border-radius: 16px; overflow: hidden;
    background: var(--light-bg); min-height: 380px;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--border); position: relative;
}
.about-img-placeholder {
    display: flex; flex-direction: column;
    align-items: center; gap: 12px;
    color: var(--text-muted); font-size: 13px;
}
.about-img-placeholder i { font-size: 48px; color: #b5c9e8; }
.about-accent-bar {
    width: 48px; height: 4px; background: var(--accent);
    border-radius: 2px; margin-bottom: 1.25rem;
}
.about-content h3 {
    font-size: 28px; font-weight: 700;
    color: var(--primary); margin-bottom: 1rem;
}
.about-content p {
    font-size: 14.5px; color: #4a5568; line-height: 1.8;
    margin-bottom: 1rem;
}
.about-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 1rem; }
.about-tag {
    background: #EEF4FF; color: var(--primary);
    font-size: 12.5px; padding: 5px 14px;
    border-radius: 20px; font-weight: 400;
}
.about-feature-list { margin: 1.5rem 0; }
.about-feature-list li {
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; color: #4a5568; margin-bottom: 10px;
    list-style: none;
}
.about-feature-list li i { color: var(--accent); font-size: 16px; }

/* ═══════════════════════════════════════
   PRINCIPAL
═══════════════════════════════════════ */
.principal-section {
    background: var(--primary);
    padding: 80px 0; position: relative; overflow: hidden;
}
.principal-section::after {
    content: '"'; font-family: var(--font-display);
    font-size: 400px; line-height: 1; color: rgba(255,255,255,.03);
    position: absolute; top: -80px; right: 40px;
    pointer-events: none; user-select: none;
}
.principal-card {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 20px; padding: 2.5rem;
    display: flex; gap: 2.5rem; align-items: flex-start;
}
.principal-avatar-wrap { flex-shrink: 0; text-align: center; }
.principal-avatar {
    width: 110px; height: 110px; border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 3px solid var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-display);
    font-size: 32px; font-weight: 700; color: #fff;
    margin-bottom: 12px;
}
.principal-name {
    font-size: 13px; font-weight: 500; color: #fff;
    margin-bottom: 2px;
}
.principal-designation { font-size: 11px; color: #7ea8cc; }
.principal-quote-mark {
    font-family: var(--font-display);
    font-size: 72px; line-height: 0.6;
    color: var(--accent); margin-bottom: 1rem;
    display: block;
}
.principal-message {
    font-size: 15.5px; color: #cce0f5;
    line-height: 1.85; font-style: italic;
    font-family: var(--font-display); font-weight: 400;
}

/* ═══════════════════════════════════════
   BENEFITS
═══════════════════════════════════════ */
.benefits-section { padding: 80px 0; background: var(--light-bg); }
.benefit-card {
    background: #fff; border: 1px solid var(--border);
    border-radius: 14px; padding: 2rem 1.5rem;
    text-align: center; height: 100%;
    transition: transform .2s, box-shadow .2s;
}
.benefit-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(27,63,122,.1);
}
.benefit-icon {
    width: 60px; height: 60px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem; font-size: 26px;
}
.benefit-card h4 {
    font-size: 16px; font-weight: 600;
    color: var(--primary); margin-bottom: 0.6rem;
}
.benefit-card p { font-size: 13.5px; color: var(--text-muted); line-height: 1.7; }

/* ═══════════════════════════════════════
   EVENTS & NOTICES (side by side)
═══════════════════════════════════════ */
.events-section { padding: 80px 0; background: #fff; }
.section-header-left { text-align: left; margin-bottom: 1.75rem; }
.sec-badge-dark {
    display: inline-block;
    background: rgba(27,63,122,.08); color: var(--primary);
    font-size: 11px; padding: 4px 14px;
    border-radius: 20px; font-weight: 500;
    letter-spacing: .4px; text-transform: uppercase;
    margin-bottom: 0.5rem;
}
.event-card {
    border: 1px solid var(--border); border-radius: 12px;
    overflow: hidden; margin-bottom: 12px;
    transition: box-shadow .2s;
}
.event-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }
.event-card-header {
    background: var(--primary); padding: 10px 16px;
    display: flex; align-items: center; gap: 12px;
}
.event-date-box {
    background: var(--accent); color: #fff;
    border-radius: 8px; padding: 4px 12px;
    text-align: center; flex-shrink: 0;
}
.event-date-box .day {
    font-family: var(--font-display);
    font-size: 18px; font-weight: 700; line-height: 1;
}
.event-date-box .month { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
.event-meta { color: rgba(255,255,255,.7); font-size: 12px; }
.event-meta strong { color: #fff; font-size: 13px; display: block; }
.event-card-body { padding: 14px 16px; background: #fff; }
.event-card-body p { font-size: 13px; color: var(--text-muted); margin: 0; }

.notice-item {
    border: 1px solid var(--border); border-radius: 10px;
    padding: 14px 16px; margin-bottom: 10px;
    display: flex; align-items: flex-start; gap: 12px;
    background: #fff; transition: box-shadow .2s;
}
.notice-item:hover { box-shadow: 0 3px 12px rgba(0,0,0,.06); }
.notice-dot {
    width: 9px; height: 9px; border-radius: 50%;
    flex-shrink: 0; margin-top: 5px;
}
.notice-dot.new  { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
.notice-dot.info { background: var(--primary); }
.notice-dot.warn { background: var(--accent); }
.notice-text { flex: 1; font-size: 13.5px; color: var(--text-main); }
.notice-right { flex-shrink: 0; text-align: right; }
.notice-badge {
    font-size: 10px; padding: 2px 9px; border-radius: 10px;
    font-weight: 500; display: inline-block; margin-bottom: 4px;
}
.badge-new  { background: #dcfce7; color: #166534; }
.badge-exam { background: #fef9c3; color: #854d0e; }
.badge-info { background: #dbeafe; color: #1e40af; }
.notice-date { font-size: 11px; color: var(--text-muted); }
.view-all-link {
    color: var(--primary); font-size: 13px; font-weight: 500;
    display: inline-flex; align-items: center; gap: 5px;
    transition: gap .15s;
}
.view-all-link:hover { gap: 8px; color: var(--accent); }

/* ═══════════════════════════════════════
   GALLERY
═══════════════════════════════════════ */
.gallery-section { padding: 80px 0; background: var(--light-bg); }
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: repeat(2, 180px);
    gap: 12px;
}
.gallery-grid .g-item-wide { grid-column: span 2; }
.gallery-item {
    border-radius: 12px; overflow: hidden;
    background: #dde6f3; border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 8px;
    color: var(--text-muted); font-size: 12px;
    cursor: pointer; transition: transform .2s, box-shadow .2s;
    position: relative;
}
.gallery-item:hover { transform: scale(1.02); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.gallery-item i { font-size: 28px; color: #8fafd6; }
.gallery-item-label {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: rgba(27,63,122,.75);
    color: #fff; font-size: 11.5px; padding: 6px 12px;
    opacity: 0; transition: opacity .2s;
}
.gallery-item:hover .gallery-item-label { opacity: 1; }

/* ═══════════════════════════════════════
   FAQ
═══════════════════════════════════════ */
.faq-section { padding: 80px 0; background: #fff; }
.accordion-item {
    border: 1px solid var(--border) !important;
    border-radius: 10px !important;
    margin-bottom: 10px; overflow: hidden;
}
.accordion-button {
    font-family: var(--font-body); font-size: 15px;
    font-weight: 500; color: var(--primary) !important;
    background: #fff !important; padding: 1.1rem 1.25rem;
}
.accordion-button:not(.collapsed) {
    background: var(--light-bg) !important;
    box-shadow: none !important; color: var(--primary) !important;
}
.accordion-button::after {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%231B3F7A' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
}
.accordion-body {
    font-size: 14px; color: #4a5568; line-height: 1.8;
    padding: 0.75rem 1.25rem 1.25rem;
    background: var(--light-bg);
}

/* ═══════════════════════════════════════
   CONTACT
═══════════════════════════════════════ */
.contact-section { padding: 80px 0; background: var(--light-bg); }
.contact-info-card {
    background: var(--primary); border-radius: 16px;
    padding: 2.5rem; height: 100%; color: #fff;
}
.contact-info-card h3 {
    font-size: 22px; font-weight: 700;
    color: #fff; margin-bottom: 0.5rem;
}
.contact-info-card > p { font-size: 14px; color: #9bbfdc; margin-bottom: 2rem; }
.contact-row-item {
    display: flex; align-items: flex-start; gap: 14px;
    margin-bottom: 1.5rem;
}
.contact-icon-box {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(255,255,255,.1);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 18px; color: var(--accent);
}
.contact-row-item strong { font-size: 13px; color: #fff; display: block; margin-bottom: 2px; }
.contact-row-item span { font-size: 13px; color: #9bbfdc; line-height: 1.6; display: block; }
.office-hours-box {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 10px; padding: 1rem 1.25rem; margin-top: 1.5rem;
}
.office-hours-box p { font-size: 12.5px; color: #9bbfdc; margin: 0; }
.office-hours-box p:first-child { color: var(--accent); font-weight: 500; margin-bottom: 6px; }

.contact-form-card {
    background: #fff; border-radius: 16px;
    padding: 2.5rem; border: 1px solid var(--border);
}
.contact-form-card h3 {
    font-size: 22px; font-weight: 700;
    color: var(--primary); margin-bottom: 1.75rem;
}
.form-label { font-size: 12.5px; color: var(--text-muted); margin-bottom: 5px; font-weight: 400; }
.form-control, .form-select {
    font-family: var(--font-body); font-size: 14px;
    border: 1px solid var(--border); border-radius: 8px;
    padding: 10px 14px; color: var(--text-main);
    background: #fafbfd; transition: border-color .2s, box-shadow .2s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(27,63,122,.1);
    background: #fff;
}
textarea.form-control { resize: none; height: 100px; }
.btn-submit {
    background: var(--primary); color: #fff;
    border: none; border-radius: 8px;
    padding: 12px 28px; font-size: 14px; font-weight: 500;
    width: 100%; cursor: pointer;
    transition: background .2s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { background: var(--primary-dark); transform: translateY(-1px); }

/* ═══════════════════════════════════════
   FOOTER
═══════════════════════════════════════ */
.school-footer {
    background: #0d2349; color: #8aaecc;
    padding: 60px 0 0;
}
.footer-brand-name {
    font-family: var(--font-display);
    font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 0.5rem;
}
.footer-brand-desc { font-size: 13px; line-height: 1.7; max-width: 240px; }
.footer-heading { font-size: 12px; color: #fff; font-weight: 500; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 1rem; }
.footer-links { list-style: none; padding: 0; }
.footer-links li { margin-bottom: 8px; }
.footer-links a { font-size: 13px; color: #8aaecc; transition: color .15s; }
.footer-links a:hover { color: var(--accent); }
.footer-divider { border-color: rgba(255,255,255,.07); margin: 2.5rem 0 1.25rem; }
.footer-bottom { font-size: 12.5px; padding-bottom: 1.5rem; }
.social-btn {
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    display: inline-flex; align-items: center; justify-content: center;
    color: #8aaecc; font-size: 14px; cursor: pointer;
    transition: background .2s, color .2s; margin-right: 6px;
}
.social-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

/* Animations */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-up { opacity: 0; }
.animate-fade-up.visible { animation: fadeUp .6s ease forwards; }

@media (max-width: 768px) {
    .stats-bar-inner { grid-template-columns: repeat(2, 1fr); }
    .gallery-grid { grid-template-columns: repeat(2, 1fr); grid-template-rows: auto; }
    .gallery-grid .g-item-wide { grid-column: span 2; }
    .principal-card { flex-direction: column; align-items: center; text-align: center; }
}
</style>
@endsection

@section('content')

{{-- ─── NAVBAR ─────────────────────────────────────────── --}}
<nav class="navbar navbar-expand-lg school-nav">
    <div class="container">
        <a class="navbar-brand" href="{{ route('tenant.home') }}">
            <div class="nav-logo-circle">
                {{ strtoupper(substr($schoolName ?? 'S', 0, 2)) }}
            </div>
            <div class="nav-school-name">
                {{ $schoolName ?? config('app.name') }}
                <small>Est. {{ $established ?? '2000' }} &nbsp;·&nbsp; {{ $board ?? 'CBSE' }} Affiliated</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#schoolNavMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="schoolNavMenu">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#benefits">Academics</a></li>
                <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="#notices">Notices</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item ms-2">
                    <a class="nav-link nav-login-btn" href="{{ route('tenant.login') }}">
                        <i class="icon-base ti tabler-login me-1" style="font-size:14px"></i>
                        Student Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ─── NEWS TICKER ───────────────────────────────────────── --}}
<div class="news-ticker">
    <div class="ticker-label">
        <i class="icon-base ti tabler-bell me-1" style="font-size:12px"></i> Latest
    </div>
    <div class="ticker-track">
        <div class="ticker-inner">
            <span>🎓 Admissions open for 2025–26 — Apply now at the school office</span>
            <span>📅 Annual Sports Day on 15 January 2025 — All parents invited</span>
            <span>📋 Half-yearly exam schedule published — Check notices section</span>
            <span>🏆 Our students ranked 1st in District Science Olympiad 2024</span>
            <span>📢 Republic Day celebration on 26 January — Flag hoisting at 8:00 AM</span>
        </div>
    </div>
</div>

{{-- ─── HERO ──────────────────────────────────────────────── --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-end g-4">
            <div class="col-lg-6 pb-lg-5">
                <div class="hero-badge">
                    <i class="icon-base ti tabler-star me-1" style="font-size:11px"></i>
                    Admissions open &nbsp;·&nbsp; 2025–26
                </div>
                <h1 class="hero-title">
                    Nurturing Minds,<br>
                    <span>Shaping Futures</span>
                </h1>
                <p class="hero-desc">
                    A premier {{ $board ?? 'CBSE' }} school committed to holistic education — blending academic excellence with values, sports, and the arts for over {{ $yearsOfExcellence ?? '25' }} years.
                </p>
                <div class="hero-cta-group">
                    <a href="#contact" class="btn-hero-primary">
                        <i class="icon-base ti tabler-school" style="font-size:16px"></i>
                        Apply for admission
                    </a>
                    <a href="#about" class="btn-hero-outline">
                        <i class="icon-base ti tabler-arrow-down" style="font-size:16px"></i>
                        Explore school
                    </a>
                </div>
                <div class="hero-stats-strip">
                    <div class="hero-stat">
                        <div class="num">{{ $totalStudents ?? '1,240' }}+</div>
                        <div class="lbl">Students</div>
                    </div>
                    <div class="hero-stat">
                        <div class="num">{{ $totalStaff ?? '86' }}</div>
                        <div class="lbl">Faculty</div>
                    </div>
                    <div class="hero-stat">
                        <div class="num">{{ $yearsOfExcellence ?? '38' }}</div>
                        <div class="lbl">Years</div>
                    </div>
                    <div class="hero-stat">
                        <div class="num">98%</div>
                        <div class="lbl">Pass rate</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-carousel-wrap">
                    <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
                        </div>
                        <div class="carousel-inner h-100">
                            @php
                                $slides = [
                                    ['img' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&q=80', 'label' => 'Annual Day 2024 — Prize Distribution'],
                                    ['img' => 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800&q=80', 'label' => 'Science & Innovation Fair 2024'],
                                    ['img' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&q=80', 'label' => 'Inter-School Sports Championship'],
                                    ['img' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80', 'label' => 'Cultural Fest — Tarang 2024'],
                                ];
                            @endphp
                            @foreach($slides as $i => $slide)
                            <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                                <img src="{{ $slide['img'] }}"
                                     alt="{{ $slide['label'] }}"
                                     style="width:100%;height:340px;object-fit:cover;display:block;">
                                <div class="carousel-caption-custom">
                                    <p>{{ $slide['label'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── STATS BAR ─────────────────────────────────────────── --}}
<div class="stats-bar">
    <div class="container">
        <div class="stats-bar-inner">
            <div class="stat-box">
                <div class="s-num">{{ $totalStudents ?? '1,240' }}+</div>
                <div class="s-lbl">Students enrolled</div>
            </div>
            <div class="stat-box">
                <div class="s-num">{{ $totalStaff ?? '86' }}</div>
                <div class="s-lbl">Qualified faculty</div>
            </div>
            <div class="stat-box">
                <div class="s-num">{{ $yearsOfExcellence ?? '38' }}</div>
                <div class="s-lbl">Years of excellence</div>
            </div>
            <div class="stat-box">
                <div class="s-num">98%</div>
                <div class="s-lbl">Board pass rate</div>
            </div>
        </div>
    </div>
</div>

{{-- ─── ABOUT ──────────────────────────────────────────────── --}}
<section class="about-section" id="about">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 animate-fade-up">
                <div class="about-img-wrap" style="min-height:380px">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80"
                         alt="School campus"
                         style="width:100%;height:100%;min-height:380px;object-fit:cover;display:block;border-radius:16px;">
                </div>
            </div>
            <div class="col-lg-7 animate-fade-up">
                <div class="about-accent-bar"></div>
                <div class="sec-badge">About us</div>
                <h3>A legacy of learning since {{ $established ?? '1985' }}</h3>
                <p>{{ $schoolName ?? 'Our school' }} has been a cornerstone of quality education in the region. We believe in the all-round development of every child — academically, physically, and morally.</p>
                <p>Our curriculum follows {{ $board ?? 'CBSE' }} guidelines with special emphasis on experiential learning, digital literacy, and cultural values rooted in Indian heritage.</p>
                <ul class="about-feature-list">
                    <li><i class="icon-base ti tabler-check"></i> Classes I to XII with dedicated subject teachers</li>
                    <li><i class="icon-base ti tabler-check"></i> Fully equipped science and computer laboratories</li>
                    <li><i class="icon-base ti tabler-check"></i> Smart classrooms with digital learning boards</li>
                    <li><i class="icon-base ti tabler-check"></i> Spacious sports ground and indoor games facility</li>
                </ul>
                <div class="about-tags">
                    <span class="about-tag">{{ $board ?? 'CBSE' }} affiliated</span>
                    <span class="about-tag">Classes I–XII</span>
                    <span class="about-tag">Smart classrooms</span>
                    <span class="about-tag">Sports complex</span>
                    <span class="about-tag">Science labs</span>
                    <span class="about-tag">Library</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── PRINCIPAL'S MESSAGE ─────────────────────────────────── --}}
<section class="principal-section">
    <div class="container">
        <div class="text-center mb-4">
            <div class="sec-badge" style="background:rgba(232,145,26,.15);color:#f5b85a">Principal's message</div>
            <h2 class="sec-title" style="color:#fff">A word from our principal</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="principal-card animate-fade-up">
                    <div class="principal-avatar-wrap">
                        <div class="principal-avatar" style="padding:0;overflow:hidden;">
                            <img src="https://images.unsplash.com/photo-1564564321837-a57b7070ac4f?w=200&q=80"
                                 alt="Principal"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        </div>
                        <div class="principal-name">Dr. Rajesh Pandey</div>
                        <div class="principal-designation">Principal</div>
                    </div>
                    <div>
                        <span class="principal-quote-mark">"</span>
                        <p class="principal-message">
                            Education is not the filling of a pail, but the lighting of a fire. At {{ $schoolName ?? 'our school' }}, we strive every day to ignite curiosity, build character, and prepare students not just for examinations — but for life itself. Our dedicated faculty, supportive community, and nurturing environment make this institution a place where every child can truly flourish and discover their greatest potential.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── BENEFITS ───────────────────────────────────────────── --}}
<section class="benefits-section" id="benefits">
    <div class="container">
        <div class="text-center mb-5 animate-fade-up">
            <div class="sec-badge">Why choose us</div>
            <h2 class="sec-title">Benefits of joining our school</h2>
            <p class="sec-subtitle">A rich learning environment designed to bring out the best in every student</p>
        </div>
        <div class="row g-4">
            @php
                $benefits = [
                    ['icon' => 'tabler-award', 'color' => '#EEF4FF', 'ic' => '#1B3F7A', 'title' => 'Academic excellence', 'desc' => 'Structured CBSE curriculum with experienced faculty and personalised attention for every student.'],
                    ['icon' => 'tabler-heartbeat', 'color' => '#F0FDF4', 'ic' => '#166534', 'title' => 'Holistic development', 'desc' => 'Sports, arts, music, dance, and debate clubs ensure well-rounded growth beyond the classroom.'],
                    ['icon' => 'tabler-device-desktop', 'color' => '#FEFCE8', 'ic' => '#854d0e', 'title' => 'Smart classrooms', 'desc' => 'Digital boards, projectors, and e-learning tools bring every lesson to life.'],
                    ['icon' => 'tabler-users', 'color' => '#FFF1F2', 'ic' => '#9f1239', 'title' => 'Experienced faculty', 'desc' => 'Over 86 qualified teachers with an average 12+ years of teaching experience in their subjects.'],
                    ['icon' => 'tabler-shield-check', 'color' => '#F0FDF4', 'ic' => '#166534', 'title' => 'Safe & secure campus', 'desc' => 'CCTV surveillance, trained security staff, and a fully fenced campus for student safety.'],
                    ['icon' => 'tabler-bus', 'color' => '#EEF4FF', 'ic' => '#1B3F7A', 'title' => 'Transport facility', 'desc' => 'GPS-tracked school buses covering all major routes, with trained drivers and attendants.'],
                ];
            @endphp
            @foreach($benefits as $b)
            <div class="col-md-6 col-lg-4 animate-fade-up">
                <div class="benefit-card">
                    <div class="benefit-icon" style="background:{{ $b['color'] }}">
                        <i class="icon-base ti {{ $b['icon'] }}" style="font-size:26px;color:{{ $b['ic'] }}"></i>
                    </div>
                    <h4>{{ $b['title'] }}</h4>
                    <p>{{ $b['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── EVENTS & NOTICES ─────────────────────────────────────── --}}
<section class="events-section" id="events">
    <div class="container">
        <div class="row g-5">
            {{-- Events --}}
            <div class="col-lg-6 animate-fade-up">
                <div class="section-header-left">
                    <div class="sec-badge-dark">Upcoming events</div>
                    <h2 class="sec-title">What's happening</h2>
                </div>
                @php
                    $events = [
                        ['day' => '15', 'month' => 'Jan', 'time' => 'Thursday · 9:00 AM', 'title' => 'Annual Sports Day 2025', 'desc' => 'Inter-house athletics competition at school ground. Parents warmly welcome.'],
                        ['day' => '26', 'month' => 'Jan', 'time' => 'Sunday · 8:00 AM', 'title' => 'Republic Day celebration', 'desc' => 'Flag hoisting ceremony followed by cultural programme by students.'],
                        ['day' => '10', 'month' => 'Feb', 'time' => 'Monday · 10:00 AM', 'title' => 'Science & innovation fair', 'desc' => 'Students present projects across classes VI–XII. Open to public.'],
                        ['day' => '28', 'month' => 'Feb', 'time' => 'Friday · 2:00 PM', 'title' => 'Parent–teacher meeting', 'desc' => 'Classes IX & X PTM. Please collect the schedule from the office.'],
                    ];
                @endphp
                @foreach($events as $event)
                <div class="event-card">
                    <div class="event-card-header">
                        <div class="event-date-box">
                            <div class="day">{{ $event['day'] }}</div>
                            <div class="month">{{ $event['month'] }}</div>
                        </div>
                        <div class="event-meta">
                            <strong>{{ $event['title'] }}</strong>
                            {{ $event['time'] }}
                        </div>
                    </div>
                    <div class="event-card-body">
                        <p>{{ $event['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Notices --}}
            <div class="col-lg-6 animate-fade-up" id="notices">
                <div class="section-header-left">
                    <div class="sec-badge-dark">Notices & circulars</div>
                    <h2 class="sec-title">Latest notices</h2>
                </div>
                @php
                    $notices = [
                        ['dot' => 'new', 'badge' => 'badge-new', 'badge_text' => 'New', 'text' => 'Admission form for Class I — Academic year 2025–26 now available at school office and for download below.', 'date' => '08 Jan 2025'],
                        ['dot' => 'new', 'badge' => 'badge-exam', 'badge_text' => 'Exam', 'text' => 'Half-yearly examination schedule for Classes VI–XII published. Download from student portal.', 'date' => '05 Jan 2025'],
                        ['dot' => 'info', 'badge' => 'badge-info', 'badge_text' => 'Holiday', 'text' => 'Winter vacation: School will remain closed from 25 Dec 2024 to 05 Jan 2025.', 'date' => '22 Dec 2024'],
                        ['dot' => 'warn', 'badge' => '', 'badge_text' => '', 'text' => 'Parent–Teacher Meeting for Classes IX & X scheduled on 28 December at 2:00 PM.', 'date' => '18 Dec 2024'],
                        ['dot' => 'warn', 'badge' => '', 'badge_text' => '', 'text' => 'Annual school fees for Term 2 due by 31 January 2025. Late fee applicable thereafter.', 'date' => '15 Dec 2024'],
                    ];
                @endphp
                @foreach($notices as $n)
                <div class="notice-item">
                    <div class="notice-dot {{ $n['dot'] }}"></div>
                    <div class="notice-text">{{ $n['text'] }}</div>
                    <div class="notice-right">
                        @if($n['badge'])
                            <div class="notice-badge {{ $n['badge'] }}">{{ $n['badge_text'] }}</div><br>
                        @endif
                        <span class="notice-date">{{ $n['date'] }}</span>
                    </div>
                </div>
                @endforeach
                <div class="mt-3">
                    <a href="#" class="view-all-link">
                        View all notices
                        <i class="icon-base ti tabler-arrow-right" style="font-size:14px"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── GALLERY ────────────────────────────────────────────── --}}
<section class="gallery-section" id="gallery">
    <div class="container">
        <div class="text-center mb-5 animate-fade-up">
            <div class="sec-badge">Gallery</div>
            <h2 class="sec-title">Life at our school</h2>
            <p class="sec-subtitle">A glimpse into the vibrant campus and student life</p>
        </div>
        <div class="gallery-grid animate-fade-up">
            @php
                $galleryItems = [
                    ['label' => 'Modern classrooms',  'img' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&q=80',  'wide' => true],
                    ['label' => 'Sports ground',      'img' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80',  'wide' => false],
                    ['label' => 'Science lab',        'img' => 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=400&q=80',  'wide' => false],
                    ['label' => 'Annual Day',          'img' => 'https://images.unsplash.com/photo-1533561052604-c3bebb6a5e7b?w=400&q=80',  'wide' => false],
                    ['label' => 'School library',      'img' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=400&q=80',  'wide' => false],
                    ['label' => 'Computer lab',        'img' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&q=80',  'wide' => false],
                    ['label' => 'Cultural programme',  'img' => 'https://images.unsplash.com/photo-1544531585-9847b68c8c86?w=800&q=80',  'wide' => true],
                ];
            @endphp
            @foreach($galleryItems as $g)
            <div class="gallery-item {{ $g['wide'] ? 'g-item-wide' : '' }}" style="padding:0;background:none;">
                <img src="{{ $g['img'] }}"
                     alt="{{ $g['label'] }}"
                     style="width:100%;height:100%;object-fit:cover;display:block;">
                <div class="gallery-item-label">{{ $g['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── FAQ ────────────────────────────────────────────────── --}}
<section class="faq-section" id="faq">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5 animate-fade-up">
                    <div class="sec-badge">FAQ</div>
                    <h2 class="sec-title">Frequently asked questions</h2>
                    <p class="sec-subtitle">Answers to common queries from parents and students</p>
                </div>
                <div class="accordion animate-fade-up" id="faqAccordion">
                    @php
                        $faqs = [
                            ['q' => 'What is the admission process for new students?',
                             'a' => 'Admissions open in January each year. Parents need to collect the application form from the school office or download it from this website, fill it out, and submit it with required documents. A written test and interaction is conducted for Class II onwards.'],
                            ['q' => 'Which board does the school follow?',
                             'a' => 'The school is affiliated to the Central Board of Secondary Education (CBSE), New Delhi. We follow the NCERT curriculum for all classes from I to XII.'],
                            ['q' => 'Is transport facility available?',
                             'a' => 'Yes. School buses operate on 12 routes covering all major areas. All buses are GPS-tracked and equipped with a lady attendant. Transport fees are charged separately on a term basis.'],
                            ['q' => 'What are the school timings?',
                             'a' => 'School operates Monday to Saturday. Summer: 7:30 AM – 1:30 PM. Winter: 9:00 AM – 3:00 PM. Saturday has half-day sessions for co-curricular activities.'],
                            ['q' => 'What is the fee structure?',
                             'a' => 'The fee structure varies by class. Detailed fee information is available at the school office and in the admission prospectus. Fees are collected on a quarterly basis.'],
                            ['q' => 'Does the school have a hostel facility?',
                             'a' => 'At present, we do not operate a hostel. However, we maintain a list of approved paying-guest accommodations near the school that parents may contact for outstation students.'],
                        ];
                    @endphp
                    @foreach($faqs as $i => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq{{ $i }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                {{ $faq['q'] }}
                            </button>
                        </h2>
                        <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">{{ $faq['a'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── CONTACT ─────────────────────────────────────────────── --}}
<section class="contact-section" id="contact">
    <div class="container">
        <div class="text-center mb-5 animate-fade-up">
            <div class="sec-badge">Contact us</div>
            <h2 class="sec-title">Get in touch</h2>
            <p class="sec-subtitle">We are happy to answer your queries and schedule a campus visit</p>
        </div>
        <div class="row g-4 animate-fade-up">
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h3>School information</h3>
                    <p>Reach us through any of the following channels</p>
                    <div class="contact-row-item">
                        <div class="contact-icon-box">
                            <i class="icon-base ti tabler-map-pin"></i>
                        </div>
                        <div>
                            <strong>Address</strong>
                            <span>{{ $address ?? 'Near Civil Hospital, Station Road, Raipur, Chhattisgarh – 492001' }}</span>
                        </div>
                    </div>
                    <div class="contact-row-item">
                        <div class="contact-icon-box">
                            <i class="icon-base ti tabler-phone"></i>
                        </div>
                        <div>
                            <strong>Phone</strong>
                            <span>{{ $phone ?? '+91 77100 00000' }} (Office)<br>{{ $principalPhone ?? '+91 98100 00000' }} (Principal)</span>
                        </div>
                    </div>
                    <div class="contact-row-item">
                        <div class="contact-icon-box">
                            <i class="icon-base ti tabler-mail"></i>
                        </div>
                        <div>
                            <strong>Email</strong>
                            <span>{{ $email ?? 'info@school.edu.in' }}<br>admissions@school.edu.in</span>
                        </div>
                    </div>
                    <div class="office-hours-box">
                        <p>Office hours</p>
                        <p>Monday – Saturday: 8:00 AM to 4:00 PM<br>Sunday & Public Holidays: Closed</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-form-card">
                    <h3>Send us a message</h3>
                    <form action="{{ route('tenant.home.contact') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Your name</label>
                                <input type="text" name="name" class="form-control" placeholder="Full name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" placeholder="your@email.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <select name="subject" class="form-select">
                                    <option>Admission enquiry</option>
                                    <option>General query</option>
                                    <option>Transport</option>
                                    <option>Fee structure</option>
                                    <option>Complaint</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" placeholder="Type your message here..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-submit">
                                    <i class="icon-base ti tabler-send" style="font-size:15px"></i>
                                    Send message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── FOOTER ─────────────────────────────────────────────── --}}
<footer class="school-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="nav-logo-circle" style="width:40px;height:40px;font-size:14px">
                        {{ strtoupper(substr($schoolName ?? 'S', 0, 2)) }}
                    </div>
                    <div class="footer-brand-name">{{ $schoolName ?? config('app.name') }}</div>
                </div>
                <p class="footer-brand-desc">Nurturing excellence and values since {{ $established ?? '1985' }}. {{ $board ?? 'CBSE' }} affiliated school serving the region with quality education.</p>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h4 class="footer-heading">Quick links</h4>
                <ul class="footer-links">
                    <li><a href="#about">About school</a></li>
                    <li><a href="#benefits">Academics</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#notices">Notices</a></li>
                    <li><a href="{{ route('tenant.login') }}">Student portal</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h4 class="footer-heading">Information</h4>
                <ul class="footer-links">
                    <li><a href="#faq">Fee structure</a></li>
                    <li><a href="#faq">School calendar</a></li>
                    <li><a href="#notices">Circulars</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="#contact">Downloads</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h4 class="footer-heading">Contact</h4>
                <p style="font-size:13px;line-height:1.8">
                    <i class="icon-base ti tabler-map-pin me-1" style="font-size:13px"></i>
                    {{ $address ?? 'Station Road, Raipur, CG' }}<br>
                    <i class="icon-base ti tabler-phone me-1" style="font-size:13px"></i>
                    {{ $phone ?? '+91 77100 00000' }}<br>
                    <i class="icon-base ti tabler-mail me-1" style="font-size:13px"></i>
                    {{ $email ?? 'info@school.edu.in' }}
                </p>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p>© {{ date('Y') }} {{ $schoolName ?? config('app.name') }}. All rights reserved.</p>
            <div>
                <a href="#" class="social-btn"><i class="icon-base ti tabler-brand-facebook" style="font-size:14px"></i></a>
                <a href="#" class="social-btn"><i class="icon-base ti tabler-brand-instagram" style="font-size:14px"></i></a>
                <a href="#" class="social-btn"><i class="icon-base ti tabler-brand-youtube" style="font-size:14px"></i></a>
                <a href="#" class="social-btn"><i class="icon-base ti tabler-brand-whatsapp" style="font-size:14px"></i></a>
            </div>
        </div>
    </div>
</footer>

@endsection

@section('page-script')
<script>
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, idx) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, idx * 80);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.animate-fade-up').forEach(el => observer.observe(el));

document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endsection