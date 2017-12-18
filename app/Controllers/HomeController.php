<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use App\Models\User;
class HomeController extends Controller
{

    public function index($request, $response)
    {

        
        // view passes to the Controller class
        return $this->view->render($response, 'home.twig');
    }

}