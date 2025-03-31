<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Container;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Controllers\Controller;

class ContainerController extends Controller
{
   
    // function test_container()
    // {
    //     Container::bind('foo', function () {
    //         return new Foo();
    //     });

    //     $foo = Container::make('foo');
    //     print_r($foo->bar());

    //     $foo = Container::make('foo');
    //     print_r($foo->bar());
    // }

    function test_container2()
    {
        Container::bind('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container3()
    {
        Container::singleton('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container4()
    {
        Container::bind('car', \Boctulus\Simplerest\Libs\Car::class);

        $o = Container::makeWith('car', ['color' => 'blue', 'max_speed' => 200]);
        print_r($o->run());
        print_r($o);
    }

}

