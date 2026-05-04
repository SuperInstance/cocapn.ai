<?php
/**
 * CocapnPlatoClient — PLATO API wrapper for cocapn.ai
 * Connects to local PLATO room server and keeper agent registry
 */

class CocapnPlatoClient {
    private string $plato_host = '127.0.0.1';
    private int $plato_port = 8847;
    private string $keeper_host = '127.0.0.1';
    private int $keeper_port = 8900;
    private float $timeout = 3.0;

    /** Fetch all rooms */
    public function get_all_rooms(): array {
        return $this->api_get('/rooms');
    }

    /** Fetch a single room by name */
    public function get_room(string $name): array {
        $encoded = urlencode($name);
        return $this->api_get("/room/{$encoded}");
    }

    /** Submit a new tile to PLATO */
    public function submit_tile(array $tile): array {
        return $this->api_post('/submit', $tile);
    }

    /** Search tiles across all rooms */
    public function search_tiles(string $query): array {
        return $this->api_get('/search?q=' . urlencode($query));
    }

    /** Get fleet status from keeper */
    public function get_fleet_status(): array {
        return $this->api_get_keeper('/status');
    }

    /** Get agent registry */
    public function get_agent_registry(): array {
        $status = $this->get_fleet_status();
        return $status['agents'] ?? [];
    }

    /** Check health of all services */
    public function get_service_health(): array {
        $services = [
            'keeper'      => ['host' => $this->keeper_host, 'port' => 8900],
            'agent-api'   => ['host' => $this->keeper_host, 'port' => 8901],
            'PLATO'       => ['host' => $this->plato_host,  'port' => 8847],
            'seed-mcp'    => ['host' => $this->plato_host,  'port' => 9438],
            'crab-trap'   => ['host' => $this->plato_host,  'port' => 4042],
            'lock'        => ['host' => $this->plato_host,  'port' => 4043],
            'arena'       => ['host' => $this->plato_host,  'port' => 4044],
            'grammar'     => ['host' => $this->plato_host,  'port' => 4045],
            'conduwuit'   => ['host' => $this->plato_host,  'port' => 6167],
            'bridge'      => ['host' => $this->plato_host,  'port' => 6168],
        ];

        $results = [];
        foreach ($services as $name => $cfg) {
            $start = microtime(true);
            $ok = $this->ping_port($cfg['host'], $cfg['port']);
            $ms = round((microtime(true) - $start) * 1000, 1);
            $results[$name] = [
                'port'   => $cfg['port'],
                'status' => $ok ? 'up' : 'down',
                'ms'     => $ok ? $ms : null,
            ];
        }
        return $results;
    }

    /** Internal GET to PLATO server */
    private function api_get(string $path): array {
        $url = "http://{$this->plato_host}:{$this->plato_port}{$path}";
        $ctx = stream_context_create([
            'http' => ['timeout' => $this->timeout, 'ignore_errors' => true]
        ]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) return [];
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Internal GET to keeper */
    private function api_get_keeper(string $path): array {
        $url = "http://{$this->keeper_host}:{$this->keeper_port}{$path}";
        $ctx = stream_context_create([
            'http' => ['timeout' => $this->timeout, 'ignore_errors' => true]
        ]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) return [];
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Internal POST to PLATO */
    private function api_post(string $path, array $data): array {
        $url = "http://{$this->plato_host}:{$this->plato_port}{$path}";
        $ctx = stream_context_create([
            'http' => [
                'timeout' => $this->timeout,
                'method'  => 'POST',
                'header'  => "Content-Type: application/json",
                'content' => json_encode($data),
                'ignore_errors' => true,
            ]
        ]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) return ['error' => 'Connection failed'];
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** TCP ping a port */
    private function ping_port(string $host, int $port): bool {
        $fp = @fsockopen($host, $port, $errno, $errstr, 1.0);
        if (!$fp) return false;
        fclose($fp);
        return true;
    }

    /** Format uptime from seconds */
    public function format_uptime(int $seconds): string {
        if ($seconds < 60) return "{$seconds}s";
        if ($seconds < 3600) return round($seconds / 60, 1) . "m";
        if ($seconds < 86400) return round($seconds / 3600, 1) . "h";
        return round($seconds / 86400, 1) . "d";
    }
}