<?php
function sayHello() {
    echo "Hello!";
}

function sayBye(string $to) {
    echo "Hello $to";
}

function tb_prefix() {
    return "tb_";
}

class MyClass {
    public function doSomething() {
        echo "Doing something...";
    }

    public function doSomethingElse() {
        echo "Doing something...";
    }
}