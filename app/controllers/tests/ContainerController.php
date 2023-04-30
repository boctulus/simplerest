<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\Container;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class ContainerController extends MyController
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
        Container::bind('car', \simplerest\libs\Car::class);

        $o = Container::makeWith('car', ['color' => 'blue', 'max_speed' => 200]);
        print_r($o->run());
        print_r($o);
    }

}

