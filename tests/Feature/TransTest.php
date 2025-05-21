<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class TransTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItHasAllTranslations()
    {
        $translation_files = [];
        foreach (glob('./resources/lang/ca/*') as $file) {
            //$this->assertTrue(false, $file);
            if (is_file($file)) {
                $translation_files[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        foreach ($translation_files as $translation_file) {
            foreach (trans($translation_file) as $key => $value) {
                $this->assertTrue(
                    Lang::hasForLocale($translation_file.'.'.$key, 'en'),
                    'English translation '.$translation_file.'.'.$key.' not found'
                );
                $this->assertTrue(
                    Lang::hasForLocale($translation_file.'.'.$key, 'fr'),
                    'French translation '.$translation_file.'.'.$key.' not found'
                );
                $this->assertTrue(
                    Lang::hasForLocale($translation_file.'.'.$key, 'es'),
                    'Spanish translation '.$translation_file.'.'.$key.' not found'
                );
            }
        }
    }
}
