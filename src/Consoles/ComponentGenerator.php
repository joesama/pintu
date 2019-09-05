<?php
namespace Joesama\Pintu\Consoles;

use ReflectionClass;
use Illuminate\Console\Command;

class ComponentGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pintu:component {package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create component for package';

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
        $package = $this->argument('package');

        $reflector = new ReflectionClass($package);

        dump(\get_class_methods($reflector));
        dump($reflector->getShortName());
        $path = strstr($reflector->getFileName(), $reflector->getShortName(), true);
        dump($path);
        $componentPath = \realpath($path.'/../');
        dump($componentPath);
        dd($reflector->getFileName());
    }  
}