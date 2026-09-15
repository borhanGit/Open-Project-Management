@props(['color' => '#64748b', 'text' => ''])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide']) }}
      style="background-color: {{ $color }}15; color: {{ $color }}; border: 1px solid {{ $color }}30;">
    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $color }};"></span>
    {{ $text ?: $slot }}
</span>
