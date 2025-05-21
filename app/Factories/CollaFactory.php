<?php

declare(strict_types=1);

namespace App\Factories;

use App\Colla;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

class CollaFactory
{
    public static function make(ParameterBag $bag): Colla
    {

        self::createDirectories($bag->get('shortname'));

        $colla = new Colla();
        $colla = self::update($colla, $bag);

        return $colla;

    }

    public static function update(Colla $colla, ParameterBag $bag): Colla
    {

        if ($bag->has('id_colla_external')) {
            $colla->setAttribute('id_colla_external', $bag->getInt('id_colla_external'));
        }

        if ($bag->has('name')) {
            $colla->setAttribute('name', $bag->get('name'));
        }

        if ($bag->has('shortname')) {
            $colla->setAttribute('shortname', $bag->get('shortname'));
        }

        if ($bag->has('email')) {
            $colla->setAttribute('email', $bag->get('email'));
        }

        if ($bag->has('phone')) {
            $colla->setAttribute('phone', $bag->get('phone'));
        }

        if ($bag->has('country')) {
            $colla->setAttribute('country', $bag->get('country'));
        }

        if ($bag->has('city')) {
            $colla->setAttribute('city', $bag->get('city'));
        }

        if ($bag->has('max_members')) {
            $colla->setAttribute('max_members', $bag->get('max_members'));
        }

        if ($bag->has('color')) {
            $colla->setAttribute('color', $bag->get('color'));
        }

        if ($bag->has('logo')) {
            if ($logo = $bag->get('logo')) {
                $colla->setAttribute('logo', self::saveLogo($colla, $logo));
            }
        }

        if ($bag->has('banner')) {
            if ($banner = $bag->get('banner')) {
                $colla->setAttribute('banner', self::saveBanner($colla, $banner));
            }
        }

        if ($bag->has('banner_notification_message_submitted')) {
            $message = $bag->get('banner_notification_message');
            $colla->setAttribute('banner_notification_message', $message === '' ? null : $message);
        }

        return $colla;
    }

    private static function createDirectories(string $shortname)
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

    private static function saveLogo(Colla $colla, UploadedFile $file): string
    {

        if ($colla->getLogo()) {
            $filePath = public_path('media/colles/'.$colla->getShortName().'/'.$colla->getLogo());
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $image_input = $file;

        $logo = Image::make($image_input);
        $file_name = Str::random(10).'.png';
        $logo->fit(64);
        $logo->encode('png');
        $logo->save(public_path('media/colles/'.$colla->getShortName().'/'.$file_name));

        return $file_name;

    }

    private static function saveBanner(Colla $colla, UploadedFile $file): string
    {

        if ($colla->getBanner()) {
            $filePath = public_path('media/colles/'.$colla->getShortName().'/'.$colla->getBanner());
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $image_input = $file;

        $logo = Image::make($image_input);
        $file_name = Str::random(10).'.png';
        $logo->resize(null, 70, function ($constraint) {
            $constraint->aspectRatio();
        });
        $logo->encode('png');
        $logo->save(public_path('media/colles/'.$colla->getShortName().'/'.$file_name));

        return $file_name;

    }
}
