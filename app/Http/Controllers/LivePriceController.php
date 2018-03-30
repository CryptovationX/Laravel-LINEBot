<?php

namespace App\Http\Controllers;

use Line;

use Illuminate\Http\Request;

class LivePriceController extends Controller
{
    public function test()
    {
        Line::pushMessage('Ua2b3dd43fdfaf129015087ee98896a5a','hello i fai');
    }
}
