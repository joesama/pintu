<?php
namespace Joesama\Pintu\Components;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Validator
{
    private $stubs;

    private $component;

    public function __construct(array $component)
    {
        $this->component = $component;
    }

    /**
     * Validate component format
     *
     * @return bool
     */
    public function validate(): bool
    {
        $this->stubs = $this->componentStub();

        $validStructure = $this->validateComponent();

        if (!$validStructure) {
            throw new Exception('Component usage has been violate', 402);
        };

        return $validStructure;
    }

    private function validateComponent(): bool
    {
        list($keys, $values) = Arr::divide($this->stubs);

        $api = Arr::where($keys, function ($key) {
            return !Arr::exists($this->component, $key);
        });

        return empty($api) ? true : false;
    }

    /**
     * Get Stub For Component Format.
     *
     * @return array
     */
    protected function componentStub(): array
    {
        $stubstPath = realpath(__DIR__. '/../../resources/stubs/component.json');

        return json_decode(file_get_contents($stubstPath), true);
    }
}
