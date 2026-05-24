<button
    x-data="{ visible: false }"
    x-init="window.addEventListener('scroll', () => visible = window.scrollY > 400)"
    x-show="visible"
    x-cloak
    x-transition.opacity
    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    aria-label="{{ app()->getLocale() === 'en' ? 'Back to top' : 'Yukarı dön' }}"
    class="fixed bottom-6 left-6 z-40 w-11 h-11 rounded-full bg-navy-800 text-cream hover:bg-navy-700 shadow-lg flex items-center justify-center transition-all"
>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
    </svg>
</button>
