<?php

namespace App\Http\Controllers;

use App\Models\Transporteur;
use Illuminate\Http\Request;

class TransporteurController extends Controller
{
    public function index()
    {
        $options = Transporteur::all();
        return response()->json(["data" => $options]);
    }
}
