Новая заявка на автомобиль

Регистрационный номер: {!! $lead->reg_number !!}
Телефон: {!! $lead->phone !!}
E-mail: {!! $lead->email !!}
Желаемая цена: {!! $lead->formattedPrice() ?? 'не указана' !!}
Язык общения: {!! $lead->languageLabel() !!}
Пробег: {!! $lead->formattedMileage() ?? 'не указан' !!}

Дополнительная информация:
{!! $lead->info ?: '—' !!}

Фотографии:
@if ($lead->photoCount() === 0)
не приложены
@else
{{ $lead->photoCount() - $skippedPhotos }} из {{ $lead->photoCount() }} — во вложении к письму.
@if ($skippedPhotos > 0)
Остальные {{ $skippedPhotos }} не поместились в письмо по размеру — они доступны в админ-панели.
@endif
@endif

Открыть заявку: {!! $adminUrl !!}

Заявка № {{ $lead->id }} · {{ $lead->created_at?->timezone('Europe/Tallinn')->format('d.m.Y H:i') }} · язык сайта: {{ strtoupper($lead->locale) }}
