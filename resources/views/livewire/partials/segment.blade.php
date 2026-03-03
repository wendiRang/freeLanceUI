@php
    use Carbon\Carbon;
    try {
        $dptCarbon = Carbon::parse($seg['dptDateTime']);
        $arrCarbon = Carbon::parse($seg['arrDateTime']);
        $diff      = $dptCarbon->diff($arrCarbon);
        $hours     = ($diff->days * 24) + $diff->h;
        $mins      = $diff->i;
        $duration  = $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
    } catch (\Exception $e) {
        $duration = null;
    }
    $hasStopover = !empty($seg['stopOvers']);
@endphp

<div class="bg-gray-800/40 border border-gray-700/50 rounded-lg px-4 py-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">

    {{-- Flight code --}}
    <span class="font-bold text-white tracking-wide">{{ $seg['flightCode'] ?? '-' }}</span>

    {{-- Route --}}
    <span class="flex items-center gap-2">
        <span class="font-mono font-semibold text-blue-300 tracking-widest">
            {{ $seg['dptAirport'] ?? '?' }}@if($seg['dptTerminal'] ?? false)<span class="text-gray-500 text-xs ml-0.5">T{{ $seg['dptTerminal'] }}</span>@endif
        </span>
        <span class="text-gray-500 text-xs">→</span>
        <span class="font-mono font-semibold text-blue-300 tracking-widest">
            {{ $seg['arrAirport'] ?? '?' }}@if($seg['arrTerminal'] ?? false)<span class="text-gray-500 text-xs ml-0.5">T{{ $seg['arrTerminal'] }}</span>@endif
        </span>
    </span>

    {{-- Times --}}
    <span class="text-xs font-mono text-gray-400">
        {{ $seg['dptDateTime'] ?? '-' }}
        <span class="text-gray-600 mx-1">→</span>
        {{ $seg['arrDateTime'] ?? '-' }}
    </span>

    {{-- Duration --}}
    @if($duration)
    <span class="text-xs bg-gray-700/60 text-gray-300 px-2 py-0.5 rounded-full">
        ⏱ {{ $duration }}
    </span>
    @endif

    {{-- Stopover badge --}}
    @if($hasStopover)
    <span class="text-xs bg-orange-900/40 text-orange-300 border border-orange-700 px-2 py-0.5 rounded-full">
        Stop: {{ implode(', ', $seg['stopOvers']) }}
    </span>
    @endif

    {{-- Carrier --}}
    <span class="text-xs text-gray-500">
        MKT: <span class="text-gray-300">{{ $seg['mktCarrier'] ?? '-' }}</span>
        @if($seg['codeShare'] ?? false)
            <span class="text-yellow-500 ml-1">(OPR: {{ $seg['oprCarrier'] ?? '-' }})</span>
        @endif
    </span>

    {{-- Aircraft --}}
    @if(!empty($seg['aircraft']['iata']) || !empty($seg['aircraft']['name']))
    <span class="text-xs text-gray-500">
        <span class="text-gray-300">{{ trim(($seg['aircraft']['iata'] ?? '') . ' ' . ($seg['aircraft']['name'] ?? '')) }}</span>
    </span>
    @endif

</div>
