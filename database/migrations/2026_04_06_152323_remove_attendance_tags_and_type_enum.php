<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveAttendanceTagsAndTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Fetch all events that have ATTENDANCE tags
        $attendanceTags = DB::table('tags')->where('type', 'ATTENDANCE')->get()->keyBy('id_tag');
        
        $eventTags = DB::table('event_tags')
            ->whereIn('tag_id', $attendanceTags->keys())
            ->get()
            ->groupBy('event_id');

        foreach ($eventTags as $eventId => $tags) {
            $event = DB::table('events')->where('id_event', $eventId)->first();
            
            if (!$event) continue;

            // Don't overwrite if form_schema already exists and is not null
            if (!empty($event->form_schema)) {
                continue;
            }

            // Build form_schema for legacy tags
            $values = [];
            foreach ($tags as $eventTag) {
                $tagId = $eventTag->tag_id;
                if ($attendanceTags->has($tagId)) {
                    $tagName = $attendanceTags->get($tagId)->name;
                    $values[] = [
                        'label' => $tagName,
                        'value' => $tagName,
                    ];
                }
            }

            if (empty($values)) continue;

            $formSchema = [
                [
                    'type' => 'checkbox-group',
                    'required' => false,
                    'label' => 'Opcions (antigues)',
                    'name' => 'opcions_legacy',
                    'values' => $values,
                ]
            ];

            // Update event
            DB::table('events')
                ->where('id_event', $eventId)
                ->update(['form_schema' => json_encode($formSchema)]);

            // Migrate attendance records for this event
            $attendances = DB::table('attendance')
                ->where('event_id', $eventId)
                ->whereNotNull('options')
                ->where('options', '!=', '[]')
                ->get();

            foreach ($attendances as $attendance) {
                $optionsIds = json_decode($attendance->options, true);
                if (is_array($optionsIds) && (count($optionsIds) > 0 && is_numeric(array_keys($optionsIds)[0]))) {
                    $selectedValues = [];
                    foreach ($optionsIds as $optId) {
                        if ($attendanceTags->has($optId)) {
                            $selectedValues[] = $attendanceTags->get($optId)->name;
                        }
                    }

                    if (!empty($selectedValues)) {
                        $newOptions = [
                            'opcions_legacy' => $selectedValues
                        ];
                        DB::table('attendance')
                            ->where('id_attendance', $attendance->id_attendance)
                            ->update(['options' => json_encode($newOptions)]);
                    }
                }
            }
        }

        // 2. Delete all tags of type ATTENDANCE. 'event_tags' entries will ON DELETE CASCADE or can be deleted manually.
        DB::table('tags')->where('type', 'ATTENDANCE')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // One way operation
    }
}
