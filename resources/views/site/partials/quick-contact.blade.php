@php
    use App\Support\Site;

    // Floating quick-contact buttons. Each one appears only when its number / username
    // is filled in under admin panel → Настройки.
    $whatsapp = Site::whatsappUrl();
    $telegram = Site::telegramUrl();
@endphp

@if ($whatsapp || $telegram)
    <div class="asv-quick" aria-label="{{ __('site.contact.messengers') }}">
        @if ($whatsapp)
            <a class="asv-quick__btn asv-quick__btn--whatsapp" href="{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
                <x-asv-icon name="whatsapp" size="26" />
            </a>
        @endif
        @if ($telegram)
            <a class="asv-quick__btn asv-quick__btn--telegram" href="{{ $telegram }}" target="_blank" rel="noopener" aria-label="Telegram" title="Telegram">
                <x-asv-icon name="telegram" size="26" />
            </a>
        @endif
    </div>
@endif
