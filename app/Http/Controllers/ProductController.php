<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function index(){

        $products = Product::all();

        return $this->sendResponse($products);
    
    }
}
