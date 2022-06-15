<?php

namespace App\Http\Presenters;

use Illuminate\Http\Request;

interface PresenterInterface
{
    public function render($query);
    public function renderCollection($query);
}