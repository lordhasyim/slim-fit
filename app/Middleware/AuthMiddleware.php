<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        // check ifuser is not signed in
        //flash
        // redirect
        if (!$this->container->auth->check()) {
            $this->container->flash->addMessage('error', 'please sign in before doing that.');
            return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

        $response = $next($request, $response);
        return $response;
    }

}