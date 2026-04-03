<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class StructureHelper
{
    public static function createDirectories(string $shortname)
    {
        $safeShortname = preg_replace('/[^A-Za-z0-9_\-]/', '', $shortname);

        if (empty($safeShortname)) {
            return;
        }

        $pathCollesGeneral = public_path('media/colles/'.$safeShortname);
        $pathCollesCastellers = public_path('media/colles/'.$safeShortname.'/castellers');
        $pathCollesSVG = public_path('media/colles/'.$safeShortname.'/svg');

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
