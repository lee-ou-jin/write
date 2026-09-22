<?php

namespace App\Support;

class NovaReverbConfig
{
    public static function make(): array
    {
        $appUrl = config('app.url');
        $appHost = parse_url($appUrl, PHP_URL_HOST);
        $appScheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'https';

        $scheme = config('broadcasting.connections.reverb.options.scheme', $appScheme);
        $port = (int) config('broadcasting.connections.reverb.options.port', 443);
        $host = config('broadcasting.connections.reverb.options.host') ?: $appHost;

        // REVERB_HOST=127.0.0.1 은 Reverb 프로세스용. 브라우저는 APP_URL(공개 도메인)로 접속.
        if (in_array($host, ['127.0.0.1', 'localhost', '0.0.0.0'], true)) {
            $host = $appHost ?: $host;
            $scheme = $appScheme;
            $port = $appScheme === 'https' ? 443 : $port;
        }

        $useTls = $scheme === 'https';

        return [
            'broadcaster' => 'reverb',
            'key' => config('broadcasting.connections.reverb.key'),
            'wsHost' => $host,
            'wsPort' => $useTls ? 80 : $port,
            'wssPort' => $port,
            'forceTLS' => $useTls,
            'enabledTransports' => $useTls ? ['wss'] : ['ws'],
        ];
    }
}
