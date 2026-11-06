@php
    /** @var \App\Models\Lead $record */
    $record = $getRecord();
    $photos = $record->photos ?? [];
@endphp

@if (count($photos) === 0)
    <p class="text-sm text-gray-500 dark:text-gray-400">Клиент не приложил фотографии.</p>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:12px;">
        @foreach ($photos as $index => $path)
            @php $url = route('admin.lead-photo', ['lead' => $record->id, 'index' => $index]); @endphp
            <a href="{{ $url }}" target="_blank" rel="noopener"
               style="display:block;aspect-ratio:4/3;border-radius:12px;overflow:hidden;background:#f3f4f6;"
               title="Открыть фото {{ $index + 1 }} в полном размере">
                <img src="{{ $url }}" alt="Фото {{ $index + 1 }} — {{ $record->reg_number }}" loading="lazy"
                     style="width:100%;height:100%;object-fit:cover;">
            </a>
        @endforeach
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400" style="margin-top:10px;">
        Всего фото: {{ count($photos) }}. Нажмите на фото, чтобы открыть его в полном размере.
    </p>
@endif
