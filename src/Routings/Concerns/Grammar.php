<?php

namespace Joesama\Pintu\Routings\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Grammar
{
    /**
     * Path naming convention definition.
     *
     * @param  string  $type
     * @param  string  $function
     * @param  array  $attributes
     * @return string
     */
    public function pathConvention(string $type, string $function, array $attributes): string
    {
        if ($function === 'index') {
            return '';
        }

        $keymap = $this->keymapIsString(Arr::get($attributes, 'keymap', []) ?? []);

        if (! \is_array($keymap) || empty($keymap)) {
            return $function;
        } else {
            $keymap = collect($keymap)->map(function ($id, $key) use ($type) {
                if (! is_int($key) && strtolower($key) === strtolower($type)) {
                    return '{'.$id.'}';
                }

                return null;
            })->implode('/');

            return $function.'/'.$keymap;
        }
    }

    /**
     * Check if keymap is string.
     *
     * @param $keymap
     * @return array
     */
    private function keymapIsString($keymap): array
    {
        if (\is_string($keymap)) {
            return (Str::length($keymap) > 0) ? Arr::wrap($keymap) : [];
        }

        return $keymap ?? [];
    }

    /**
     * Append ID parameter.
     *
     * @param  array  $keymap
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
     * @param  string  $type
     * @param  string  $controller
     * @param  string  $function
     * @return string
     */
    public function classConvention(string $type, string $controller, string $function): string
    {
        return Str::ucfirst($controller).'Controller@'.Str::camel(Str::lower($type).'_'.$function);
    }

    /**
     * Router name naming convention.
     *
     * @param  string  $type
     * @param  string  $controller
     * @param  string  $function
     * @param  array  $attributes
     * @return string
     */
    public function namedConvention(string $type, string $controller, string $function, array $attributes): string
    {
        $named = Arr::get($attributes, 'named', null);

        if ($named === '' || $named === null) {
            $named = Str::lower($controller.'.'.$function);
        }

        if (\is_string($named)) {
            return Str::lower($type.'.'.$named);
        }
        $type = Str::lower($type);

        $names = array_change_key_case($named, \CASE_LOWER);

        if (collect($names)->has($type)) {
            return Str::lower(collect($names)->get($type, \null));
        } else {
            return Str::lower($type.'.'.$controller.'.'.$function);
        }
    }
}
