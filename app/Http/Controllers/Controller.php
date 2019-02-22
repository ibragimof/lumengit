<?php

namespace App\Http\Controllers;

use Faker\Provider\DateTime;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function show(){
        return date('m/d/Y h:i:s a', time());
    }
}
