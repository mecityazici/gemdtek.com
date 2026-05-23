<div
    x-data="{
        show: false,
        init() {
            this.show = !localStorage.getItem('gemdtek_cookie_ack');
        },
        accept() {
            localStorage.setItem('gemdtek_cookie_ack', new Date().toISOString());
            this.show = false;
        }
    }"
    x-show="show"
    x-cloak
    x-transition.opacity
    class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:bottom-6 md:max-w-md bg-navy-900 text-cream rounded-xl shadow-2xl border border-brass-400/30 p-5 z-50"
    role="dialog"
    aria-live="polite"
>
    <p class="text-sm text-cream/90 leading-relaxed mb-4">
        {{ __('pages.cookie.message') }}
    </p>
    <div class="flex flex-wrap gap-2 items-center">
        <button
            @click="accept()"
            class="bg-brass-500 hover:bg-brass-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors"
        >
            {{ __('pages.cookie.accept') }}
        </button>
        <a
            href="{{ route('legal.privacy') }}"
            class="text-sm text-cream/70 hover:text-cream px-3 py-2"
        >
            {{ __('pages.cookie.learn') }} →
        </a>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
