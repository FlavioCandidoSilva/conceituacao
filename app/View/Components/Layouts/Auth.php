<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;

class Auth extends Component
{
    public string $title;

    public function __construct(string $title = 'Autenticação')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('layouts.auth');
    }
}
