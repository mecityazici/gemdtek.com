<x-mail::message>
# Yeni İletişim Mesajı

Web sitesinden yeni bir iletişim formu mesajı geldi.

**Gönderen:** {{ $name }}
**E-posta:** {{ $email }}
**Konu:** {{ $subject }}
**IP:** {{ $ip }}

---

{{ $body }}

---

<x-mail::button :url="'mailto:' . $email">
Yanıtla
</x-mail::button>

Saygılarımızla,
{{ config('app.name') }}
</x-mail::message>
