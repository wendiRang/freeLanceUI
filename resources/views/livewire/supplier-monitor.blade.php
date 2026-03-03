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
                    <div class="overflow-x-auto scrollbar-thin">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-gray-400">
                                    <th class="text-left px-3 py-2">Cabin</th>
                                    <th class="text-left px-3 py-2">Fare Basis</th>
                                    <th class="text-left px-3 py-2">Currency</th>
                                    <th class="text-right px-3 py-2">Base</th>
                                    <th class="text-right px-3 py-2">Tax</th>
                                    <th class="text-right px-3 py-2">Other</th>
                                    <th class="text-right px-3 py-2">Total (ADT)</th>
                                    @if(($runPayload['child'] ?? 0) > 0)
                                    <th class="text-right px-3 py-2">Total (CHD)</th>
                                    @endif
                                    @if(($runPayload['infant'] ?? 0) > 0)
                                    <th class="text-right px-3 py-2">Total (INF)</th>
                                    @endif
                                    <th class="text-center px-3 py-2">Meal</th>
                                    <th class="text-center px-3 py-2">Baggage</th>
                                    <th class="text-center px-3 py-2">Seat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flight['fares'] as $fare)
                                @php
                                    $cabin      = collect($fare['cabinClass'] ?? [])->map(fn($v) => is_array($v) ? implode('+', $v) : $v)->implode(' / ');
                                    $fareBasis  = collect($fare['fareBasis'] ?? [])->map(fn($v) => is_array($v) ? implode('+', $v) : $v)->implode(' / ');
                                    $base       = $fare['adult']['price']['base']  ?? 0;
                                    $tax        = $fare['adult']['price']['tax']   ?? 0;
                                    $other      = $fare['adult']['price']['other'] ?? 0;
                                    $total      = $base + $tax + $other;
                                    $childTotal  = isset($fare['child'])  ? array_sum($fare['child']['price']  ?? []) : null;
                                    $infantTotal = isset($fare['infant']) ? array_sum($fare['infant']['price'] ?? []) : null;
                                    $meal    = collect($fare['benefit']['meal']    ?? [])->flatten()->filter(fn($v) => $v && $v !== '-')->first() ?? null;
                                    $baggage = $fare['adult']['baggage'] ?? null;
                                    $seat    = collect($fare['benefit']['seatSelection'] ?? [])->flatten()->filter(fn($v) => $v && $v !== '-')->first() ?? null;
                                    $fareCurrency = $fare['currency'] ?? '-';
                                @endphp
                                <tr class="border-t border-gray-800 hover:bg-gray-800/30">
                                    <td class="px-3 py-2 text-gray-200 capitalize">{{ $cabin ?: '-' }}</td>
                                    <td class="px-3 py-2 text-gray-400 font-mono">{{ $fareBasis ?: '-' }}</td>
                                    <td class="px-3 py-2">
                                        <span class="font-mono font-semibold text-yellow-300">{{ $fareCurrency }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-right text-gray-200">{{ number_format($base) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-400">{{ number_format($tax) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-400">{{ number_format($other) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-green-400">{{ number_format($total) }}</td>
                                    @if(($runPayload['child'] ?? 0) > 0)
                                    <td class="px-3 py-2 text-right font-semibold text-blue-400">{{ $childTotal !== null ? number_format($childTotal) : '-' }}</td>
                                    @endif
                                    @if(($runPayload['infant'] ?? 0) > 0)
                                    <td class="px-3 py-2 text-right font-semibold text-purple-400">{{ $infantTotal !== null ? number_format($infantTotal) : '-' }}</td>
                                    @endif
                                    <td class="px-3 py-2 text-center {{ $meal ? 'text-green-400' : 'text-gray-600' }}">{{ $meal ? '✓' : '✗' }}</td>
                                    <td class="px-3 py-2 text-center text-gray-400">{{ is_null($baggage) ? '-' : $baggage }}</td>
                                    <td class="px-3 py-2 text-center {{ $seat ? 'text-green-400' : 'text-gray-600' }}">{{ $seat ? '✓' : '✗' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
