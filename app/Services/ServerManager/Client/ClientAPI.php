<?php

namespace App\Services\ServerManager\Client;

use App\Models\Server;
use Httpful\Exception\ConnectionErrorException;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;

class ClientAPI
{
    /**
     * @param Server $server
     * @param string $config
     * @return bool
     */
    public function setConfig(Server $server, string $config)
    {
        $response = $this->put($server, '/config', ['content' => $config]);
        return $response->body->updated;
    }

    /**
     * @param Server $server
     * @param string $entryList
     * @return bool
     */
    public function setEntryList(Server $server, string $entryList)
    {
        $response = $this->put($server, '/entrylist', ['content' => $entryList]);
        return $response->body->updated;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function ping(Server $server)
    {
        try {
            $response = $this->get($server, '/ping');
        } catch (ConnectionErrorException $e) {
            return false;
        }
        return $response->body->success;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function start(Server $server)
    {
        $response = $this->put($server, '/start');
        return $response->body->success;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function stop(Server $server)
    {
        $response = $this->put($server, '/stop');
        return $response->body->success;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function running(Server $server)
    {
        $response = $this->get($server, '/running');
        return $response->body->running;
    }

    /**
     * @param Server $server
     * @return string
     */
    public function latestResults(Server $server)
    {
        $response = $this->get($server, '/results/latest');
        return $response->body->results;
    }

    /**
     * @param Server $server
     * @return array
     */
    public function allResults(Server $server)
    {
        $response = $this->get($server, '/results/all');
        return $response->body;
    }

    /**
     * @param Server $server
     * @return string
     */
    public function serverLog(Server $server)
    {
        $response = $this->get($server, '/log/server');
        return $response->body->log;
    }

    /**
     * @param Server $server
     * @return array
     */
    public function systemLog(Server $server)
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
    private function put(Server $server, $path, $body = [])
    {
        return $this->send(
            Request::put($this->url($server, $path)),
            $body
        );
    }

    /**
     * Execute a get request
     *
     * @param Server $server
     * @param string $path
     *
     * @return Response
     */
    private function get(Server $server, $path)
    {
        return $this->send(
            Request::get($this->url($server, $path))
        );
    }

    /**
     * Get the full URL for a request
     *
     * @param Server $server
     * @param string $path
     *
     * @return string
     */
    private function url(Server $server, $path)
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
