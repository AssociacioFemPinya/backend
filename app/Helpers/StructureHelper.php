<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class StructureHelper
{
    public static function createDirectories(string $shortname)
    {

        $pathCollesGeneral = public_path('media/colles/'.$shortname);
        $pathCollesCastellers = public_path('media/colles/'.$shortname.'/castellers');
        $pathCollesSVG = public_path('media/colles/'.$shortname.'/svg');

        if (! File::isDirectory($pathCollesGeneral)) {
            File::makeDirectory($pathCollesGeneral, 0755, true, true);
        }
        if (! File::isDirectory($pathCollesCastellers)) {
            File::makeDirectory($pathCollesCastellers, 0755, true, true);
        }
        if (! File::isDirectory($pathCollesSVG)) {
            File::makeDirectory($pathCollesSVG, 0755, true, true);
        }

    }
}
