<?php

namespace App\Http\Controller;

use Illuminate\Facade\Config;
use Illuminate\Facade\Request;
use Illuminate\Facade\Response;

class PageController
{
    public function index(Request $request, Response $response)
    {
        return $response->html('
            <form action="http://localhost/ukk/csrf" method="POST">
                ' . csrf() . '
                <input type="text" name="email" placeholder="example@gmail.com" />
                <button type="submit">SUBMIT</button>    
            </form>
        ');
    }

    public function store(Request $request, Response $response)
    {
        return $response->json($request->input(null));
    }
}
