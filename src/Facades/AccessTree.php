<?php

namespace Obrainwave\AccessTree\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Obrainwave\AccessTree\Skeleton
 */
class AccessTree extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Obrainwave\AccessTree\AccessTree::class;
    }
}
