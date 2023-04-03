<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function countries()
    {

        $data = [];
        $countries = Country::get();

        $data['countries'] = $countries;

        return sendResponse($data, trans('messages.success'));
    }
}
