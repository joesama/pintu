<?php

namespace Joesama\Pintu\Routings\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Grammar
{
    /**
     * Path naming convention definition.
     *
     * @param string $type
     * @param string $function
     * @param array $attributes
     *
     * @return string
     */
    public function pathConvention(string $type, string $function, array $attributes): string
    {
        $keymap = $this->keymapIsString(Arr::get($attributes, 'keymap'));

        $keymap = $this->appendIdParameter($keymap);

        if (!\is_array($keymap) || empty($keymap)) {
            return $function;
        } else {
            $keymap = collect($keymap)->map(function ($id) {
                return '{' . $id . '}';
            })->implode('/');

            return $function . '/' . $keymap;
        }
    }

    /**
     * Check if keymap is string.
     *
     * @param $keymap
     *
     * @return array
     */
    private function keymapIsString($keymap): array
    {
        if (\is_string($keymap) && Str::length($keymap) > 0) {
            return [$keymap];
        }

        return [];
    }

    /**
     * Append ID parameter.
     *
     * @param array $keymap
     *
     * @return array
     */
    private function appendIdParameter(array $keymap): array
    {
        // @todo Need to analize the need on default id parameter.
        // if (Str::lower($type) === 'head') {
        //     if (!in_array('id', $keymap)) {
        //         $keymap = Arr::prepend($keymap, 'id');
        //     }
        // }

        return $keymap;
    }

    /**
     * Class naming convention definition.
     *
     * @param string $type
     * @param string $controller
     * @param string $function
     *
     * @return string
     */
    public function classConvention(string $type, string $controller, string $function): string
    {
        return Str::ucfirst($controller) . 'Controller@' . Str::camel(Str::lower($type) . '_' . $function);
    }

    /**
     * Router name naming convention.
     *
     * @param string $type
     * @param string $controller
     * @param string $function
     * @param array $attributes
     *
     * @return string
     */
    public function namedConvention(string $type, string $controller, string $function, array $attributes): string
    {
        $named = Arr::get($attributes, 'named', null);

        if ($named === '' || $named === null) {
            $named = Str::lower($controller . '.' . $type . '.l' . $function);
        }

        return Str::lower($named);
    }
}
