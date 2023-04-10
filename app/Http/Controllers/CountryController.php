<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends BaseController
{
    public function countries()
    {

        $data = [];

        $countries = Country::get();

        return $this->sendResponse($countries, trans('messages.success'));
    }
}
