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
    private const FILE_NAME = 'component';

    private const FILE_EXT = '.php';

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
        return Collection::make($this->component->get('component'));
    }

    /**
     * Return component collection.
     *
     * @return Collection
     */
    public function getApi(): Collection
    {
        return Collection::make($this->component->get('api'));
    }

    /**
     * Return the component file path.
     *
     * @return string
     */
    public function getComponentFilePath(): string
    {
        return $this->componentPath .'/' . self::FILE_NAME . self::FILE_EXT;
    }

    /**
     * Return the component file stub.
     *
     * @return string
     */
    public function getComponentFileStub(): string
    {
        return __DIR__.'/stubs/component.stub';
    }

    /**
     * Get the component resources.
     *
     * @return Collection
     */
    private function componentSource(): Collection
    {
        $content = [];

        if (realpath($this->componentPath) !== false) {
            $componentFile = Collection::make(
                Finder::create()
                ->files()
                ->name(self::FILE_NAME . self::FILE_EXT)
                ->in($this->componentPath)
            )->first();

            if ($componentFile !== null) {
                $content = include $componentFile->getRealPath();
            }
        }

        return Collection::make($content);
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

        return $baseDir . '/' . self::FILE_NAME;
    }
}
