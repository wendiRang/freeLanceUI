<div class="bg-gray-800/40 border border-gray-700/50 rounded-lg px-4 py-3 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">

    {{-- Flight Code --}}
    <span class="font-bold text-white tracking-wide">{{ $seg['flightCode'] ?? '-' }}</span>

    {{-- Route --}}
    <span class="flex items-center gap-2 text-gray-300">
        <span class="font-semibold text-blue-300">{{ $seg['dptAirport'] ?? '?' }}</span>
        @if($seg['dptTerminal'] ?? false)
            <span class="text-xs text-gray-500">T{{ $seg['dptTerminal'] }}</span>
        @endif
        <span class="text-gray-600">→</span>
        <span class="font-semibold text-blue-300">{{ $seg['arrAirport'] ?? '?' }}</span>
        @if($seg['arrTerminal'] ?? false)
            <span class="text-xs text-gray-500">T{{ $seg['arrTerminal'] }}</span>
        @endif
    </span>

    {{-- Times --}}
    <span class="text-gray-400 text-xs font-mono">
        {{ $seg['dptDateTime'] ?? '-' }}
        <span class="text-gray-600 mx-1">→</span>
        {{ $seg['arrDateTime'] ?? '-' }}
    </span>

    {{-- Carriers --}}
    <span class="text-xs text-gray-500">
        MKT: <span class="text-gray-300">{{ $seg['mktCarrier'] ?? '-' }}</span>
        @if(($seg['codeShare'] ?? false))
            <span class="text-yellow-500 ml-1">(Codeshare: {{ $seg['oprCarrier'] ?? '-' }})</span>
        @endif
    </span>

    {{-- Aircraft --}}
    @if(!empty($seg['aircraft']['iata']) || !empty($seg['aircraft']['name']))
    <span class="text-xs text-gray-500">
        Aircraft: <span class="text-gray-300">{{ $seg['aircraft']['iata'] ?? '' }} {{ $seg['aircraft']['name'] ?? '' }}</span>
    </span>
    @endif

    {{-- Stopovers --}}
    @if(!empty($seg['stopOvers']))
    <span class="text-xs text-orange-400">
        Stop: {{ implode(', ', $seg['stopOvers']) }}
    </span>
    @endif

</div>
