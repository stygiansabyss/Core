<?php namespace NukaCode\Core\Exceptions;


class InvalidSrc extends \Exception {

    public function __construct($src)
    {
        parent::__construct('')
    }
}