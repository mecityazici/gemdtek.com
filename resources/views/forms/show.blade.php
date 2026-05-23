@extends('layouts.app')

@section('title', $form->title . ' — Başvuru — GEMDTEK')

@php
    $hasFileFields = $form->fields->contains(fn ($f) => $f->type === 'file');
    $inputClass = 'block w-full rounded-md border-graphite/20 shadow-sm focus:border-petrol focus:ring-petrol/30 font-sans';
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-16">
        <a href="{{ route('forms.index') }}" class="inline-flex items-center gap-2 text-brass-300 hover:text-brass-200 text-sm mb-6 font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Tüm başvurular
        </a>
        <h1 class="text-3xl md:text-4xl font-display font-bold mb-4">{{ $form->title }}</h1>
        @if ($form->description)
            <p class="text-lg text-cream/80 max-w-3xl">{{ $form->description }}</p>
        @endif
    </div>
</section>

<section class="container-tight py-12 max-w-3xl">

    @if (session('submitted'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-lg mb-10">
            <p class="font-display font-bold text-emerald-900 mb-1">Başvurunuz iletildi.</p>
            <p class="text-emerald-900/80 text-sm">{{ session('successMessage') }}</p>
        </div>
    @endif

    @if (! $isOpen)
        <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-lg">
            <p class="font-display font-bold text-amber-900 mb-1">Başvuru şu anda kapalı.</p>
            <p class="text-amber-900/80 text-sm">
                {{ $form->closed_message ?: 'Bu form şu anda açık değil. Yeni dönem için bizi takipte kal.' }}
            </p>
        </div>
    @elseif ($form->fields->isEmpty())
        <div class="bg-graphite/5 p-6 rounded-lg text-center text-graphite/60 italic">
            Bu form henüz alan içermiyor.
        </div>
    @else
        <form action="{{ route('forms.submit', $form) }}"
              method="POST"
              {{ $hasFileFields ? 'enctype=multipart/form-data' : '' }}
              class="space-y-6 bg-white p-8 rounded-xl shadow-sm border border-graphite/5">
            @csrf

            {{-- honeypot --}}
            <div class="hidden" aria-hidden="true">
                <label>Web site (boş bırak): <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            @foreach ($form->fields as $field)
                <div>
                    <label for="field-{{ $field->name }}" class="block text-sm font-medium text-graphite mb-1">
                        {{ $field->label }}
                        @if ($field->is_required)
                            <span class="text-brass-600">*</span>
                        @endif
                    </label>

                    @php
                        $old   = old($field->name);
                        $oldArr = is_array($old) ? $old : [];
                        $err = $errors->first($field->name);
                    @endphp

                    @switch($field->type)
                        @case('textarea')
                            <textarea
                                id="field-{{ $field->name }}"
                                name="{{ $field->name }}"
                                rows="4"
                                placeholder="{{ $field->placeholder }}"
                                @if ($field->is_required) required @endif
                                class="{{ $inputClass }}">{{ $old }}</textarea>
                            @break

                        @case('select')
                            <select id="field-{{ $field->name }}"
                                    name="{{ $field->name }}"
                                    @if ($field->is_required) required @endif
                                    class="{{ $inputClass }}">
                                <option value="">— seçin —</option>
                                @foreach ($field->options ?? [] as $opt)
                                    <option value="{{ $opt }}" @selected($old === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('radio')
                            <div class="space-y-2 mt-1">
                                @foreach ($field->options ?? [] as $opt)
                                    <label class="flex items-center gap-2">
                                        <input type="radio"
                                               name="{{ $field->name }}"
                                               value="{{ $opt }}"
                                               @checked($old === $opt)
                                               @if ($field->is_required) required @endif
                                               class="text-petrol focus:ring-petrol/30">
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('checkbox')
                            <div class="space-y-2 mt-1">
                                @foreach ($field->options ?? [] as $opt)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox"
                                               name="{{ $field->name }}[]"
                                               value="{{ $opt }}"
                                               @checked(in_array($opt, $oldArr))
                                               class="rounded text-petrol focus:ring-petrol/30">
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('file')
                            <input type="file"
                                   id="field-{{ $field->name }}"
                                   name="{{ $field->name }}"
                                   @if ($field->is_required) required @endif
                                   class="block w-full text-sm text-graphite file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-navy-800 file:text-white hover:file:bg-navy-700 file:cursor-pointer">
                            @break

                        @default
                            <input type="{{ $field->type === 'tel' ? 'tel' : ($field->type === 'email' ? 'email' : ($field->type === 'url' ? 'url' : ($field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : 'text')))) }}"
                                   id="field-{{ $field->name }}"
                                   name="{{ $field->name }}"
                                   value="{{ $old }}"
                                   placeholder="{{ $field->placeholder }}"
                                   @if ($field->is_required) required @endif
                                   class="{{ $inputClass }}">
                    @endswitch

                    @if ($field->help_text)
                        <p class="mt-1 text-xs text-graphite/60">{{ $field->help_text }}</p>
                    @endif

                    @if ($err)
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $err }}</p>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="btn-accent w-full md:w-auto">
                Başvuruyu gönder
            </button>

            <p class="text-xs text-graphite/50">
                Kişisel verilerin GEMDTEK Aydınlatma Metni çerçevesinde, yalnızca bu başvurunun değerlendirilmesi amacıyla işlenir.
            </p>
        </form>
    @endif
</section>

@endsection
