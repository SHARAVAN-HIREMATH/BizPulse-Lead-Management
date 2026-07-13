<?php
/**
 * BizPulse — Landing Page (index.php) v2
 *
 * Changes in v2:
 *  - Beautiful animated success modal (replaces simple alert div)
 *  - Full dark mode support across all sections
 *  - Improved service card styling
 *  - Error handling for failed form submissions
 */

$pageTitle       = 'Helping Businesses Grow Digitally';
$metaDescription = 'BizPulse offers professional Web Design, SEO Optimization and Content Management services. Book a free consultation today.';

$showSuccess = isset($_GET['success']) && $_GET['success'] === '1';
$showError   = isset($_GET['error'])   && !empty($_GET['error']);
$errorMsg    = $showError ? htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES, 'UTF-8') : '';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ══════════════════════════════════════════════════════════════════════════
     SUCCESS MODAL
     Triggered by ?success=1 after form submission (PRG pattern)
     ══════════════════════════════════════════════════════════════════════════ -->
<?php if ($showSuccess): ?>
<div id="success-modal"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 modal-overlay"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title"
     aria-describedby="modal-desc">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeSuccessModal()"></div>

    <!-- Modal Card -->
    <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl max-w-md w-full p-8 text-center modal-card border border-slate-100 dark:border-slate-700">

        <!-- Close button -->
        <button onclick="closeSuccessModal()"
                class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
                aria-label="Close modal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Animated checkmark -->
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/30 checkmark-circle">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <!-- Content -->
        <h2 id="modal-title" class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
            Thank you for contacting BizPulse!
        </h2>
        <p id="modal-desc" class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-1">
            Your inquiry has been submitted successfully.
        </p>
        <p class="text-slate-500 dark:text-slate-500 text-sm mb-6">
            Our team will review your request and contact you within <strong class="text-indigo-600 dark:text-indigo-400">24 hours</strong>.
        </p>

        <!-- Countdown progress bar -->
        <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-5" role="progressbar" aria-label="Auto-close countdown">
            <div id="modal-progress"
                 class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 rounded-full modal-progress"
                 style="animation-duration: 5s; animation-fill-mode: both; transform-origin: left;">
            </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500 mb-5">
            This message will close automatically in <span id="countdown">5</span> seconds
        </p>

        <button onclick="closeSuccessModal()"
                class="w-full py-3 px-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-indigo-300/50 hover:shadow-lg transition-all text-sm">
            Close &amp; Return to Page
        </button>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════════════════
     ERROR BANNER (from failed form submission)
     ══════════════════════════════════════════════════════════════════════════ -->
<?php if ($showError && !empty($errorMsg)): ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 relative z-10" role="alert" aria-live="polite">
    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 animate-slide-down">
        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-red-800 dark:text-red-300">Submission Failed</p>
            <p class="text-sm text-red-700 dark:text-red-400 mt-0.5"><?= $errorMsg ?></p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()"
                class="text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors"
                aria-label="Dismiss">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════════════════════════════════ -->
<section class="hero-bg relative overflow-hidden" aria-label="Hero section">
    <!-- Decorative blobs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500 opacity-20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-blue-500 opacity-20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-28 text-center">

        <!-- Eyebrow -->
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

<!-- ══════════════════════════════════════════════════════════════════════════
     SERVICES SECTION
     ══════════════════════════════════════════════════════════════════════════ -->
<section id="services" class="py-24 bg-white dark:bg-slate-900 transition-colors duration-300" aria-labelledby="services-heading">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm uppercase tracking-widest mb-3">What We Do</p>
            <h2 id="services-heading" class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Services Tailored for Growth
            </h2>
            <p class="max-w-xl mx-auto text-slate-500 dark:text-slate-400 text-base">
                We combine strategy, creativity, and technology to help your business stand out online.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-children">

            <!-- Card 1: Web Design -->
            <article class="service-card animate-fade-in-up bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-8 flex flex-col group hover:border-indigo-200 dark:hover:border-indigo-700">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-md group-hover:shadow-indigo-300/40 transition-shadow">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Web Design</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed flex-grow">
                    We craft stunning, pixel-perfect websites that captivate visitors and convert them into customers.
                    Responsive, fast, and built for performance.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <?php foreach(['Mobile-first responsive design', 'UI/UX optimised for conversions', 'CMS integration available'] as $f): ?>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        <?= $f ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="#contact" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                    Get Started <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </article>

            <!-- Card 2: SEO (featured) -->
            <article class="service-card animate-fade-in-up bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-xl p-8 flex flex-col text-white">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-semibold px-2.5 py-1 rounded-full w-fit mb-3">✦ Most Popular</div>
                <h3 class="text-xl font-bold text-white mb-3">SEO Optimization</h3>
                <p class="text-indigo-100 text-sm leading-relaxed flex-grow">
                    Dominate search rankings with data-driven SEO strategies. We boost organic traffic,
                    improve domain authority, and drive quality leads.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-indigo-100">
                    <?php foreach(['Keyword research & strategy', 'On-page & technical SEO', 'Monthly performance reports'] as $f): ?>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        <?= $f ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="#contact" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-white hover:text-indigo-200 transition-colors">
                    Get Started <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </article>

            <!-- Card 3: Content -->
            <article class="service-card animate-fade-in-up bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-8 flex flex-col group hover:border-indigo-200 dark:hover:border-indigo-700">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-md group-hover:shadow-purple-300/40 transition-shadow">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Content Management</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed flex-grow">
                    Consistent, high-quality content is the backbone of brand authority. We manage your editorial
                    calendar, create copy, and keep your audience engaged.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <?php foreach(['Blog & social media content', 'SEO-optimised copywriting', 'Brand voice development'] as $f): ?>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        <?= $f ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="#contact" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                    Get Started <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </article>

        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════════════════
     CONTACT FORM SECTION
     ══════════════════════════════════════════════════════════════════════════ -->
<section id="contact" class="py-24 bg-slate-50 dark:bg-slate-950 transition-colors duration-300" aria-labelledby="contact-heading">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

            <!-- Left: copy + benefits -->
            <div class="animate-fade-in-up">
                <p class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm uppercase tracking-widest mb-3">Free Consultation</p>
                <h2 id="contact-heading" class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-5 leading-tight">
                    Book a Free<br />
                    <span class="bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">Consultation</span>
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-8">
                    Tell us about your project and we'll get back to you within 24 hours with a personalised plan.
                    No commitments, no hidden fees.
                </p>

                <?php
                $benefits = [
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Response within 24 hours'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'No obligation, completely free'],
                    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Dedicated senior consultant'],
                ];
                ?>
                <div class="space-y-4">
                    <?php foreach ($benefits as $b): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $b['icon'] ?>" />
                            </svg>
                        </div>
                        <span class="text-slate-700 dark:text-slate-300 font-medium text-sm"><?= htmlspecialchars($b['text']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 p-8 animate-fade-in-up" style="animation-delay:0.15s">
                <form id="contact-form" action="/submit.php" method="POST" novalidate>

                    <!-- CSRF-like token -->
                    <input type="hidden" name="form_token" value="<?= htmlspecialchars(bin2hex(random_bytes(16))) ?>" />

                    <!-- Full Name -->
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Full Name <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-input w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm placeholder-slate-400 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-600"
                               placeholder="e.g. John Smith"
                               maxlength="100" required autocomplete="name" />
                    </div>

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Email Address <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               class="form-input w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm placeholder-slate-400 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-600"
                               placeholder="john@yourcompany.com"
                               maxlength="150" required autocomplete="email" />
                    </div>

                    <!-- Service -->
                    <div class="mb-5">
                        <label for="service" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Service Required <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <select id="service" name="service"
                                class="form-input w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm text-slate-700 dark:text-white bg-slate-50 dark:bg-slate-700 appearance-none"
                                required>
                            <option value="" disabled selected>— Select a service —</option>
                            <option value="Web Design">Web Design</option>
                            <option value="SEO Optimization">SEO Optimization</option>
                            <option value="Content Management">Content Management</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Your Message <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <textarea id="message" name="message" rows="4"
                                  class="form-input w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm placeholder-slate-400 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-600 resize-none"
                                  placeholder="Tell us about your project…"
                                  maxlength="2000" required></textarea>
                        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500" aria-live="polite" id="char-counter">0 / 2000 characters</p>
                    </div>

                    <!-- Client-side error display -->
                    <div id="form-error" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm" role="alert"></div>

                    <!-- Submit -->
                    <button type="submit" id="submit-btn"
                            class="btn-primary w-full flex items-center justify-center gap-2 py-3.5 px-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-300/50 text-sm animate-pulse-glow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span id="btn-label">Send My Enquiry</span>
                    </button>

                    <p class="text-xs text-slate-400 dark:text-slate-500 text-center mt-3">
                        By submitting you agree to our privacy policy. No spam, ever.
                    </p>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- ── Scripts ─────────────────────────────────────────────────────────────── -->
<script>
'use strict';

// ── Success Modal Logic ────────────────────────────────────────────────────
<?php if ($showSuccess): ?>
(function () {
    var modal      = document.getElementById('success-modal');
    var progress   = document.getElementById('modal-progress');
    var countdown  = document.getElementById('countdown');
    var seconds    = 5;
    var timer      = null;
    var countTimer = null;

    // Start countdown progress bar
    if (progress) {
        progress.style.animation = 'countdown ' + seconds + 's linear both';
    }

    // Countdown number
    countTimer = setInterval(function () {
        seconds--;
        if (countdown) countdown.textContent = Math.max(seconds, 0);
        if (seconds <= 0) {
            clearInterval(countTimer);
        }
    }, 1000);

    // Auto-close
    timer = setTimeout(function () {
        closeSuccessModal();
    }, seconds * 1000);

    // Expose close function globally
    window.closeSuccessModal = function () {
        clearTimeout(timer);
        clearInterval(countTimer);
        if (modal) {
            modal.style.transition = 'opacity 0.3s ease';
            modal.style.opacity = '0';
            setTimeout(function () {
                modal.remove();
                // Clean the URL without page reload
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 300);
        }
    };
})();
<?php else: ?>
window.closeSuccessModal = function () {};
<?php endif; ?>

// ── Client-side Form Validation ────────────────────────────────────────────
(function () {
    var form      = document.getElementById('contact-form');
    var errorBox  = document.getElementById('form-error');
    var submitBtn = document.getElementById('submit-btn');
    var btnLabel  = document.getElementById('btn-label');
    var msgArea   = document.getElementById('message');
    var counter   = document.getElementById('char-counter');

    // Character counter
    if (msgArea && counter) {
        msgArea.addEventListener('input', function () {
            counter.textContent = this.value.length + ' / 2000 characters';
        });
    }

    // Validation on submit
    if (form) {
        form.addEventListener('submit', function (e) {
            var name    = document.getElementById('name').value.trim();
            var email   = document.getElementById('email').value.trim();
            var service = document.getElementById('service').value.trim();
            var message = document.getElementById('message').value.trim();
            var errors  = [];

            if (!name)                        errors.push('Full Name is required.');
            if (!email)                       errors.push('Email Address is required.');
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Please enter a valid email address.');
            if (!service)                     errors.push('Please select a service.');
            if (!message)                     errors.push('Message is required.');
            else if (message.length < 10)     errors.push('Message must be at least 10 characters.');

            if (errors.length > 0) {
                e.preventDefault();
                errorBox.innerHTML = errors.map(function(e){ return '<p>• ' + e + '</p>'; }).join('');
                errorBox.classList.remove('hidden');
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                errorBox.classList.add('hidden');
                if (submitBtn) submitBtn.disabled = true;
                if (btnLabel)  btnLabel.textContent = 'Sending…';
            }
        });
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
