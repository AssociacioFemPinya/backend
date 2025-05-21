<?php

declare(strict_types=1);

namespace App\Factories;

use App\Casteller;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

class CastellerFactory
{
    public static function make(int $collaId, ParameterBag $bag): Casteller
    {
        $casteller = new Casteller();

        $casteller->setAttribute('colla_id', $collaId);

        $casteller->setAttribute('alias', $bag->get('alias'));

        $casteller->setAttribute('status', $bag->getInt('status'));

        $colla = $casteller->getColla();

        $casteller->setAttribute('language', $colla->getConfig()->getLanguage());

        // we need to save the empty casteller at the beginning to be able to set a tag on it
        $casteller->save();

        return self::update($casteller, $bag);
    }

    public static function update(Casteller $casteller, ParameterBag $bag): Casteller
    {

        if ($bag->has('alias')) {
            if ($alias = $bag->get('alias')) {
                $casteller->setAttribute('alias', $alias);
            }
        }

        if ($bag->has('status')) {
            if ($status = $bag->getInt('status')) {
                $casteller->setAttribute('status', $status);
            }
        }

        if ($bag->has('id_casteller_external')) {
            $casteller->setAttribute('id_casteller_external', $bag->getInt('id_casteller_external'));
        }

        if ($bag->has('num_soci')) {
            $casteller->setAttribute('num_soci', $bag->get('num_soci'));
        }

        if ($bag->has('nationality')) {
            $casteller->setAttribute('nationality', $bag->get('nationality'));
        }

        if ($bag->has('national_id_number')) {
            $casteller->setAttribute('national_id_number', $bag->get('national_id_number'));
        }

        if ($bag->has('national_id_type')) {
            $casteller->setAttribute('national_id_type', $bag->getAlpha('national_id_type'));
        }

        if ($bag->has('name')) {
            $casteller->setAttribute('name', $bag->get('name'));
        }

        if ($bag->has('last_name')) {
            $casteller->setAttribute('last_name', $bag->get('last_name'));
        }

        if ($bag->has('gender')) {
            $casteller->setAttribute('gender', $bag->getInt('gender'));
        }

        if ($bag->has('birthdate') && $bag->get('birthdate') != null) {
            $birthdate = Carbon::createFromFormat('d/m/Y', $bag->get('birthdate'));
            $casteller->setAttribute('birthdate', $birthdate);
        }

        if ($bag->has('subscription_date') && $bag->get('subscription_date') != null) {
            $subscription_date = Carbon::createFromFormat('d/m/Y', $bag->get('subscription_date'));
            $casteller->setAttribute('subscription_date', $subscription_date);
        }

        if ($bag->has('email')) {
            $casteller->setAttribute('email', $bag->get('email'));
        }

        if ($bag->has('email2')) {
            $casteller->setAttribute('email2', $bag->get('email2'));
        }

        if ($bag->has('phone')) {
            $casteller->setAttribute('phone', $bag->get('phone'));
        }

        if ($bag->has('mobile_phone')) {
            $casteller->setAttribute('mobile_phone', $bag->get('mobile_phone'));
        }

        if ($bag->has('emergency_phone')) {
            $casteller->setAttribute('emergency_phone', $bag->get('emergency_phone'));
        }

        if ($bag->has('address')) {
            $casteller->setAttribute('address', $bag->get('address'));
        }

        if ($bag->has('postal_code')) {
            $casteller->setAttribute('postal_code', $bag->get('postal_code'));
        }

        if ($bag->has('city')) {
            $casteller->setAttribute('city', $bag->get('city'));
        }

        if ($bag->has('comarca')) {
            $casteller->setAttribute('comarca', $bag->get('comarca'));
        }

        if ($bag->has('province')) {
            $casteller->setAttribute('province', $bag->get('province'));
        }

        if ($bag->has('country')) {
            $casteller->setAttribute('country', $bag->get('country'));
        }

        if ($bag->has('comments')) {
            $casteller->setAttribute('comments', $bag->get('comments'));
        }

        if ($bag->has('height')) {
            $casteller->setAttribute('height', $bag->get('height'));
            // $casteller->setAttribute('height', self::relativeToAbsoluteHeight($casteller, $bag->getInt('height'), $casteller->getColla()->getConfig()->getHeightBaseline()));
        }

        if ($bag->has('weight')) {
            $casteller->setAttribute('weight', $bag->get('weight'));
        }

        if ($bag->has('shoulder_height')) {
            $casteller->setAttribute('shoulder_height', $bag->get('shoulder_height'));
            // $casteller->setAttribute('shoulder_height', self::relativeToAbsoluteHeight($casteller, $bag->getInt('shoulder_height'), $casteller->getColla()->getConfig()->getShoulderHeightBaseline()));

        }

        if ($bag->has('photo')) {
            if ($photo = $bag->get('photo')) {
                $casteller->setAttribute('photo', self::savePhoto($casteller, $photo));
            }
        }

        if ($bag->has('tags')) {
            self::addOrUpdateTags($casteller, $bag->get('tags'));
        } else {
            self::addOrUpdateTags($casteller, []);
        }

        if ($bag->has('position')) {

            self::addOrUpdatePosition($casteller, $bag->getInt('position'));
        }

        if ($bag->has('language')) {
            $casteller->setAttribute('language', $bag->get('language'));
        }

        return $casteller;
    }

    private static function addOrUpdateTags(Casteller $casteller, ?array $tags)
    {

        if ($casteller->hasTags()) {
            self::removeTagsFromCasteller($casteller);
        }

        if (! empty($tags)) {
            foreach ($tags as $tag) {
                self::addTagToCasteller($casteller, (int) $tag);
            }
        }
    }

    private static function addOrUpdatePosition(Casteller $casteller, ?int $positionId)
    {
        /** @var Tag $position */
        if (! is_null($positionId)) {
            $position = Tag::query()->find($positionId);
        } else {
            $position = null;
        }

        if ($casteller->getPosition()) {
            $casteller->removeTag($casteller->getPosition());
        }

        if ($position) {
            self::addPositionToCasteller($casteller, $position->getId());
        }
    }

    private static function removeTagsFromCasteller(Casteller $casteller)
    {
        foreach ($casteller->getTags() as $tag) {
            $casteller->removeTag($tag);
        }
    }

    private static function addTagToCasteller(Casteller $casteller, int $tagId): void
    {
        /** @var Tag $tag */
        $tag = Tag::query()->find($tagId);

        $casteller->tags()->save($tag);

    }

    private static function addPositionToCasteller(Casteller $casteller, int $tagId): void
    {
        /** @var Tag $tag */
        $tag = Tag::query()->find($tagId);

        $existingTag = $casteller->getTags()->find($tag->getId());
        if (! $existingTag) {
            $casteller->tags()->save($tag);
        }
    }

    private static function savePhoto(Casteller $casteller, UploadedFile $file)
    {
        $imageSizes = [
            'xs' => 32,
            'med' => 128,
            'xl' => 1024,
        ];

        $colla = $casteller->getColla();

        if ($casteller->getPhoto()) {

            $path = public_path('media/colles/'.$colla->getShortname().'/castellers').'/'.$casteller->getPhoto();

            foreach ($imageSizes as $size => $width) {
                $filePath = $path.'-'.$size.'.png';
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $random_str = Str::random(10);
        $image_input = $file;
        $imagePath = public_path('media/colles/'.$colla->getShortname().'/castellers/').$casteller->getId().'_'.$random_str;

        foreach ($imageSizes as $size => $width) {
            $image = Image::make($image_input);
            $image->fit($width);
            $image->encode('png');
            $image->save($imagePath.'-'.$size.'.png');
        }

        return $casteller->getId().'_'.$random_str;

    }

    private static function relativeToAbsoluteHeight(Casteller $casteller, int $relative_height, int $baseline)
    {
        $height = $relative_height + $baseline;

        return $height;
    }
}
