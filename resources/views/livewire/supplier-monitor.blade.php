@php use Carbon\Carbon; @endphp

<div class="max-w-screen-2xl mx-auto space-y-5">

    {{-- ==================== PROXY BAR ==================== --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl px-5 py-3 flex items-center gap-4">
        <span class="text-xs text-gray-500 whitespace-nowrap">Proxy <span class="text-gray-600">(optional)</span></span>
        <input wire:model="proxy" type="text" placeholder="http://user:pass@host:port"
            class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-gray-300 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <span class="text-xs text-gray-600 whitespace-nowrap">Applied to all runs</span>
    </div>

    {{-- ==================== PAYLOAD LIST ==================== --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-3 bg-gray-800/50 border-b border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-widest">Payloads</h2>
                <span class="text-xs text-gray-500">{{ count($payloads) }} item(s) · payload.json</span>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="runAll" wire:loading.attr="disabled" wire:target="runAll,run"
                    class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors
                        bg-green-700 hover:bg-green-600 disabled:bg-gray-700 disabled:text-gray-500 text-white">
                    <span wire:loading.remove wire:target="runAll">▶ Run All</span>
                    <span wire:loading wire:target="runAll" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Running All...
                    </span>
                </button>

                <button wire:click="clearAll" wire:confirm="Clear all saved results?"
                    wire:loading.attr="disabled" wire:target="runAll,run"
                    class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors
                        bg-gray-700 hover:bg-red-800 text-gray-300 hover:text-white">
                    Clear All
                </button>
            </div>
        </div>

        @if(empty($payloads))
        <div class="p-10 text-center text-gray-600 text-sm">
            No payloads found. Check <code class="text-gray-500">config/payload.json</code>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-800/60 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="text-left px-4 py-3 w-8">#</th>
                        <th class="text-left px-4 py-3">Class</th>
                        <th class="text-left px-4 py-3">Route</th>
                        <th class="text-left px-4 py-3">Depart</th>
                        <th class="text-left px-4 py-3">Return</th>
                        <th class="text-center px-4 py-3">ADT</th>
                        <th class="text-center px-4 py-3">CHD</th>
                        <th class="text-center px-4 py-3">INF</th>
                        <th class="text-center px-4 py-3">Type</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Last Run</th>
                        <th class="text-center px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payloads as $i => $p)
                    @php
                        $row         = $rowResults[$i] ?? null;
                        $isSelected  = $detailIndex === $i;
                        $isRoundTrip = !empty($p['rtnDate']);
                        $status      = $row['status'] ?? null;
                    @endphp
                    <tr class="border-t border-gray-800 transition-colors
                        {{ $isSelected ? 'bg-blue-900/20' : 'hover:bg-gray-800/20' }}">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-semibold text-white">{{ $p['Class'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-mono font-semibold text-blue-300 tracking-widest">{{ strtoupper($p['origin'] ?? '?') }}</span>
                            <span class="text-gray-600 mx-1">→</span>
                            <span class="font-mono font-semibold text-blue-300 tracking-widest">{{ strtoupper($p['dest'] ?? '?') }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-300 font-mono text-xs">{{ $p['dptDate'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $p['rtnDate'] ?: '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-300">{{ $p['adult'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-center text-gray-400">{{ $p['child'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-center text-gray-400">{{ $p['infant'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($isRoundTrip)
                                <span class="text-xs bg-purple-900/50 text-purple-300 border border-purple-700 px-2 py-0.5 rounded-full">RT</span>
                            @else
                                <span class="text-xs bg-blue-900/50 text-blue-300 border border-blue-700 px-2 py-0.5 rounded-full">OW</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @if($status === 'success')
                                <button wire:click="viewDetail({{ $i }})"
                                    class="text-xs bg-green-900/40 text-green-400 border border-green-700 px-2 py-0.5 rounded-full hover:bg-green-800/50 transition-colors whitespace-nowrap">
                                    ✓ {{ $row['count'] }} opts · {{ number_format($row['responseTime']) }}ms
                                </button>
                            @elseif($status === 'error')
                                <button wire:click="viewDetail({{ $i }})"
                                    class="text-xs bg-red-900/40 text-red-400 border border-red-800 px-2 py-0.5 rounded-full hover:bg-red-800/50 transition-colors max-w-[180px] truncate block"
                                    title="{{ $row['errorMsg'] ?? '' }}">
                                    ✗ {{ Str::limit($row['errorMsg'] ?? 'Error', 22) }}
                                </button>
                            @else
                                <span class="text-xs text-gray-600">—</span>
                            @endif
                        </td>

                        {{-- Last run --}}
                        <td class="px-4 py-3 text-xs text-gray-600 font-mono">
                            {{ $row['updatedAt'] ?? '—' }}
                        </td>

                        {{-- Action: Show Result + Run --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">

                                {{-- Show Result button (only when result exists) --}}
                                @if($row)
                                <button wire:click="viewDetail({{ $i }})"
                                    class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors
                                        {{ $isSelected ? 'bg-gray-600 text-white' : 'bg-gray-700 hover:bg-gray-600 text-gray-200' }}">
                                    {{ $isSelected ? 'Shown' : 'Show Result' }}
                                </button>
                                @endif

                                {{-- Run / Re-run button --}}
                                <button wire:click="run({{ $i }})" wire:loading.attr="disabled" wire:target="run({{ $i }}),runAll"
                                    class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors
                                        bg-blue-700 hover:bg-blue-600 disabled:bg-gray-700 disabled:text-gray-500 text-white">
                                    <span wire:loading.remove wire:target="run({{ $i }})">{{ $row ? 'Re-run' : 'Run' }}</span>
                                    <span wire:loading wire:target="run({{ $i }})" class="flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        ...
                                    </span>
                                </button>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ==================== DETAIL PANEL ==================== --}}
    @if($detailIndex >= 0)
    @php $runPayload = $rowResults[$detailIndex]['payload'] ?? $payloads[$detailIndex] ?? null; @endphp
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">

        {{-- Panel header --}}
        <div class="px-5 py-3 bg-gray-800/50 border-b border-gray-800 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-sm font-semibold text-gray-300">
                    Result — #{{ $detailIndex + 1 }}
                    <span class="text-gray-500 font-normal">{{ $runPayload['Class'] ?? '' }}</span>
                    <span class="text-blue-300 font-mono ml-1">
                        {{ strtoupper($runPayload['origin'] ?? '') }} → {{ strtoupper($runPayload['dest'] ?? '') }}
                    </span>
                    <span class="text-gray-500 font-mono text-xs ml-1">{{ $runPayload['dptDate'] ?? '' }}</span>
                </span>

                @if($detailError)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-900/40 text-red-400 border border-red-800">
                        ✗ {{ $detailError['msg'] }}
                    </span>
                @elseif($detailResults !== null)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-900/40 text-green-400 border border-green-800">
                        ✓ {{ count($detailResults) }} option(s)
                    </span>
                @endif

                @if(isset($rowResults[$detailIndex]['responseTime']))
                    <span class="text-xs text-gray-500">{{ number_format($rowResults[$detailIndex]['responseTime']) }} ms</span>
                @endif
                @if(isset($rowResults[$detailIndex]['updatedAt']))
                    <span class="text-xs text-gray-600 font-mono">{{ $rowResults[$detailIndex]['updatedAt'] }}</span>
                @endif
            </div>

            @if($detailRaw)
            <button wire:click="toggleRaw"
                class="text-xs text-gray-400 hover:text-white border border-gray-700 rounded px-2 py-1 transition-colors">
                {{ $showRaw ? 'Hide' : 'Show' }} Raw JSON
            </button>
            @endif
        </div>

        {{-- Raw JSON --}}
        @if($showRaw && $detailRaw)
        <div class="border-b border-gray-800">
            <pre class="text-xs text-green-300 font-mono p-4 overflow-x-auto max-h-64 bg-gray-950/50 scrollbar-thin">{{ $detailRaw }}</pre>
        </div>
        @endif

        {{-- Flight results --}}
        @if($detailResults !== null)
        <div class="p-5 space-y-4">
            @forelse($detailResults as $idx => $flight)
            @php
                $dptSegs  = $flight['dptSegments'] ?? [];
                $rtnSegs  = $flight['rtnSegments'] ?? [];
                $isDirect = count($dptSegs) === 1 && empty($dptSegs[0]['stopOvers'] ?? []);

                // Total journey duration (depart leg)
                $journeyDuration = null;
                if (!empty($dptSegs)) {
                    try {
                        $firstDpt = Carbon::parse($dptSegs[0]['dptDateTime']);
                        $lastArr  = Carbon::parse(end($dptSegs)['arrDateTime']);
                        $diff     = $firstDpt->diff($lastArr);
                        $h        = ($diff->days * 24) + $diff->h;
                        $journeyDuration = $h > 0 ? "{$h}h {$diff->i}m" : "{$diff->i}m";
                    } catch (\Exception $e) {}
                }

                // Currency from first fare
                $currency = $flight['fares'][0]['currency'] ?? null;
            @endphp

            <div class="border border-gray-700/50 rounded-xl overflow-hidden">
                {{-- Option header --}}
                <div class="bg-gray-800/40 px-4 py-2 flex items-center justify-between border-b border-gray-700/50 flex-wrap gap-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-gray-400">Option #{{ $idx + 1 }}</span>

                        {{-- Direct / Transit --}}
                        @if($isDirect)
                            <span class="text-xs bg-green-900/40 text-green-400 border border-green-700 px-2 py-0.5 rounded-full">Direct</span>
                        @else
                            <span class="text-xs bg-orange-900/40 text-orange-300 border border-orange-700 px-2 py-0.5 rounded-full">
                                Transit · {{ count($dptSegs) - 1 }} stop{{ count($dptSegs) - 1 > 1 ? 's' : '' }}
                            </span>
                        @endif

                        {{-- Total duration --}}
                        @if($journeyDuration)
                            <span class="text-xs text-gray-400">⏱ {{ $journeyDuration }}</span>
                        @endif

                        {{-- Currency --}}
                        @if($currency)
                            <span class="text-xs font-mono font-semibold bg-yellow-900/30 text-yellow-300 border border-yellow-700/50 px-2 py-0.5 rounded">
                                {{ $currency }}
                            </span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-600">{{ count($flight['fares'] ?? []) }} fare(s)</span>
                </div>

                <div class="p-4 space-y-4">

                    {{-- Departure Segments --}}
                    @if(!empty($dptSegs))
                    <div>
                        <div class="text-xs font-semibold text-blue-400 uppercase tracking-widest mb-2">Departure</div>
                        <div class="space-y-2">
                            @foreach($dptSegs as $seg)
                                @include('livewire.partials.segment', ['seg' => $seg])
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Return Segments --}}
                    @if(!empty($rtnSegs))
                    @php
                        $isDirectRtn = count($rtnSegs) === 1 && empty($rtnSegs[0]['stopOvers'] ?? []);
                        $rtnDuration = null;
                        try {
                            $rFirstDpt = Carbon::parse($rtnSegs[0]['dptDateTime']);
                            $rLastArr  = Carbon::parse(end($rtnSegs)['arrDateTime']);
                            $rDiff     = $rFirstDpt->diff($rLastArr);
                            $rH        = ($rDiff->days * 24) + $rDiff->h;
                            $rtnDuration = $rH > 0 ? "{$rH}h {$rDiff->i}m" : "{$rDiff->i}m";
                        } catch (\Exception $e) {}
                    @endphp
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="text-xs font-semibold text-purple-400 uppercase tracking-widest">Return</div>
                            @if($isDirectRtn)
                                <span class="text-xs bg-green-900/40 text-green-400 border border-green-700 px-2 py-0.5 rounded-full">Direct</span>
                            @else
                                <span class="text-xs bg-orange-900/40 text-orange-300 border border-orange-700 px-2 py-0.5 rounded-full">
                                    Transit · {{ count($rtnSegs) - 1 }} stop{{ count($rtnSegs) - 1 > 1 ? 's' : '' }}
                                </span>
                            @endif
                            @if($rtnDuration)
                                <span class="text-xs text-gray-400">⏱ {{ $rtnDuration }}</span>
                            @endif
                        </div>
                        <div class="space-y-2">
                            @foreach($rtnSegs as $seg)
                                @include('livewire.partials.segment', ['seg' => $seg])
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Fares --}}
                    @if(!empty($flight['fares']))
                    <div class="space-y-3">
                        @foreach($flight['fares'] as $fi => $fare)
                        @php
                            $adt = (int)($runPayload['adult']  ?? 1);
                            $chd = (int)($runPayload['child']  ?? 0);
                            $inf = (int)($runPayload['infant'] ?? 0);

                            $fareCurrency = $fare['currency'] ?? '-';
                            $meal    = collect($fare['benefit']['meal']    ?? [])->flatten()->filter(fn($v) => $v && $v !== '-')->first() ?? null;
                            $baggage = $fare['adult']['baggage'] ?? null;
                            $seat    = collect($fare['benefit']['seatSelection'] ?? [])->flatten()->filter(fn($v) => $v && $v !== '-')->first() ?? null;

                            // Prices per pax type
                            $adtBase  = $fare['adult']['price']['base']  ?? 0;
                            $adtTax   = $fare['adult']['price']['tax']   ?? 0;
                            $adtOther = $fare['adult']['price']['other'] ?? 0;
                            $adtSub   = $adtBase + $adtTax + $adtOther;

                            $chdBase  = $fare['child']['price']['base']  ?? null;
                            $chdTax   = $fare['child']['price']['tax']   ?? null;
                            $chdOther = $fare['child']['price']['other'] ?? null;
                            $chdSub   = isset($fare['child']) ? ($chdBase + $chdTax + $chdOther) : null;

                            $infBase  = $fare['infant']['price']['base']  ?? null;
                            $infTax   = $fare['infant']['price']['tax']   ?? null;
                            $infOther = $fare['infant']['price']['other'] ?? null;
                            $infSub   = isset($fare['infant']) ? ($infBase + $infTax + $infOther) : null;

                            $grandTotal = ($adtSub * $adt)
                                        + ($chdSub !== null ? $chdSub * $chd : 0)
                                        + ($infSub !== null ? $infSub * $inf : 0);

                            // Per-leg fare info (index 0 = depart, index 1 = return)
                            $dptCabins   = $fare['cabinClass'][0]   ?? [];
                            $dptBasis    = $fare['fareBasis'][0]    ?? [];
                            $dptClass    = $fare['fareClass'][0]    ?? [];
                            $dptBooking  = $fare['bookingClass'][0] ?? [];
                            $rtnCabins   = $fare['cabinClass'][1]   ?? [];
                            $rtnBasis    = $fare['fareBasis'][1]    ?? [];
                            $rtnClass    = $fare['fareClass'][1]    ?? [];
                            $rtnBooking  = $fare['bookingClass'][1] ?? [];
                        @endphp

                        <div class="border border-gray-700/40 rounded-lg overflow-hidden text-xs">

                            {{-- Fare header --}}
                            <div class="bg-gray-800/60 px-4 py-2 flex items-center justify-between border-b border-gray-700/40">
                                <span class="text-gray-400 font-semibold">Fare #{{ $fi + 1 }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-semibold text-yellow-300">{{ $fareCurrency }}</span>
                                    <span class="{{ $meal ? 'text-green-400' : 'text-gray-600' }}">Meal: {{ $meal ? '✓' : '✗' }}</span>
                                    <span class="text-gray-400">Baggage: {{ is_null($baggage) ? '-' : $baggage }}</span>
                                    <span class="{{ $seat ? 'text-green-400' : 'text-gray-600' }}">Seat: {{ $seat ? '✓' : '✗' }}</span>
                                </div>
                            </div>

                            {{-- Per-segment fare info (cabin / basis / class per leg) --}}
                            <div class="px-4 py-3 border-b border-gray-700/40 space-y-2">

                                {{-- Departure segments --}}
                                @if(!empty($dptSegs))
                                <div>
                                    <span class="text-blue-400 font-semibold uppercase tracking-wider text-xs">Departure</span>
                                    <div class="mt-1 space-y-1">
                                        @foreach($dptSegs as $si => $seg)
                                        <div class="flex items-center gap-4 text-gray-300">
                                            <span class="font-mono text-blue-200 font-semibold w-16">{{ $seg['dptAirport'] ?? '?' }}→{{ $seg['arrAirport'] ?? '?' }}</span>
                                            <span class="text-gray-500">Cabin: <span class="text-white capitalize">{{ $dptCabins[$si] ?? '-' }}</span></span>
                                            <span class="text-gray-500">Fare Class: <span class="text-white font-mono">{{ $dptClass[$si] ?? '-' }}</span></span>
                                            <span class="text-gray-500">Fare Basis: <span class="text-white font-mono">{{ $dptBasis[$si] ?? '-' }}</span></span>
                                            <span class="text-gray-500">RBD: <span class="text-white font-mono">{{ $dptBooking[$si] ?? '-' }}</span></span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Return segments --}}
                                @if(!empty($rtnSegs))
                                <div>
                                    <span class="text-purple-400 font-semibold uppercase tracking-wider text-xs">Return</span>
                                    <div class="mt-1 space-y-1">
                                        @foreach($rtnSegs as $si => $seg)
                                        <div class="flex items-center gap-4 text-gray-300">
                                            <span class="font-mono text-purple-200 font-semibold w-16">{{ $seg['dptAirport'] ?? '?' }}→{{ $seg['arrAirport'] ?? '?' }}</span>
                                            <span class="text-gray-500">Cabin: <span class="text-white capitalize">{{ $rtnCabins[$si] ?? '-' }}</span></span>
                                            <span class="text-gray-500">Fare Class: <span class="text-white font-mono">{{ $rtnClass[$si] ?? '-' }}</span></span>
                                            <span class="text-gray-500">Fare Basis: <span class="text-white font-mono">{{ $rtnBasis[$si] ?? '-' }}</span></span>
                                            <span class="text-gray-500">RBD: <span class="text-white font-mono">{{ $rtnBooking[$si] ?? '-' }}</span></span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Pax breakdown table --}}
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-800/40 text-gray-500 uppercase tracking-wider">
                                        <th class="text-left px-4 py-2">Pax</th>
                                        <th class="text-right px-4 py-2">Base</th>
                                        <th class="text-right px-4 py-2">Tax</th>
                                        <th class="text-right px-4 py-2">Other</th>
                                        <th class="text-right px-4 py-2">Subtotal / pax</th>
                                        <th class="text-right px-4 py-2">× Count</th>
                                        <th class="text-right px-4 py-2 text-gray-400">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Adult --}}
                                    <tr class="border-t border-gray-800/60">
                                        <td class="px-4 py-2 text-white font-semibold">Adult</td>
                                        <td class="px-4 py-2 text-right text-gray-300">{{ number_format($adtBase) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-400">{{ number_format($adtTax) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-400">{{ number_format($adtOther) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-200 font-semibold">{{ number_format($adtSub) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-500">× {{ $adt }}</td>
                                        <td class="px-4 py-2 text-right font-bold text-green-400">{{ number_format($adtSub * $adt) }}</td>
                                    </tr>

                                    {{-- Child --}}
                                    @if($chd > 0 && $chdSub !== null)
                                    <tr class="border-t border-gray-800/60">
                                        <td class="px-4 py-2 text-blue-300 font-semibold">Child</td>
                                        <td class="px-4 py-2 text-right text-gray-300">{{ number_format($chdBase) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-400">{{ number_format($chdTax) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-400">{{ number_format($chdOther) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-200 font-semibold">{{ number_format($chdSub) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-500">× {{ $chd }}</td>
                                        <td class="px-4 py-2 text-right font-bold text-blue-400">{{ number_format($chdSub * $chd) }}</td>
                                    </tr>
                                    @endif

                                    {{-- Infant --}}
                                    @if($inf > 0 && $infSub !== null)
                                    <tr class="border-t border-gray-800/60">
                                        <td class="px-4 py-2 text-purple-300 font-semibold">Infant</td>
                                        <td class="px-4 py-2 text-right text-gray-300">{{ number_format($infBase) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-400">{{ number_format($infTax) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-400">{{ number_format($infOther) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-200 font-semibold">{{ number_format($infSub) }}</td>
                                        <td class="px-4 py-2 text-right text-gray-500">× {{ $inf }}</td>
                                        <td class="px-4 py-2 text-right font-bold text-purple-400">{{ number_format($infSub * $inf) }}</td>
                                    </tr>
                                    @endif

                                    {{-- Grand Total --}}
                                    <tr class="border-t-2 border-gray-600 bg-gray-800/30">
                                        <td colspan="6" class="px-4 py-2 text-right text-gray-400 font-semibold uppercase tracking-wider text-xs">
                                            Grand Total ({{ $adt }} ADT{{ $chd > 0 ? ", {$chd} CHD" : '' }}{{ $inf > 0 ? ", {$inf} INF" : '' }})
                                        </td>
                                        <td class="px-4 py-2 text-right font-bold text-yellow-300 text-sm">
                                            {{ $fareCurrency }} {{ number_format($grandTotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-6">No results returned from supplier.</div>
            @endforelse
        </div>
        @endif

    </div>
    @endif

</div>
