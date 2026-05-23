<x-mail::message>
# Yeni Başvuru: {{ $formTitle }}

**Başvuru #{{ $submission->id }}** {{ $submission->created_at->format('d M Y H:i') }} tarihinde alındı.

---

@foreach ($rows as $row)
**{{ $row['label'] }}:** {{ $row['value'] }}

@endforeach

@if ($attachments->isNotEmpty())
---

### Ekli dosyalar

@foreach ($attachments as $att)
- **{{ $att['field'] }}:** {{ $att['name'] }} ({{ $att['size'] }})
@endforeach
@endif

---

<x-mail::button :url="$adminUrl">
Admin panelinde incele
</x-mail::button>

IP: `{{ $submission->ip_address ?? '—' }}`

Saygılarımızla,
{{ config('app.name') }}
</x-mail::message>
