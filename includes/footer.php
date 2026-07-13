<!-- ═══════════════════════════════════════════════════════════════════════════
     FOOTER (includes/footer.php)
     ═══════════════════════════════════════════════════════════════════════════ -->
<footer class="bg-slate-900 dark:bg-slate-950 text-slate-400 mt-20 transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            <!-- Brand -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center shadow">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">BizPulse</span>
                </div>
                <p class="text-sm leading-relaxed text-slate-400">
                    Helping businesses grow digitally through cutting-edge web solutions,
                    SEO strategies, and compelling content.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/index.php"          class="hover:text-indigo-400 transition-colors">Home</a></li>
                    <li><a href="/index.php#services" class="hover:text-indigo-400 transition-colors">Services</a></li>
                    <li><a href="/index.php#contact"  class="hover:text-indigo-400 transition-colors">Contact</a></li>
                    <li><a href="/admin.php"          class="hover:text-indigo-400 transition-colors">Admin Dashboard</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Our Services</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li>Web Design</li>
                    <li>SEO Optimization</li>
                    <li>Content Management</li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-slate-800 dark:border-slate-700 mt-10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <span>&copy; <?= date('Y') ?> BizPulse. All rights reserved.</span>
            <span>Built with PHP 8 &middot; PDO &middot; MySQL 8 &middot; Tailwind CSS</span>
        </div>
    </div>
</footer>
<!-- ═══════════════════════════════════════════════════════════════════════════
     /FOOTER
     ═══════════════════════════════════════════════════════════════════════════ -->

</body>
</html>
