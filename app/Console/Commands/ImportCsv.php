<?php

namespace App\Console\Commands;

use App\Imports\ProductDetailConsoleImport;
use App\Imports\ProductDetailImport;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;

class ImportCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom csv importer by amirnajafidev@gmail.com';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->title('Starting import');
        if (!file_exists($this->argument('path'))) {
            $this->output->error('File not exists. Nothing Imported.');
            return;
        }
        ini_set('memory_limit', '700M');
        $start = microtime(true);
        (new ProductDetailConsoleImport())->withOutput($this->output)->import($this->argument('path'));
        $time_elapsed_secs = sprintf('%g',microtime(true) - $start );

        $this->output->success('Import successful in ' . $time_elapsed_secs . ' seconds');
    }
}
