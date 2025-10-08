<?php

namespace Boctulus\CliTest\Controllers;

class AdminController
{
    public function users()
    {
        return 'AdminController@users - List all users';
    }

    public function cache()
    {
        return 'AdminController@cache - Clear cache';
    }
}
