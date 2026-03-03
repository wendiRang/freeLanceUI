<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SupplierMonitor extends Component
{
    public array  $payloads   = [];
    public array  $rowResults = [];   // keyed by index
    public string $proxy      = '';

    // Detail panel
    public int     $detailIndex   = -1;
    public ?array  $detailResults = null;
    public ?array  $detailError   = null;
    public ?string $detailRaw     = null;
    public bool    $showRaw       = false;

    public bool $loading = false;

    public function mount(): void
    {
        $this->payloads   = $this->loadPayloads();
        $this->rowResults = $this->loadSavedResults();
    }

    public function run(int $index): void
    {
        $this->loading = true;

        $result = $this->execute($index);
        $this->rowResults[$index] = $result;
        $this->saveToFile();
        $this->showDetail($index, $result);

        $this->loading = false;
    }

    public function runAll(): void
    {
        $this->loading = true;
        set_time_limit(0);

        foreach ($this->payloads as $i => $_) {
            $result = $this->execute($i);
            $this->rowResults[$i] = $result;
        }

        $this->saveToFile();
        $this->loading = false;
    }

    public function viewDetail(int $index): void
    {
        $result = $this->rowResults[$index] ?? null;
        if ($result) {
            $this->showDetail($index, $result);
        }
    }

    public function clearAll(): void
    {
        $this->rowResults  = [];
        $this->detailIndex = -1;
        $this->detailResults = null;
        $this->detailError   = null;
        $this->detailRaw     = null;
        $this->saveToFile();
    }

    public function toggleRaw(): void
    {
        $this->showRaw = !$this->showRaw;
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    private function execute(int $index): array
    {
        $payload = $this->payloads[$index] ?? null;

        if (!$payload) {
            return $this->errorResult($index, "Payload not found at index {$index}", 0, $payload);
        }

        $classMap    = config('payload.class_map', []);
        $supplierKey = $classMap[$payload['Class']] ?? strtolower($payload['Class']);
        $nodeUrl     = config('payload.node_server_url');
        $suppliers   = config('payload.suppliers');
        $tripType    = !empty($payload['rtnDate']) ? 'roundtrip' : 'oneway';

        if (!isset($suppliers[$supplierKey])) {
            return $this->errorResult($index, "Supplier [{$payload['Class']}] not configured in payload.php class_map", 0, $payload);
        }

        $endpoint = $suppliers[$supplierKey][$tripType];

        $body = [
            'origin'  => strtoupper($payload['origin']),
            'dest'    => strtoupper($payload['dest']),
            'adult'   => (int) $payload['adult'],
            'child'   => (int) $payload['child'],
            'infant'  => (int) $payload['infant'],
            'dptDate' => $payload['dptDate'],
        ];

        if ($tripType === 'roundtrip') {
            $body['rtnDate'] = $payload['rtnDate'];
        }

        if (!empty(trim($this->proxy))) {
            $body['proxy'] = trim($this->proxy);
        }

        $startTime = microtime(true);

        try {
            $response     = Http::timeout(120)->post($nodeUrl . $endpoint, $body);
            $responseTime = (int) round((microtime(true) - $startTime) * 1000);
            $data         = $response->json();
            $raw          = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if (is_array($data) && isset($data['error']) && $data['error'] === true) {
                return $this->errorResult($index, $data['msg'] ?? 'Unknown error', $responseTime, $payload, $raw);
            }

            $results = is_array($data) ? $data : [];

            return [
                'status'       => 'success',
                'count'        => count($results),
                'errorMsg'     => null,
                'responseTime' => $responseTime,
                'results'      => $results,
                'raw'          => $raw,
                'payload'      => $payload,
                'updatedAt'    => now()->format('Y-m-d H:i:s'),
            ];

        } catch (\Exception $e) {
            $responseTime = (int) round((microtime(true) - $startTime) * 1000);
            $raw          = json_encode(['error' => true, 'msg' => $e->getMessage()], JSON_PRETTY_PRINT);
            return $this->errorResult($index, $e->getMessage(), $responseTime, $payload, $raw);
        }
    }

    private function errorResult(int $index, string $msg, int $responseTime, ?array $payload, ?string $raw = null): array
    {
        return [
            'status'       => 'error',
            'count'        => 0,
            'errorMsg'     => $msg,
            'responseTime' => $responseTime,
            'results'      => null,
            'raw'          => $raw,
            'payload'      => $payload,
            'updatedAt'    => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function showDetail(int $index, array $result): void
    {
        $this->detailIndex   = $index;
        $this->detailResults = $result['results'];
        $this->detailError   = $result['status'] === 'error' ? ['msg' => $result['errorMsg']] : null;
        $this->detailRaw     = $result['raw'];
        $this->showRaw       = false;
    }

    private function loadPayloads(): array
    {
        $path = config_path('payload.json');
        return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
    }

    private function loadSavedResults(): array
    {
        $path = storage_path('app/monitor_results.json');
        return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
    }

    private function saveToFile(): void
    {
        $path = storage_path('app/monitor_results.json');
        file_put_contents($path, json_encode($this->rowResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function render()
    {
        return view('livewire.supplier-monitor');
    }
}
