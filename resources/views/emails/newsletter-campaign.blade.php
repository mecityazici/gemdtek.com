<x-mail::message>
{!! \Illuminate\Support\Str::markdown($body) !!}

---

@if ($subscriber->locale === 'en')
You're receiving this email because you subscribed to GEMDTEK updates. If you'd rather not receive future campaigns, [unsubscribe here]({{ $unsubscribeUrl }}).
@else
Bu e-postayı GEMDTEK güncellemelerine abone olduğunuz için aldınız. İleride kampanyalarımızı almak istemezseniz [buradan abonelikten çıkabilirsiniz]({{ $unsubscribeUrl }}).
@endif
</x-mail::message>
