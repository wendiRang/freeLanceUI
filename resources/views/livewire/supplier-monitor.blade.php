<div class="max-w-screen-2xl mx-auto">

    {{-- ==================== PAYLOAD FORM ==================== --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-widest">Payload Configuration</h2>
            <div class="flex gap-2">
                {{-- Supplier selector --}}
                <select wire:model="supplier"
                    class="bg-gray-800 text-gray-200 text-sm border border-gray-700 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($suppliers as $key => $sup)
                        <option value="{{ $key }}">{{ $sup['name'] }}</option>
                    @endforeach
                </select>

                {{-- Trip type --}}
                <div class="flex bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
                    <button wire:click="$set('tripType', 'oneway')"
                        class="px-4 py-1.5 text-sm font-medium transition-colors
                            {{ $tripType === 'oneway' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' }}">
                        One Way
                    </button>
                    <button wire:click="$set('tripType', 'roundtrip')"
                        class="px-4 py-1.5 text-sm font-medium transition-colors
                            {{ $tripType === 'roundtrip' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' }}">
                        Round Trip
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
            {{-- Origin --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Origin</label>
                <input wire:model="origin" type="text" placeholder="SGN"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Dest --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Destination</label>
                <input wire:model="dest" type="text" placeholder="HAN"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Depart Date --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Depart Date</label>
                <input wire:model="dptDate" type="date"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Return Date --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Return Date</label>
                <input wire:model="rtnDate" type="date"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        {{ $tripType === 'roundtrip' ? 'text-white' : 'text-gray-600 cursor-not-allowed' }}"
                    {{ $tripType !== 'roundtrip' ? 'disabled' : '' }} />
            </div>

            {{-- Adult --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Adult</label>
                <input wire:model="adult" type="number" min="1" max="9"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Child --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Child</label>
                <input wire:model="child" type="number" min="0" max="9"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Infant --}}
            <div class="col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Infant</label>
                <input wire:model="infant" type="number" min="0" max="9"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Run Button --}}
            <div class="col-span-1 flex items-end">
                <button wire:click="run" wire:loading.attr="disabled"
                    class="w-full bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500
                        text-white font-semibold text-sm rounded-lg px-4 py-2 transition-colors flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="run">Run</span>
                    <span wire:loading wire:target="run" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Running...
                    </span>
                </button>
            </div>
        </div>

        {{-- Proxy (full width row) --}}
        <div class="mt-3">
            <label class="block text-xs text-gray-500 mb-1">Proxy <span class="text-gray-600">(optional)</span></label>
            <input wire:model="proxy" type="text" placeholder="http://user:pass@host:port"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-300 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
    </div>

    {{-- ==================== STATUS BAR ==================== --}}
    @if($responseTime !== null || $loading)
    <div class="mb-4 flex items-center gap-3 text-sm">
        @if($loading)
            <span class="flex items-center gap-2 text-yellow-400">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Hitting {{ $suppliers[$supplier]['name'] ?? $supplier }} ...
            </span>
        @elseif($errorData)
            <span class="flex items-center gap-2 px-3 py-1 rounded-full bg-red-900/40 text-red-400 border border-red-800">
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                Error: {{ $errorData['msg'] ?? 'Unknown error' }}
            </span>
        @elseif($results !== null)
            <span class="flex items-center gap-2 px-3 py-1 rounded-full bg-green-900/40 text-green-400 border border-green-800">
                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                {{ count($results) }} flight option(s) found
            </span>
        @endif

        @if($responseTime !== null && !$loading)
            <span class="text-gray-500">{{ number_format($responseTime) }} ms</span>

            @if($rawJson)
                <button wire:click="toggleRaw"
                    class="ml-auto text-xs text-gray-400 hover:text-white border border-gray-700 rounded px-2 py-1 transition-colors">
                    {{ $showRaw ? 'Hide' : 'Show' }} Raw JSON
                </button>
            @endif
        @endif
    </div>
    @endif

    {{-- ==================== RAW JSON ==================== --}}
    @if($showRaw && $rawJson)
    <div class="mb-5 bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-4 py-2 bg-gray-800 flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Raw JSON Response</span>
        </div>
        <pre class="text-xs text-green-300 font-mono p-4 overflow-x-auto max-h-80 scrollbar-thin">{{ $rawJson }}</pre>
    </div>
    @endif

    {{-- ==================== RESULTS ==================== --}}
    @if($results !== null && count($results) > 0)
    <div class="space-y-4">
        @foreach($results as $index => $flight)
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">

            {{-- Flight header --}}
            <div class="bg-gray-800/50 px-5 py-3 flex items-center justify-between border-b border-gray-800">
                <span class="text-sm font-semibold text-gray-300">Option #{{ $index + 1 }}</span>
                <span class="text-xs text-gray-500">
                    {{ count($flight['fares'] ?? []) }} fare(s)
                </span>
            </div>

            <div class="p-5 space-y-5">

                {{-- Departure Segments --}}
                @if(!empty($flight['dptSegments']))
                <div>
                    <div class="text-xs font-semibold text-blue-400 uppercase tracking-widest mb-2">Departure Segments</div>
                    <div class="space-y-2">
                        @foreach($flight['dptSegments'] as $seg)
                        @include('livewire.partials.segment', ['seg' => $seg])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Return Segments --}}
                @if(!empty($flight['rtnSegments']))
                <div>
                    <div class="text-xs font-semibold text-purple-400 uppercase tracking-widest mb-2">Return Segments</div>
                    <div class="space-y-2">
                        @foreach($flight['rtnSegments'] as $seg)
                        @include('livewire.partials.segment', ['seg' => $seg])
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Fares Table --}}
                @if(!empty($flight['fares']))
                <div>
                    <div class="text-xs font-semibold text-yellow-400 uppercase tracking-widest mb-2">Fares</div>
                    <div class="overflow-x-auto scrollbar-thin">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-gray-400">
                                    <th class="text-left px-3 py-2 font-medium">Cabin</th>
                                    <th class="text-left px-3 py-2 font-medium">Fare Basis</th>
                                    <th class="text-left px-3 py-2 font-medium">Currency</th>
                                    <th class="text-right px-3 py-2 font-medium">Adult Base</th>
                                    <th class="text-right px-3 py-2 font-medium">Adult Tax</th>
                                    <th class="text-right px-3 py-2 font-medium">Adult Other</th>
                                    <th class="text-right px-3 py-2 font-medium">Adult Total</th>
                                    @if($child > 0)
                                    <th class="text-right px-3 py-2 font-medium">Child Total</th>
                                    @endif
                                    @if($infant > 0)
                                    <th class="text-right px-3 py-2 font-medium">Infant Total</th>
                                    @endif
                                    <th class="text-center px-3 py-2 font-medium">Meal</th>
                                    <th class="text-center px-3 py-2 font-medium">Baggage</th>
                                    <th class="text-center px-3 py-2 font-medium">Seat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flight['fares'] as $fare)
                                @php
                                    $cabinLabel = collect($fare['cabinClass'] ?? [])->map(fn($l) => is_array($l) ? implode('+', $l) : $l)->implode(' / ');
                                    $fareBasisLabel = collect($fare['fareBasis'] ?? [])->map(fn($l) => is_array($l) ? implode('+', $l) : $l)->implode(' / ');
                                    $adultBase  = $fare['adult']['price']['base']  ?? 0;
                                    $adultTax   = $fare['adult']['price']['tax']   ?? 0;
                                    $adultOther = $fare['adult']['price']['other'] ?? 0;
                                    $adultTotal = $adultBase + $adultTax + $adultOther;
                                    $childTotal  = isset($fare['child'])  ? array_sum($fare['child']['price']  ?? []) : null;
                                    $infantTotal = isset($fare['infant']) ? array_sum($fare['infant']['price'] ?? []) : null;
                                    $meal    = collect($fare['benefit']['meal']    ?? [])->map(fn($v) => is_array($v) ? ($v[0] ?? '-') : $v)->filter()->first() ?? '-';
                                    $baggage = $fare['adult']['baggage'] ?? '-';
                                    $seat    = collect($fare['benefit']['seatSelection'] ?? [])->map(fn($v) => is_array($v) ? ($v[0] ?? '-') : $v)->filter()->first() ?? '-';
                                @endphp
                                <tr class="border-t border-gray-800 hover:bg-gray-800/30 transition-colors">
                                    <td class="px-3 py-2 text-gray-200 font-medium capitalize">{{ $cabinLabel ?: '-' }}</td>
                                    <td class="px-3 py-2 text-gray-400 font-mono">{{ $fareBasisLabel ?: '-' }}</td>
                                    <td class="px-3 py-2 text-gray-300">{{ $fare['currency'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-200">{{ number_format($adultBase) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-400">{{ number_format($adultTax) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-400">{{ number_format($adultOther) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-green-400">{{ number_format($adultTotal) }}</td>
                                    @if($child > 0)
                                    <td class="px-3 py-2 text-right font-semibold text-blue-400">{{ $childTotal !== null ? number_format($childTotal) : '-' }}</td>
                                    @endif
                                    @if($infant > 0)
                                    <td class="px-3 py-2 text-right font-semibold text-purple-400">{{ $infantTotal !== null ? number_format($infantTotal) : '-' }}</td>
                                    @endif
                                    <td class="px-3 py-2 text-center">
                                        @if($meal && $meal !== '-' && $meal !== null)
                                            <span class="text-green-400">✓</span>
                                        @else
                                            <span class="text-gray-600">✗</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center text-gray-400">{{ is_null($baggage) || $baggage === '' ? '-' : $baggage }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($seat && $seat !== '-' && $seat !== null)
                                            <span class="text-green-400">✓</span>
                                        @else
                                            <span class="text-gray-600">✗</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endforeach
    </div>

    @elseif($results !== null && count($results) === 0 && !$errorData)
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-10 text-center text-gray-500">
        No results returned from supplier.
    </div>
    @elseif($results === null && $errorData === null && $responseTime === null)
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-10 text-center text-gray-600">
        Configure the payload above and press <span class="text-gray-400 font-semibold">Run</span> to hit the supplier.
    </div>
    @endif

</div>
