<?php
    require __DIR__ . '/../vendor/autoload.php';

    use Nibun\CmsProject\ExampleClass;

    $example = new ExampleClass();
    echo $example->sayHello();
?>