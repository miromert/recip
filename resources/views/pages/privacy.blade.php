<x-app-layout>
    <x-slot name="title">Privacy Policy</x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8 prose prose-gray">
        <h1>Privacy Policy</h1>

        <p><strong>Recip takes your privacy seriously.</strong></p>

        <h2>What we collect</h2>
        <ul>
            <li><strong>Account data:</strong> When you register, we store your name, username, email, and password (hashed). That's it.</li>
            <li><strong>Recipes:</strong> Anything you submit is stored and publicly visible when published.</li>
            <li><strong>Session cookie:</strong> A strictly functional cookie is used to keep you logged in. It contains no tracking data.</li>
        </ul>

        <h2>Email verification</h2>
        <p>We require email verification when you register. A single transactional email is sent to confirm your address. We do not send marketing emails or newsletters.</p>

        <h2>Bot protection</h2>
        <p>We use <a href="https://www.cloudflare.com/products/turnstile/" target="_blank" rel="noopener">Cloudflare Turnstile</a> on our registration form to prevent automated abuse. Turnstile is a privacy-preserving CAPTCHA alternative that does <strong>not</strong> use cookies or track you across the web. It runs a brief, non-interactive challenge in your browser to verify you're human.</p>

        <h2>What we don't do</h2>
        <ul>
            <li>We do <strong>not</strong> use analytics tracking (no Google Analytics, no Facebook Pixel, nothing).</li>
            <li>We do <strong>not</strong> set tracking cookies.</li>
            <li>We do <strong>not</strong> sell or share your data with anyone.</li>
            <li>We do <strong>not</strong> use targeted advertising.</li>
        </ul>

        <h2>Ads</h2>
        <p>We may display minimal, contextual ads to cover hosting costs. These ads are served by privacy-respecting ad networks that do <strong>not</strong> use cookies or track you across the web.</p>

        <h2>Your unit preference</h2>
        <p>Your metric/imperial preference is stored in your browser's <code>localStorage</code> — it never leaves your device.</p>

        <h2>Data deletion</h2>
        <p>You can delete your account at any time from your profile settings. This will permanently remove your account data and all your recipes.</p>

        <h2>Contact</h2>
        <p>Questions? Open an issue on our <a href="https://github.com/miromert/recip" target="_blank" rel="noopener">GitHub repository</a>.</p>
    </div>
</x-app-layout>
