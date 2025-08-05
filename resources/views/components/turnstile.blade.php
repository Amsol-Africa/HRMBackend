<div id="cf-turnstile" class="turnstile-wrapper"></div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
window.onload = function() {
    turnstile.render('#cf-turnstile', {
        sitekey: "{{ env('CF_TURNSTILE_SITE_KEY') }}",
        theme: 'light'
    });
};
</script>