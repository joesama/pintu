<?php

namespace Joesama\Pintu\Consoles;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Joesama\Pintu\Components\Manager;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuStyle;
use ReflectionClass;

class ComponentGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pintu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create package component & routing configuration file.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $file)
    {
        $placeHolder = 'Joesama\\Pintu\\PintuProvider';

        $console = $this;

        $itemCallable = function (CliMenu $menu) use ($placeHolder, $console, $file) {
            $successStyle = (new MenuStyle)
                ->setBg('254')
                ->setFg('166');

            $result = $menu->askText($successStyle)
                ->setPromptText('Type service provider namespace. [esc] to exit')
                ->setPlaceholderText($placeHolder)
                ->setValidationFailedText('Please type full qualified service provider namespace')
                ->setValidator(function ($provider) use ($placeHolder, $console) {
                    if (! $console->fileIsExist($provider)) {
                        $this->setValidationFailedText("Class {$provider} must be exist");

                        return false;
                    }

                    return $provider !== $placeHolder;
                })
                ->ask();

            $providerOption = $result->fetch();

            if (strlen($providerOption) > 0 && $providerOption !== $placeHolder) {
                $console->makeComponentFile($menu, $file, $result->fetch());
            }
        };

        $menu = ($builder = new CliMenuBuilder)
            ->setWidth($builder->getTerminal()->getWidth() - 2 * 2)
            ->setMarginAuto()
//            ->setPadding(2, 4)
            ->setTitle('Component File Generator')
            ->setTitleSeparator('=')
            ->setBackgroundColour('166')
            ->setForegroundColour('254')
            ->setExitButtonText('Leave console')
//            ->setUnselectedMarker('❅ ')
//            ->setSelectedMarker('> ')
            ->addItem('Create component file', $itemCallable)
            ->build();

        $menu->open();
    }

    /**
     * Check service provider file exist.
     *
     * @param  string  $provider
     * @return bool
     */
    private function fileIsExist(string $provider): bool
    {
        return class_exists($provider) ? true : false;
    }

    /**
     * Generate component file.
     *
     * @param  CliMenu  $menu
     * @param  Filesystem  $file
     * @param  string  $providerOption
     * @return void
     */
    private function makeComponentFile(CliMenu $menu, Filesystem $file, string $providerOption)
    {
        $provider = new ReflectionClass($providerOption);

        $manager = new Manager($provider);

        $filePath = $manager->getComponentFilePath();

        $stub = $file->get($manager->getComponentFileStub());

        $successStyle = (new MenuStyle)
            ->setBg('254')
            ->setFg('166');

        if ($file->exists($filePath)) {
            $result = $menu->askText($successStyle)
                ->setPromptText("Component file {$filePath} already exists!. Want to overwrite?")
                ->setPlaceholderText(' Y / N ')
                ->setValidationFailedText('Please choose either Y / N')
                ->setValidator(function ($force) {
                    return in_array(strtolower($force), ['y', 'n']);
                })->ask();

            $force = $result->fetch();

            if (strtolower($force) === strtolower('N')) {
                $menu->confirm('Component file are remained same!!!', $successStyle)->display('OK!');
            } elseif (strtolower($force) === strtolower('Y')) {
                $file->copy($filePath, \str_replace('component.php', date('dmyHis').'.php', $filePath));

                $this->copyStubToLocation($file, $filePath, $stub);

                $menu->confirm('Component file successfully overwritten!!!', $successStyle)->display('OK!');
            }
        } else {
            $this->copyStubToLocation($file, $filePath, $stub);

            $menu->confirm('Component file successfully created!!!', $successStyle)->display('OK!');
        }
    }

    /**
     * Copy stubs to location.
     *
     * @param  Filesystem  $file
     * @param  string  $filePath
     * @param $stub
     * @return void
     */
    private function copyStubToLocation(Filesystem $file, string $filePath, $stub)
    {
        if (! $file->isDirectory(dirname($filePath))) {
            $file->makeDirectory(dirname($filePath), 0655, true, true);
        }

        $file->put($filePath, $stub);
    }
}
