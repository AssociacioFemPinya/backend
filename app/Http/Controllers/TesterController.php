<?php

namespace App\Http\Controllers;

use App\Casteller;
use App\Colla;
use App\Imports\CastellersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\ParameterBag;

class TesterController extends Controller
{
    public function postImport(Request $request)
    {
        $colla = Colla::getCurrent();
        $file = $request->file('import');

        $array = Excel::toArray(new CastellersImport, $file);

        foreach ($array as $row) {
            $attributs = new ParameterBag($row);
            Casteller::newCasteller($attributs, $colla);
        }

        dd($array);
    }
}
