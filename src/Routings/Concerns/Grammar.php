<?php
namespace Joesama\Pintu\Routings\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Grammar
{
    /**
     * Path naming convention definition.
     *
     * @param string $function
     * @param array $attributes
     *
     * @return string
     */
    protected function pathConvention(string $type, string $function, array $attributes): string
    {
        $keymap = Arr::get($attributes, 'keymap');
    
        if (\is_string($keymap)) {

            if (Str::length($keymap) > 0) {
                $keymap = [$keymap];
            } else {
                $keymap = [];
            }
        }

        if (Str::lower($type) == 'head') {
            if (!in_array('id', $keymap)) {
                dump(collect($keymap));
                try {
                    $keymap = Arr::prepend($keymap, 'id');
                } catch (\Exception $th) {
                    dd($th->getMessage());
                }
                
                dump(collect($keymap));
            }
        }

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
     * Class naming convention definition.
     *
     * @param string $type
     * @param string $controller
     * @param string $function
     *
     * @return string
     */
    protected function classConvention(string $type, string $controller, string $function): string
    {
        return Str::ucfirst($controller) . 'Controller@' . Str::camel(Str::lower($type) .'_'. $function);
    }

    /**
     * Router name naming convention.
     *
     * @param string $controller
     * @param string $function
     * @param array $attributes
     *
     * @return string
     */
    protected function namedConvention(string $controller, string $function, array $attributes): string
    {
        $named = Arr::get($attributes, 'named', null);

        if ($named === '' || $named === null) {
            $named = $controller.'.'.$function;
        };

        return Str::lower($named);
    }
}