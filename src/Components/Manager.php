<?php
namespace Joesama\Pintu\Components;

use Exception;
use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;

class Manager
{
    const FILE_NAME = 'component';

    const FILE_EXT = '.php';

    /**
     * Component Path
     *
     * @var string
     */
    protected $componentPath;

    /**
     * Component Object.
     *
     * @var Collection
     */
    protected $component;
    
    /**
     * Initiate Component Manager.
     *
     * @param array $component
     */
    public function __construct($componentProvider)
    {
        if (!($componentProvider instanceof ServiceProvider)) {
            throw new Exception(
                \get_class($componentProvider) . ' must an instance of ' .  \basename(ServiceProvider::class),
                1
            );
        }

        $this->componentPath = $this->componentPath($componentProvider);

        $this->component = $this->componentSource();
    }

    /**
     * Return component collection.
     *
     * @return Collection
     */
    public function getComponent(): Collection
    {
        return Collection::make(Arr::get($this->component, 'component'));
    }

    /**
     * Return component collection.
     *
     * @return Collection
     */
    public function getApi(): Collection
    {
        return Collection::make(Arr::get($this->component, 'api'));
    }

    /**
     * Get the component resources.
     *
     * @return array
     */
    private function componentSource(): array
    {
        $componentFile = collect(
            Finder::create()
            ->files()
            ->name(self::FILE_NAME . self::FILE_EXT)
            ->in(realpath($this->componentPath))
        )->first();

        $content = include $componentFile->getRealPath();

        return $content;
    }

    /**
     * Get the path for define component.
     *
     * @param ServiceProvider $provider
     *
     * @return string
     */
    private function componentPath(ServiceProvider $provider): string
    {
        $reflector = new ReflectionClass($provider);
       
        $classNameSpace = Collection::make(explode('\\', $reflector->getNamespaceName()));

        $baseDir = dirname($reflector->getFileName(), $classNameSpace->count());

        $filename = $baseDir . '/' . self::FILE_NAME;

        $componentPath = \realpath($filename);

        if (!$componentPath) {
            throw new Exception("Component directory { $filename } not found", 1);
        }

        return $componentPath;
    }
}
