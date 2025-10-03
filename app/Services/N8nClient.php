<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class N8nClient
{
    private PendingRequest $http;
    private string $base;
    private string $secret;

    public function __construct()
    {
        $this->base   = rtrim(config('services.n8n.base_url'), '/');
        $this->secret = (string) config('services.n8n.hmac_secret');
        $this->http   = Http::timeout(30);
    }

    /** Firma HMAC para JSON puro */
    private function signJson(array $payload): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $sig  = hash_hmac('sha256', $body, $this->secret);
        $ts   = time();

        return [
            'X-Signature' => $sig,
            'X-Timestamp' => $ts,
        ];
    }

    /** Firma HMAC para multipart: firma meta y hash del binario */
    private function signMultipart(string $metaJson, string $binaryPath): array
    {
        $metaSig = hash_hmac('sha256', $metaJson, $this->secret);
        $hash    = hash_file('sha256', $binaryPath);
        $ts      = time();

        return [
            'X-Meta-Signature' => $metaSig,
            'X-Content-Hash'   => $hash,
            'X-Timestamp'      => $ts,
        ];
    }

    /** POST JSON a un webhook n8n privado */
    public function postJson(string $path, array $payload, array $extraHeaders = [])
    {
        $headers = array_merge($this->signJson($payload), $extraHeaders);
        return $this->http->withHeaders($headers)->post("{$this->base}/{$path}", $payload);
    }

    /** POST multipart (audio + meta) al webhook n8n público/privado */
    public function postMultipart(string $path, string $fileField, string $filePath, array $meta, array $extraHeaders = [])
    {
        $metaJson = json_encode($meta, JSON_UNESCAPED_UNICODE);
        $headers  = array_merge($this->signMultipart($metaJson, $filePath), $extraHeaders);

        return $this->http
            ->asMultipart()
            ->withHeaders($headers)
            ->attach($fileField, file_get_contents($filePath), basename($filePath))
            ->attach('meta', $metaJson)
            ->post("{$this->base}/{$path}");
    }
}
