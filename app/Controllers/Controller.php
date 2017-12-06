<?php 

namespace App\Controllers;

class Controller
{
    protected $container;

    //you can use typehint Container also, not yet at the moment
    public function __construct( $container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        //check if exist or not
        //ex : view passes to this class, 
        // check is view available or not
        // if so, container->view
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

}