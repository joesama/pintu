<?php
namespace Joesama\Pintu\Consoles;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Joesama\Pintu\Components\Manager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class ComponentGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pintu 
        { provider : Fully qualified service provider name }
        { --f|force : Force to recreate component file. }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Define package component & routing';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Application $app, Filesystem $file)
    {
        $providerOption = $this->argument('provider');

        $provider = $this->isServiceProvider($app, $providerOption);

        if (!$this->fileIsExist($providerOption)
            &&
            ($provider === false) ) {
            return false;
        }

        $manager = new Manager($provider);

        $filePath = $manager->getComponentFilePath();

        $stub = $file->get($manager->getComponentFileStub());

        $this->alreadyExist($file, $filePath);

        $this->forceTriggered($file, $filePath);

        if (! $file->isDirectory(dirname($filePath))) {
            $file->makeDirectory(dirname($filePath), 0655, true, true);
        }

        $file->put($filePath, $stub);

        $this->line('Component file successfully created!');

    }

    /**
     * Check service provider file exist.
     *
     * @param string $provider
     *
     * @return bool
     */
    private function fileIsExist(string $provider): bool
    {
        if (\class_exists($provider)) {
            return true;
        }

        $this->error("class {$provider} must be exist");

        return false;
    }

    /**
     * Provider passed is registered provider.
     *
     * @param [type] $app
     * @param string $providerOption
     *
     * @return bool
     */
    private function isServiceProvider($app, string $providerOption)
    {
        $provider = Arr::first($app->getProviders($providerOption));

        if ($provider === null) {
            $this->error("class {$providerOption} must be instance of ". ServiceProvider::class);

            return false;
        }

        return $provider;
    }

    /**
     * Validate if component file already exist.
     *
     * @return void
     */
    private function alreadyExist($file, $filePath)
    {
        if (! $this->option('force') && $file->exists($filePath)) {
            $this->error(
                'Component file already exists!. Please use --force to re-create component file'
            );

            exit;
        }
    }

    /**
     * Process if --force indicator is used.
     *
     * @param [type] $file
     * @param [type] $filePath
     *
     * @return void
     */
    private function forceTriggered($file, $filePath)
    {
        if ($this->option('force') && $file->exists($filePath)) {
            $force = $this->choice(
                'This will replace current component file. Do you wish to continoue?',
                [ 'Y', "N"]
            );

            if ($force === 'N') {
                $this->line('Component file are remained same!!!');
                
                exit;
            }

            $file->copy($filePath, \str_replace('component.php', date('dmyHis') . '.php', $filePath));
        }
    }
}
