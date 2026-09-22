@php use Laravel\Nova\Nova; @endphp
<p class="mt-8 text-center text-xs text-80">
    <a href="https://cms.amuz.co.kr" class="text-primary dim no-underline" target="_blank">AMUZ CMS2</a>
    <span class="px-1">&middot;</span>
    &copy; {{ date("Y") }} AmuzCORP - By <a href="mailto:ceo@amuz.co.kr" target="_blank">XISO</a>.
    <span class="px-1">&middot;</span>
    v{{ Nova::version() }}
</p>
