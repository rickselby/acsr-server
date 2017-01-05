<?php

namespace App\Services\VoiceServer\Discord;

use Httpful\Request;
use Httpful\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DiscordRequest
{
    const CACHE_KEY = 'discord.rate-limit.';

    /** @var Logger */
    private $log;

    public function __construct()
    {
        $this->log = new Logger('discord_api');
        $this->log->pushHandler(new StreamHandler(storage_path('logs/discord.log')));
    }

    /**
     * Send a request with rate limiting
     *
     * @param Request $request
     * @param string|null $rateURI
     *
     * @return Response
     */
    public function send(Request $request, $rateURI = null)
    {
        $rateURI = $rateURI ?? $request->uri;

        $this->log->info('Send', [
            'method' => $request->method,
            'uri' => $request->uri
        ]);

        $cacheKey = self::CACHE_KEY.$rateURI;

        // Check if this route is rate limited and we have info about it already
        $cachedRateLimit = \Cache::get($cacheKey);
        if ($cachedRateLimit) {
            if ($cachedRateLimit['remaining'] == 0) {
                $time = $cachedRateLimit['reset'] - time() + 1;
                if ($time > 0) {
                    $this->log->info('Rate Limited', [
                        'uri' => $rateURI,
                        'time' => $time,
                    ]);
                    sleep($time);
                }
            }
        }

        // Send the request
        $response = $request->send();

        // Check if we got new rate limit information
        if ($response->headers->offsetExists('x-ratelimit-reset')) {
            // Save the information
            \Cache::forever($cacheKey, [
                'remaining' => $response->headers->offsetGet('x-ratelimit-remaining'),
                'reset' => $response->headers->offsetGet('x-ratelimit-reset'),
            ]);
            // Log the limit
            $this->log->info('Got Limit', [
                'uri' => $rateURI,
                'remaining' => $response->headers->offsetGet('x-ratelimit-remaining'),
                'reset' => $response->headers->offsetGet('x-ratelimit-reset'),
                'time' => $response->headers->offsetGet('x-ratelimit-reset') - time(),
            ]);
        }

        return $this->checkForErrors($response);
    }

    /**
     * Check a request for errors; throw exceptions if halting ones found
     *
     * @param Response $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    private function checkForErrors(Response $response)
    {
        try {
            switch ($response->code) {
                case '400':
                    throw new \Exception('Discord: Bad Request');
                case '401':
                    throw new \Exception('Discord: Unauthorized');
                case '403':
                    throw new \Exception('Discord: Permission Denied');
                case '404':
                    throw new \Exception('Discord: 404 Not Found');
                case '405':
                    throw new \Exception('Discord: Method Not Allowed');
                case '429':
                    throw new \Exception('Discord: Too Many Requests');
                default:
            }
        } catch (\Exception $e) {
            // Catch it to log it, then throw it again
            $this->log->info($e->getMessage());
            throw $e;
        }

        return $response;
    }

}
