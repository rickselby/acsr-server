<?php

namespace App\Services\ServerManager\Client;

use App\Models\LinodeServer;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;

class ClientAPI
{
    /**
     * @param LinodeServer $server
     * @param string $config
     * @return bool
     */
    public function setConfig(LinodeServer $server, string $config)
    {
        return $this->put($server, '/config', [
            'contents' => $config
        ])->body;
    }

    /**
     * @param LinodeServer $server
     * @param string $entryList
     * @return bool
     */
    public function setEntryList(LinodeServer $server, string $entryList)
    {
        $response = $this->put($server, '/entrylist', [
            'contents' => $entryList
        ]);
        return $response->body->updated;
    }

    /**
     * @param LinodeServer $server
     * @return bool
     */
    public function ping(LinodeServer $server)
    {
        $response = $this->get($server, '/ping');
        // TODO: check for response code too? Likely to fail a few times...
        return $response->body->success;
    }

    /**
     * @param LinodeServer $server
     * @return bool
     */
    public function start(LinodeServer $server)
    {
        $response = $this->put($server, '/start');
        return $response->body->success;
    }

    /**
     * @param LinodeServer $server
     * @return bool
     */
    public function stop(LinodeServer $server)
    {
        $response = $this->put($server, '/stop');
        return $response->body->success;
    }

    /**
     * @param LinodeServer $server
     * @return bool
     */
    public function running(LinodeServer $server)
    {
        $response = $this->get($server, '/running');
        return $response->body->running;
    }

    /**
     * @param LinodeServer $server
     * @return string
     */
    public function latestResults(LinodeServer $server)
    {
        $response = $this->get($server, '/results/latest');
        return $response->body->results;
    }

    /**
     * @param LinodeServer $server
     * @return array
     */
    public function allResults(LinodeServer $server)
    {
        $response = $this->get($server, '/results/all');
        return $response->body;
    }

    /**
     * @param LinodeServer $server
     * @return string
     */
    public function serverLog(LinodeServer $server)
    {
        $response = $this->get($server, '/log/server');
        return $response->body->log;
    }

    /**
     * @param LinodeServer $server
     * @return array
     */
    public function systemLog(LinodeServer $server)
    {
        $response = $this->get($server, '/log/system');
        return $response->body;
    }

    /**************************************************************************
     * Private stuff from here
     *************************************************************************/

    /**
     * Execute a put request
     *
     * @param string $url
     * @param array $body
     *
     * @return Response
     */
    private function put(LinodeServer $server, $path, $body = [])
    {
        return $this->send(
            Request::put($this->url($server, $path)),
            $body
        );
    }

    /**
     * Execute a get request
     *
     * @param LinodeServer $server
     * @param string $path
     *
     * @return Response
     */
    private function get(LinodeServer $server, $path)
    {
        return $this->send(
            Request::get($this->url($server, $path))
        );
    }

    /**
     * Get the full URL for a request
     *
     * @param LinodeServer $server
     * @param string $path
     *
     * @return string
     */
    private function url(LinodeServer $server, $path)
    {
        return 'http://'.$server->ip.$path;
    }

    /**
     * Attatch a body to a request and send the request
     *
     * @param Request $request
     * @param array $body
     *
     * @return Response
     */
    private function send(Request $request, $body = [])
    {
        if (count($body)) {
            $request->sendsType(Mime::JSON)
                ->body(json_encode($body));
        }

        return $request->send();
    }

}
