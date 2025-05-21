<?php

namespace Tests\Unit\Services;

use App\Colla;
use App\Enums\NotificationTypeEnum;
use App\Event;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_send_message_notification()
    {
        $colla = Colla::factory()->create();
        $title = 'Test Message';
        $message = 'This is a test message';
        $notification = NotificationService::SendMessage($colla, $title, $message);

        $this->assertEquals($notification->title, $title);
        $this->assertEquals($notification->type, NotificationTypeEnum::MESSAGE);
    }

    /** @test */
    public function it_can_send_attendance_reminder_notification()
    {
        // Create necessary mocks or actual objects for dependencies if required
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];
        $event = Event::factory()->state($set_colla)->future()->create();
        $notification = NotificationService::SendAttendanceReminder($event);

        $this->assertEquals($notification->type, NotificationTypeEnum::REMINDER);
    }
}
