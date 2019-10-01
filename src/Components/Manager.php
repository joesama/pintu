<?php

namespace Joesama\Pintu\Components;

use Exception;
use ReflectionClass;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;
use Joesama\Pintu\Components\Concerns\Grammar;

class Manager
{
    use Grammar;

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
     * Component controller namespace.
     *
     * @var array
     */
    protected $controllerNamespace;

    /**
     * Initiate Component Manager.
     *
     * @param ReflectionClass $providerReflection
     */
    public function __construct(ReflectionClass $providerReflection)
    {
        if (!$providerReflection->isSubclassOf(ServiceProvider::class)) {
            throw new Exception(
                $providerReflection->getName() . ' must an instance of ' .  \basename(ServiceProvider::class),
                1
            );
        }

        $this->componentPath = $this->componentPath($providerReflection);

        $this->component = $this->componentSource();

        $this->controllerNamespace = $this->registerNamespace($providerReflection);
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
        return $this->componentPath . '/' . self::FILE_NAME . self::FILE_EXT;
    }

    /**
     * Return the component file stub.
     *
     * @return string
     */
    public function getComponentFileStub(): string
    {
        return __DIR__ . '/stubs/component.stub';
    }

    /**
     * Get default component name space.
     *
     * @return array
     */
    public function getComponentNameSpace(): array
    {
        return $this->controllerNamespace;
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
     * @param ReflectionClass $providerReflection
     *
     * @return string
     */
    private function componentPath(ReflectionClass $providerReflection): string
    {
        $classNameSpace = Collection::make(explode('\\', $providerReflection->getNamespaceName()));

        $baseDir = dirname($providerReflection->getFileName(), $classNameSpace->count());

        return $baseDir . '/' . self::FILE_NAME;
    }
}
