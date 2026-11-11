@php
    $rows = [
        'Регистрационный номер' => $lead->reg_number,
        'Телефон' => $lead->phone,
        'E-mail' => $lead->email,
        'Желаемая цена' => $lead->formattedPrice() ?? 'не указана',
        'Язык общения' => $lead->languageLabel(),
        'Пробег' => $lead->formattedMileage() ?? 'не указан',
    ];
    $photoCount = $lead->photoCount();
@endphp
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Новая заявка — {{ $lead->reg_number }}</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f2f4f6;font-family:Arial,Helvetica,sans-serif;color:#101010;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:24px 28px;background:#141414;color:#ffffff;">
                <div style="font-size:13px;letter-spacing:.06em;text-transform:uppercase;color:#ff4fae;">Autostvabaks</div>
                <div style="font-size:22px;font-weight:bold;margin-top:6px;">Новая заявка на автомобиль</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px 28px 8px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px;line-height:22px;">
                    @foreach ($rows as $label => $value)
                        <tr>
                            <td style="padding:8px 12px 8px 0;color:#737373;width:44%;vertical-align:top;border-bottom:1px solid #eef0f2;">{{ $label }}:</td>
                            <td style="padding:8px 0;font-weight:bold;vertical-align:top;border-bottom:1px solid #eef0f2;">
                                @if ($label === 'Телефон')
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $value) }}" style="color:#101010;text-decoration:none;">{{ $value }}</a>
                                @elseif ($label === 'E-mail')
                                    <a href="mailto:{{ $value }}" style="color:#101010;">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 28px 8px;font-size:15px;line-height:23px;">
                <div style="color:#737373;margin-bottom:6px;">Дополнительная информация:</div>
                <div style="white-space:pre-wrap;">{{ $lead->info ?: '—' }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 28px 8px;font-size:15px;line-height:23px;">
                <div style="color:#737373;margin-bottom:6px;">Фотографии:</div>
                @if ($photoCount === 0)
                    <div>не приложены</div>
                @else
                    <div>
                        {{ $photoCount - $skippedPhotos }} из {{ $photoCount }} — во вложении к письму.
                        @if ($skippedPhotos > 0)
                            Остальные {{ $skippedPhotos }} не поместились в письмо по размеру — они доступны в админ-панели.
                        @endif
                    </div>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:20px 28px 28px;">
                <a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 22px;background:#e6007e;color:#ffffff;font-weight:bold;font-size:14px;text-decoration:none;border-radius:10px;">Открыть заявку в админ-панели</a>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 28px;background:#f7f8f9;color:#8e8e8e;font-size:12px;line-height:18px;">
                Заявка № {{ $lead->id }} · {{ $lead->created_at?->timezone('Europe/Tallinn')->format('d.m.Y H:i') }} · язык сайта: {{ strtoupper($lead->locale) }}<br>
                Чтобы ответить клиенту, просто нажмите «Ответить» — письмо уйдёт на {{ $lead->email }}.
            </td>
        </tr>
    </table>
</body>
</html>
