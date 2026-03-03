<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class SupplierMonitor extends Component
{
    public string $supplier  = 'vietjetair';
    public string $tripType  = 'oneway';
    public string $origin    = 'SGN';
    public string $dest      = 'HAN';
    public string $dptDate   = '';
    public string $rtnDate   = '';
    public int    $adult     = 1;
    public int    $child     = 0;
    public int    $infant    = 0;
    public string $proxy     = '';

    public ?array  $results      = null;
    public ?array  $errorData    = null;
    public bool    $loading      = false;
    public ?int    $responseTime = null;
    public ?string $rawJson      = null;
    public bool    $showRaw      = false;

    public function mount(): void
    {
        $default = config('payload.default_payload');

        $this->origin  = $default['origin'];
        $this->dest    = $default['dest'];
        $this->adult   = $default['adult'];
        $this->child   = $default['child'];
        $this->infant  = $default['infant'];
        $this->proxy   = $default['proxy'] ?? '';
        $this->dptDate = now()->addDays(7)->format('Y-m-d');
        $this->rtnDate = now()->addDays(14)->format('Y-m-d');
    }

    public function run(): void
    {
        $this->results      = null;
        $this->errorData    = null;
        $this->rawJson      = null;
        $this->showRaw      = false;
        $this->loading      = true;

        $nodeUrl   = config('payload.node_server_url');
        $suppliers = config('payload.suppliers');
        $endpoint  = $suppliers[$this->supplier][$this->tripType];

        $payload = [
            'origin'  => strtoupper(trim($this->origin)),
            'dest'    => strtoupper(trim($this->dest)),
            'adult'   => $this->adult,
            'child'   => $this->child,
            'infant'  => $this->infant,
            'dptDate' => $this->dptDate,
        ];

        if ($this->tripType === 'roundtrip') {
            $payload['rtnDate'] = $this->rtnDate;
        }

        if (!empty(trim($this->proxy))) {
            $payload['proxy'] = trim($this->proxy);
        }

        $startTime = microtime(true);

        try {
            $response = Http::timeout(120)->post($nodeUrl . $endpoint, $payload);
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);

            $data = $response->json();
            $this->rawJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if (is_array($data) && isset($data['error']) && $data['error'] === true) {
                $this->errorData = $data;
            } else {
                $this->results = is_array($data) ? $data : [];
            }
        } catch (\Exception $e) {
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);
            $this->errorData    = ['error' => true, 'msg' => $e->getMessage()];
            $this->rawJson      = json_encode($this->errorData, JSON_PRETTY_PRINT);
        }

        $this->loading = false;
    }

    public function toggleRaw(): void
    {
        $this->showRaw = !$this->showRaw;
    }

    public function render()
    {
        return view('livewire.supplier-monitor', [
            'suppliers' => config('payload.suppliers'),
        ])->layout('layouts.monitor');
    }
}
