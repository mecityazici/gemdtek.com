<x-mail::message>
@if ($locale === 'en')
# Confirm your subscription

Hello{{ $subscriber->name ? ' '.$subscriber->name : '' }},

Thanks for subscribing to the GEMDTEK newsletter. Click the button below to confirm your email address — you won't receive any updates until you do.

<x-mail::button :url="$confirmUrl" color="primary">
Confirm subscription
</x-mail::button>

If you didn't request this, you can safely ignore this message or [unsubscribe here]({{ $unsubscribeUrl }}).

Best regards,
{{ config('app.name') }}
@else
# Aboneliğinizi onaylayın

Merhaba{{ $subscriber->name ? ' '.$subscriber->name : '' }},

GEMDTEK bültenine abone olduğunuz için teşekkürler. E-posta adresinizi onaylamak için aşağıdaki butona tıklayın — onaylamadan size güncelleme göndermiyoruz.

<x-mail::button :url="$confirmUrl" color="primary">
Aboneliği onayla
</x-mail::button>

Bu talebi siz yapmadıysanız bu mesajı görmezden gelebilir veya [buradan abonelikten çıkabilirsiniz]({{ $unsubscribeUrl }}).

Saygılarımızla,
{{ config('app.name') }}
@endif
</x-mail::message>
