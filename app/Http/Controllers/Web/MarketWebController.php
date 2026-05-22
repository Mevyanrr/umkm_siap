<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;


class MarketWebController extends Controller
{
    public function index()
{
    return view('market.market_intelligent');
}
}
