<x-mail::message>
# Etkinlik kaydını onayla

Merhaba {{ $registration->name }},

**{{ $event->getTranslation('title', 'tr') }}** etkinliğine kayıt talebin alındı. Yerini sabitlemek için aşağıdaki butona tıklayarak kaydını onayla.

@if ($event->event_date)
**Tarih:** {{ $event->event_date->format('d M Y H:i') }}
@endif
@if ($event->location)
**Yer:** {{ $event->location }}
@endif

<x-mail::button :url="$confirmUrl" color="primary">
Kaydı onayla
</x-mail::button>

Onaylamadığın sürece yerin sabitlenmez. Talebi yanlışlıkla başlattıysan bu e-postayı görmezden gelebilir veya [buradan iptal edebilirsin]({{ $cancelUrl }}).

Saygılarımızla,
{{ config('app.name') }}
</x-mail::message>
