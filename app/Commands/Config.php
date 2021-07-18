<?php

namespace App\Commands;

use App\Libs\Executors\Discovery\Host as HostDiscovery;
use App\Libs\Executors\Discovery\Port as PortDiscovery;
use App\Libs\Executors\Audit\Website as WebsiteAudit;
use App\Libs\Executors\Audit\Docker as DockerAudit;
use App\Libs\Executors\Audit\Repository as RepositoryAudit;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;
use App\Models\Host;
use App\Models\Website;
use App\Models\Certificate;
use App\Models\Port;
use App\Models\Docker;
use App\Models\Repository;
use App\Models\Finding;
use App\Models\Pivots\HostWebsite;
use Exception;

class Config extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config {config} {--clear-db}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Perform a scan based on a config.yml file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('clear-db')) {
            $this->clearDB();
        }

        $configPath = $this->argument('config');

        $this->import($configPath);

        $this->scan();

        if ($this->option('clear-db')) {
            $this->clearDB();
        }
    }

    /**
     * Import assets from a config file.
     *
     * @param string $configPath
     */
    private function import($configPath)
    {
        if (!file_exists($configPath)) {
            throw new Exception('Config file ' . $configPath . ' does not exist');
        }

        $config = Yaml::parse(file_get_contents($configPath));

        if (isset($config['hosts'])) {
            if (!array($config['hosts'])) {
                throw new Exception('Hosts index is not an array');
            }
            foreach ($config['hosts'] as $entry) {
                Host::firstOrCreate(['name' => $entry]);
            }
        }

        if (isset($config['websites'])) {
            if (!array($config['websites'])) {
                throw new Exception('Websites index is not an array');
            }
            foreach ($config['websites'] as $entry) {
                if (is_array($entry)) {
                    $website = Website::firstOrCreate(['url' => $entry['url']]);
                    if (isset($entry['auth'])) {
                        $website->fill(['auth' => $entry['auth']])->save();
                    }
                } else {
                    Website::firstOrCreate(['url' => $entry]);
                }
            }
        }

        if (isset($config['dockers'])) {
            if (!array($config['dockers'])) {
                throw new Exception('Dockers index is not an array');
            }
            foreach ($config['dockers'] as $entry) {
                Docker::firstOrCreate(['name' => $entry]);
            }
        }

        if (isset($config['repositories'])) {
            if (!array($config['repositories'])) {
                throw new Exception('Repositories index is not an array');
            }
            foreach ($config['repositories'] as $entry) {
                Repository::firstOrCreate(['path' => $entry]);
            }
        }
    }

    /**
     * Scan all the assets in the DB.
     */
    private function scan()
    {
        foreach (Host::all() as $host) {
            $this->line(PHP_EOL . '<info>Host: </info><comment>' . $host->name . '</comment>');
            (new HostDiscovery($host, $this->output))->run();

            foreach ($host->ports as $port) {
                $this->line(PHP_EOL . '<info>Port: </info><comment>' . $port->port . '</comment>');
                (new PortDiscovery($port, $this->output))->run();
            }
        }
        foreach (Website::all() as $website) {
            $this->line(PHP_EOL . '<info>Website: </info><comment>' . $website->url . '</comment>');
            (new WebsiteAudit($website, $this->output))->run();
        }
        foreach (Docker::all() as $docker) {
            $this->line(PHP_EOL . '<info>Docker: </info><comment>' . $docker->url . '</comment>');
            (new DockerAudit($docker, $this->output))->run();
        }
        foreach (Repository::all() as $repository) {
            $this->line(PHP_EOL . '<info>Repository: </info><comment>' . $repository->path . '</comment>');
            (new RepositoryAudit($repository, $this->output))->run();
        }

        // Exit code
        if (Finding::count()) {
            exit(1);
        } else {
            exit(0);
        }
    }

    /**
     * Delete all the models from DB.
     */
    private function clearDB()
    {
        Certificate::query()->truncate();
        Website::query()->truncate();
        Docker::query()->truncate();
        Port::query()->truncate();
        Host::query()->truncate();
        HostWebsite::query()->truncate();
        Repository::query()->truncate();
        Finding::query()->truncate();
    }
}
