<?php

namespace App\Rules;

use App\Colla;
use App\Enums\TypeTags;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class tags_exist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $tags = preg_split('/,|\./', Str::of($value));

        // Optimize by querying all tags at once
        $existingTags = DB::table('tags')
            ->whereIn('id_tag', $tags)
            ->where('type', TypeTags::CASTELLERS)
            ->where('colla_id', Colla::getCurrent()->getId())
            ->pluck('id_tag')
            ->toArray();

        // Check if all provided tags exist
        return empty(array_diff($tags, $existingTags));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'One or more tags do not exist.';
    }
}
