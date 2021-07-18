<?php

namespace App\Libs\Modules\Discovery\Hosts\Ports;

use App\Libs\Contracts\Modules\Discovery;
use App\Libs\Contracts\Modules\Traits\Process;
use Illuminate\Support\Facades\Storage;

/**
 * Host Nmap Discovery Module.
 *
 * Enumerates open Ports of a Host using nmap.
 */
class Nmap extends Discovery
{
    use Process;

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/nmap_' . $this->model->name . '.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runProcess([env('TOOLS_NMAP'), '-Pn', '-sT', $this->model->name, '-oX', storage_path('app/' . $this->tmp)]);
        $content = Storage::get($this->tmp);
        Storage::delete($this->tmp);
        $this->store($content);
        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param string $content
     */
    private function store(string $content)
    {
        $xml = simplexml_load_string($content);

        if (!isset($xml->host->ports->port)) {
            return;
        }

        foreach ($xml->host->ports->port as $portEntry) {
            if (!$this->isPortOpen($portEntry)) {
                continue;
            }

            $portNumber = $this->getPortNumber($portEntry);

            $port = $this->model->ports()->firstOrCreate([
                'port' => $portNumber
            ]);
            $port->protocol = $this->getPortProtocol($portEntry);
            $port->service = $this->getPortServiceName($portEntry);
            $port->save();

            $this->items[] = $port;
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail('Port', $item->protocol . ' ' . $item->port . ' ' . $item->service);
        }
    }

    /**
     * Checks if a port is open.
     *
     * @param  \SimpleXMLElement $port
     * @return bool
     */
    private function isPortOpen($port)
    {
        if (!isset($port->state->attributes()->state)) {
            return false;
        }
        if ((string) $port->state->attributes()->state !== 'open') {
            return false;
        }

        return true;
    }

    /**
     * Get the protocol from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return string|void
     */
    private function getPortProtocol($port)
    {
        if (!isset($port->attributes()->protocol[0])) {
            return;
        }

        return (string) $port->attributes()->protocol[0];
    }

    /**
     * Get the port number from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return int|void
     */
    private function getPortNumber($port)
    {
        if (!isset($port->attributes()->portid[0])) {
            return;
        }

        return (string) $port->attributes()->portid[0];
    }

    /**
     * Get the service name from a port.
     *
     * @param  \SimpleXMLElement $port
     * @return string|void
     */
    private function getPortServiceName($port)
    {
        if (!isset($port->service->attributes()->name[0])) {
            return;
        }

        return (string) $port->service->attributes()->name[0];
    }
}
