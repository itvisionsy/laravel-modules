<?php

namespace ItvisionSy\Laravel\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use ItvisionSy\Laravel\Modules\StoreHandlers\SimpleDbStoreHandler;

class InitiateDatabaseTable extends Command implements SelfHandling
{

    protected $signature = 'modules:db:init';
    protected $description = 'Initiate the database table for storage';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $result = SimpleDbStoreHandler::createTable();
            if ($result) {
                $this->info("Table modules_storage created successfully!");
            } else {
                $this->error("Something went wrong!");
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
