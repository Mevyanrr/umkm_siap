<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class MarketWebController extends Controller
{
    public function index()
{
    return view('market.market_intelligent');
}
}
