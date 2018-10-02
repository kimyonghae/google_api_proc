<?php
namespace GoogleApiProc\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'credentials';
    }
}