<x-mail::message>
# Kaydın alındı

Merhaba {{ $registration->name }},

**{{ $event->getTranslation('title', 'tr') }}** etkinliğine kaydın başarıyla onaylandı.

@if ($event->event_date)
**Tarih:** {{ $event->event_date->format('d M Y H:i') }}
@endif
@if ($event->location)
**Yer:** {{ $event->location }}
@endif

Bu e-postanın ekinde takvimine eklemen için `event.ics` dosyası bulunuyor.

@if ($registration->event)
<x-mail::button :url="route('events.show', $registration->event)" color="primary">
Etkinlik sayfasına git
</x-mail::button>
@endif

Plan değişirse [buradan kaydını iptal edebilirsin]({{ $cancelUrl }}).

Görüşmek üzere,
{{ config('app.name') }}
</x-mail::message>
