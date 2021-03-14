<?php

namespace App\Http\Controller;

class PageController
{
    public function show(\Illuminate\Core\Request $request, $id = null)
    {
        echo 'show page';
    }

    public function update(\Illuminate\Core\Request $request)
    {
        echo 'update page';
    }
}
