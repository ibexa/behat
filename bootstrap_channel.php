<?php

declare(strict_types=1);

/**
 * Bootstrap channel routing for parallel Behat / fastest execution.
 *
 * Rewrites WEB_HOST port, DATABASE_NAME, DATABASE_URL, and CACHE_NAMESPACE
 * so that concurrent Behat processes operate on isolated databases, caches, and Nginx vhosts.
 */

$channel = (int)(getenv('ENV_TEST_CHANNEL_READABLE') ?: getenv('ENV_TEST_CHANNEL') ?: 0);
$channelsNumber = (int)(getenv('ENV_TEST_CHANNELS_NUMBER') ?: getenv('PROCESS_COUNT') ?: 0);
$webHost = getenv('WEB_HOST') ?: 'http://web';

// When running with 1 process (or outside parallel fastest), do not rewrite to ezp_1 or port 8081;
// use the base database (ezp) and standard web port.
$isMultiProcess = ($channelsNumber > 1) || ($channel > 1) || ($channelsNumber === 0 && $channel > 0 && getenv('PROCESS_COUNT') !== '1');

if ($channel >= 1 && $isMultiProcess) {
    // Route web traffic to dedicated channel port (8081 for channel 1, 8082 for channel 2, etc.)
    $channelPort = 8080 + $channel;
    // If webHost points to reverse proxies like varnish, route directly to web container
    $webHost = preg_replace('#://varnish(?::\d+)?#', '://web', $webHost);
    if (preg_match('#^(https?://[^:/]+)(?::\d+)?(.*)$#', $webHost, $matches)) {
        $webHost = $matches[1] . ':' . $channelPort . $matches[2];
    } else {
        $webHost = rtrim($webHost, '/') . ':' . $channelPort;
    }
    putenv("WEB_HOST=$webHost");
    $_ENV['WEB_HOST'] = $webHost;
    $_SERVER['WEB_HOST'] = $webHost;

    // Route database connection for CLI Symfony / Doctrine
    $rawDbName = getenv('DATABASE_NAME') ?: 'ezp';
    // Strip any existing _<channel> suffix to ensure idempotence
    $baseDbName = preg_replace('/_\d+$/', '', $rawDbName) ?: 'ezp';
    $channelDbName = $baseDbName . '_' . $channel;

    putenv("DATABASE_NAME=$channelDbName");
    $_ENV['DATABASE_NAME'] = $channelDbName;
    $_SERVER['DATABASE_NAME'] = $channelDbName;

    $currentUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? getenv('DATABASE_URL');
    if ($currentUrl) {
        $parsed = parse_url($currentUrl);
        if ($parsed && isset($parsed['scheme'])) {
            $scheme = $parsed['scheme'] . '://';
            $user = $parsed['user'] ?? '';
            $pass = isset($parsed['pass']) ? ':' . $parsed['pass'] : '';
            $auth = ($user !== '' || $pass !== '') ? "$user$pass@" : '';
            $host = $parsed['host'] ?? '';
            $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            $path = '/' . $channelDbName;
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

            $newUrl = "$scheme$auth$host$port$path$query$fragment";
            putenv("DATABASE_URL=$newUrl");
            $_ENV['DATABASE_URL'] = $newUrl;
            $_SERVER['DATABASE_URL'] = $newUrl;
        }
    }

    // Route cache namespace for Redis / Valkey SPI cache per channel
    $channelCacheNs = 'channel_' . $channel;
    putenv("CACHE_NAMESPACE=$channelCacheNs");
    $_ENV['CACHE_NAMESPACE'] = $channelCacheNs;
    $_SERVER['CACHE_NAMESPACE'] = $channelCacheNs;
} else {
    // Single process mode: ensure base CACHE_NAMESPACE is explicitly exported
    $baseCacheNs = getenv('CACHE_NAMESPACE') ?: 'ezp';
    putenv("CACHE_NAMESPACE=$baseCacheNs");
    $_ENV['CACHE_NAMESPACE'] = $baseCacheNs;
    $_SERVER['CACHE_NAMESPACE'] = $baseCacheNs;
}
