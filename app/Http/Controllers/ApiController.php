<?php

namespace App\Http\Controllers;

use App\Models\RefDiklat;
use App\Models\RefStruktural;

class ApiController extends Controller
{
    public function getDiklat()
    {
        return response()->json(RefDiklat::select('id', 'jenis_diklat', 'jenis_kursus_sertipikat')->get());
    }

    public function getStruktural()
    {
        return response()->json(RefStruktural::select('id', 'nama')->get());
    }
}
