<x-mail::message>
# Yeni Sponsor Lead: {{ $lead->company_name }}

Sponsorluk sayfasından yeni bir kurumsal ilgi geldi.

**Şirket:** {{ $lead->company_name }}
**İletişim:** {{ $lead->contact_name }}@if ($lead->contact_role) — {{ $lead->contact_role }}@endif
**E-posta:** {{ $lead->contact_email }}
**İlgilenilen seviye:** {{ $tierLabel }}
**Gönderim zamanı:** {{ $lead->created_at->format('d M Y H:i') }}
**IP:** {{ $lead->ip_address ?? '—' }}

@if ($lead->message)
---

### Mesaj

{{ $lead->message }}
@endif

---

<x-mail::button :url="'mailto:' . $lead->contact_email">
Yanıtla
</x-mail::button>

Saygılarımızla,
{{ config('app.name') }}
</x-mail::message>
