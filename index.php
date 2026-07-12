<?php
/**
 * BizPulse — Landing Page (index.php)
 *
 * Displays:
 *  - Hero section with company branding
 *  - Three service cards
 *  - Contact / lead capture form
 *  - Success alert after form submission
 */

// ── Page meta ─────────────────────────────────────────────────────────────
$pageTitle       = 'Helping Businesses Grow Digitally';
$metaDescription = 'BizPulse offers professional Web Design, SEO Optimization and Content Management services. Book a free consultation today.';

// ── Success flag from redirect after form submission ──────────────────────
$showSuccess = isset($_GET['success']) && $_GET['success'] === '1';

// ── Include shared header ─────────────────────────────────────────────────
require_once __DIR__ . '/includes/header.php';
?>

<!-- ══════════════════════════════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════════════════════════════ -->
<section class="hero-bg relative overflow-hidden" aria-label="Hero section">

    <!-- Decorative blobs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500 opacity-20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-blue-500 opacity-20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-28 text-center">

        <!-- Eyebrow badge -->
        <div class="inline-flex items-center gap-2 glass-card px-4 py-1.5 rounded-full text-indigo-300 text-xs font-semibold uppercase tracking-widest mb-6 animate-fade-in-up">
            <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-pulse"></span>
            Digital Growth Partner
        </div>

        <!-- Headline -->
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight mb-6 animate-fade-in-up" style="animation-delay:0.1s">
            Helping Businesses<br />
            <span class="bg-gradient-to-r from-indigo-400 via-blue-400 to-cyan-400 bg-clip-text text-transparent">
                Grow Digitally
            </span>
        </h1>

        <!-- Sub-headline -->
        <p class="max-w-2xl mx-auto text-slate-300 text-lg sm:text-xl leading-relaxed mb-10 animate-fade-in-up" style="animation-delay:0.2s">
            From stunning web design to data-driven SEO and strategic content management —
            we deliver measurable results that accelerate your business.
        </p>

        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up" style="animation-delay:0.3s">
            <a href="#contact"
               class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/40 text-sm">
                Book a Free Consultation
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <a href="#services"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 glass-card text-white font-semibold rounded-xl hover:bg-white/10 transition-colors text-sm">
                Explore Services
            </a>
        </div>

        <!-- Trust badges -->
        <div class="mt-14 flex flex-wrap justify-center gap-8 text-slate-400 animate-fade-in-up" style="animation-delay:0.4s" aria-label="Trust indicators">
            <div class="flex flex-col items-center gap-1">
                <span class="text-2xl font-bold text-white">150+</span>
                <span class="text-xs uppercase tracking-wider">Projects Delivered</span>
            </div>
            <div class="w-px bg-slate-700 self-stretch hidden sm:block" aria-hidden="true"></div>
            <div class="flex flex-col items-center gap-1">
                <span class="text-2xl font-bold text-white">98%</span>
                <span class="text-xs uppercase tracking-wider">Client Satisfaction</span>
            </div>
            <div class="w-px bg-slate-700 self-stretch hidden sm:block" aria-hidden="true"></div>
            <div class="flex flex-col items-center gap-1">
                <span class="text-2xl font-bold text-white">5★</span>
                <span class="text-xs uppercase tracking-wider">Average Rating</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════════
     SUCCESS ALERT
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ($showSuccess): ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 relative z-10" role="alert" aria-live="polite">
    <div class="success-alert flex items-start gap-4 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-lg p-5 mt-6">
        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <h3 class="font-semibold text-emerald-800 text-base">Enquiry Submitted Successfully!</h3>
            <p class="text-emerald-700 text-sm mt-0.5">
                Thank you for reaching out. Our team will review your request and get back to you within 24 hours.
            </p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()"
                class="ml-auto text-emerald-500 hover:text-emerald-700 transition-colors flex-shrink-0"
                aria-label="Dismiss alert">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════════════
     SERVICES SECTION
     ══════════════════════════════════════════════════════════════════════ -->
<section id="services" class="py-24 bg-white" aria-labelledby="services-heading">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16">
            <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">What We Do</p>
            <h2 id="services-heading" class="text-3xl sm:text-4xl font-bold text-slate-900 mb-4">
                Services Tailored for Growth
            </h2>
            <p class="max-w-xl mx-auto text-slate-500 text-base">
                We combine strategy, creativity, and technology to help your business stand out online.
            </p>
        </div>

        <!-- Service cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-children">

            <!-- Card 1: Web Design -->
            <article class="service-card animate-fade-in-up bg-white rounded-2xl border border-slate-100 shadow-sm p-8 flex flex-col group hover:border-indigo-200">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-md group-hover:shadow-indigo-300 transition-shadow">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Web Design</h3>
                <p class="text-slate-500 text-sm leading-relaxed flex-grow">
                    We craft stunning, pixel-perfect websites that captivate visitors and convert them into loyal customers.
                    Responsive, fast, and built for performance.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-slate-600">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        Mobile-first responsive design
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        UI/UX optimised for conversions
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        CMS integration available
                    </li>
                </ul>
                <a href="#contact"
                   class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors group-hover:gap-2.5">
                    Get Started
                    <svg class="w-4 h-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </article>

            <!-- Card 2: SEO Optimization -->
            <article class="service-card animate-fade-in-up bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-xl p-8 flex flex-col text-white">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-semibold px-2.5 py-1 rounded-full w-fit mb-3">
                    ✦ Most Popular
                </div>
                <h3 class="text-xl font-bold text-white mb-3">SEO Optimization</h3>
                <p class="text-indigo-100 text-sm leading-relaxed flex-grow">
                    Dominate search rankings with our data-driven SEO strategies. We boost organic traffic, improve
                    domain authority, and drive quality leads to your website.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-indigo-100">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        Keyword research &amp; strategy
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        On-page &amp; technical SEO
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        Monthly performance reports
                    </li>
                </ul>
                <a href="#contact"
                   class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-white hover:text-indigo-200 transition-colors">
                    Get Started
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </article>

            <!-- Card 3: Content Management -->
            <article class="service-card animate-fade-in-up bg-white rounded-2xl border border-slate-100 shadow-sm p-8 flex flex-col group hover:border-indigo-200">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-md group-hover:shadow-purple-300 transition-shadow">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Content Management</h3>
                <p class="text-slate-500 text-sm leading-relaxed flex-grow">
                    Consistent, high-quality content is the backbone of brand authority. We manage your editorial
                    calendar, create engaging copy, and keep your audience coming back.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-slate-600">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        Blog &amp; social media content
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        SEO-optimised copywriting
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        Brand voice development
                    </li>
                </ul>
                <a href="#contact"
                   class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors group-hover:gap-2.5">
                    Get Started
                    <svg class="w-4 h-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </article>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════════
     CONTACT / LEAD FORM SECTION
     ══════════════════════════════════════════════════════════════════════ -->
<section id="contact" class="py-24 bg-slate-50" aria-labelledby="contact-heading">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

            <!-- Left: copy -->
            <div class="animate-fade-in-up">
                <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">Free Consultation</p>
                <h2 id="contact-heading" class="text-3xl sm:text-4xl font-bold text-slate-900 mb-5 leading-tight">
                    Book a Free<br />
                    <span class="bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">Consultation</span>
                </h2>
                <p class="text-slate-500 text-base leading-relaxed mb-8">
                    Tell us about your project and we'll get back to you within 24 hours with a personalised plan.
                    No commitments, no hidden fees.
                </p>

                <!-- Benefits -->
                <div class="space-y-4">
                    <?php
                    $benefits = [
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Response within 24 hours'],
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'No obligation, completely free'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Dedicated senior consultant'],
                    ];
                    foreach ($benefits as $b): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $b['icon'] ?>" />
                            </svg>
                        </div>
                        <span class="text-slate-700 font-medium text-sm"><?= htmlspecialchars($b['text']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: form -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 animate-fade-in-up" style="animation-delay:0.15s">
                <form id="contact-form"
                      action="/submit.php"
                      method="POST"
                      novalidate>

                    <!-- CSRF-like hidden field (simple demo token) -->
                    <input type="hidden" name="form_token" value="<?= htmlspecialchars(bin2hex(random_bytes(16))) ?>" />

                    <!-- Full Name -->
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Full Name <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-input w-full px-4 py-3 border border-slate-200 rounded-xl text-sm placeholder-slate-400 bg-slate-50 focus:bg-white"
                               placeholder="e.g. John Smith"
                               maxlength="100"
                               required
                               autocomplete="name" />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Email Address <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-input w-full px-4 py-3 border border-slate-200 rounded-xl text-sm placeholder-slate-400 bg-slate-50 focus:bg-white"
                               placeholder="john@yourcompany.com"
                               maxlength="150"
                               required
                               autocomplete="email" />
                    </div>

                    <!-- Service -->
                    <div class="mb-5">
                        <label for="service" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Service Required <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <select id="service"
                                name="service"
                                class="form-input w-full px-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-700 bg-slate-50 focus:bg-white appearance-none"
                                required>
                            <option value="" disabled selected>— Select a service —</option>
                            <option value="Web Design">Web Design</option>
                            <option value="SEO Optimization">SEO Optimization</option>
                            <option value="Content Management">Content Management</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Your Message <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <textarea id="message"
                                  name="message"
                                  rows="4"
                                  class="form-input w-full px-4 py-3 border border-slate-200 rounded-xl text-sm placeholder-slate-400 bg-slate-50 focus:bg-white resize-none"
                                  placeholder="Tell us about your project, goals, or any specific requirements…"
                                  maxlength="2000"
                                  required></textarea>
                        <p class="mt-1 text-xs text-slate-400" aria-live="polite" id="char-counter">0 / 2000 characters</p>
                    </div>

                    <!-- Validation error display -->
                    <div id="form-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm" role="alert"></div>

                    <!-- Submit button -->
                    <button type="submit"
                            id="submit-btn"
                            class="btn-primary w-full flex items-center justify-center gap-2 py-3.5 px-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-300 text-sm animate-pulse-glow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send My Enquiry
                    </button>

                    <p class="text-xs text-slate-400 text-center mt-3">
                        By submitting you agree to our privacy policy. No spam, ever.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ── Client-side form validation script ─────────────────────────────── -->
<script>
(function () {
    'use strict';

    const form        = document.getElementById('contact-form');
    const errorBox    = document.getElementById('form-error');
    const submitBtn   = document.getElementById('submit-btn');
    const messageArea = document.getElementById('message');
    const charCounter = document.getElementById('char-counter');

    // ── Character counter ────────────────────────────────────────────────
    if (messageArea && charCounter) {
        messageArea.addEventListener('input', function () {
            charCounter.textContent = `${this.value.length} / 2000 characters`;
        });
    }

    // ── Client-side validation ────────────────────────────────────────────
    if (form) {
        form.addEventListener('submit', function (e) {
            const name    = document.getElementById('name').value.trim();
            const email   = document.getElementById('email').value.trim();
            const service = document.getElementById('service').value.trim();
            const message = document.getElementById('message').value.trim();

            const errors = [];

            if (!name)                          errors.push('Full Name is required.');
            if (!email)                         errors.push('Email Address is required.');
            else if (!isValidEmail(email))      errors.push('Please enter a valid email address.');
            if (!service)                       errors.push('Please select a service.');
            if (!message)                       errors.push('Message is required.');
            else if (message.length < 10)       errors.push('Message must be at least 10 characters.');

            if (errors.length > 0) {
                e.preventDefault(); // Stop form submission
                errorBox.innerHTML = errors.map(err => `<p>• ${err}</p>`).join('');
                errorBox.classList.remove('hidden');
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                errorBox.classList.add('hidden');
                // Show loading state
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Sending…';
            }
        });
    }

    /**
     * isValidEmail — lightweight regex check
     * Backend always validates as well (never trust client-side only)
     */
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
