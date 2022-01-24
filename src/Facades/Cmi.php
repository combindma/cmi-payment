<?php

namespace Combindma\Cmi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\Cmi\Cmi
 */
class Cmi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Combindma\Cmi\Cmi::class;
    }
}
