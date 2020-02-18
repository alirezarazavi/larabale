<?php

namespace AlirezaRazavi\LaraBale\Facades;

use AlirezaRazavi\LaraBale\LaraBale;
use Illuminate\Support\Facades\Facade;

/**
 * Class Telegram.
 */
class Bale extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaraBale::class;
    }
}
