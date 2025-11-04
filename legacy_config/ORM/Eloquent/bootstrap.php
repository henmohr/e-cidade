<?php
use Illuminate\Database\Capsule\Manager;

final class EloquentBootstrap {

    public Manager $manager;

    public function __construct(string $host, string $database, string $username, string $password, $port)
    {
        $this->manager = new Manager();
        $this->manager->addConnection([
            'driver' => 'pgsql',
            'host' =>$host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'charset' => 'latin1',
            'collation' => 'pt_BR',
            'prefix' => '',
            'strict' => false,
            'prefix_indexes' => true,
            'search_path' => 'public',
        ]);

        $this->manager->setAsGlobal();
        return $this;
    }

    public function bootstrap(): void
    {
        $this->manager->bootEloquent();
    }
}
