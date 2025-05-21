<?php

namespace App;

use App\Enums\TypeTags;
use App\Helpers\DateHelper;
use App\Traits\FilterableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Notification represents the intention to Notify a group of Castellers about something.
 *
 * One notification will start generating notification_orders as soon it's notification date has passed
 * A notification will generate the notification_orders when the NotificationReady event is generated.
 */
class ScheduledNotification extends Model
{
    use FilterableTrait;

    protected $table = 'scheduled_notifications';

    protected $primaryKey = 'id_scheduled_notification';

    protected $fillable = ['title, body'];

    protected static $filterClass = \App\Services\Filters\ScheduledNotificationsFilter::class;

    /** Get notificationID */
    public function getId(): int
    {
        return $this->getAttribute('id_scheduled_notification');
    }

    /** Get the colla of the notification */
    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    /** Get the type of the Notification (NotificationTypeEnum) */
    public function getType(): int
    {
        return $this->getAttribute('type');
    }

    /** Set the type of the Notification (NotificationTypeEnum) */
    public function setType(int $type)
    {
        $this->setAttribute('type', $type);
    }

    /** Get the Notification title*/
    public function getTitle(): string
    {
        return $this->getAttribute('title');
    }

    /** Set the Notification title*/
    public function setTitle(string $title)
    {
        $this->setAttribute('title', $title);
    }

    /** Get the Notification body (content)*/
    public function getBody(): string
    {
        return $this->getAttribute('body');
    }

    /** Set the Notification body (content)*/
    public function setBody(string $body)
    {
        $this->setAttribute('body', $body);
    }

    /** Get the filter search type (OR or AND)*/
    public function getFilterSearchType(): string
    {
        return $this->getAttribute('filter_search_type');
    }

    /** Set the filter search type (OR or AND)*/
    public function setFilterSearchType(string $filterSearchType)
    {
        $this->setAttribute('filter_search_type', $filterSearchType);
    }

    /** Get the Notification date */
    public function getNotificationDate(): string
    {
        return DateHelper::dateTimeToCurrentTimezone($this->getAttribute('notification_date'));
    }

    /** Get the creation time date */
    public function getCreatedAt(): string
    {
        return DateHelper::dateTimeToCurrentTimezone($this->getAttribute('created_at'));
    }

    /** Set the Notification date */
    public function setNotificationDate(Carbon $notificationDate)
    {
        $this->setAttribute('notification_date', $notificationDate);
    }

    /** Get the userID who created the Notification */
    public function getUserId(): int
    {
        return $this->getAttribute('user_id');
    }

    /** Set the userID who created the Notification */
    public function setUserId(int $userId)
    {
        $this->setAttribute('user_id', $userId);
    }

    /** Get the CastellerTags used to filter who will receive the Notification */
    public function getTags(): Collection
    {
        return $this->tags()->where('type', TypeTags::Castellers()->value())->get();
    }

    public function tags(): ?BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'scheduled_notification_tag', 'scheduled_notification_id', 'tag_id');
    }

    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id_notification');
    }

    /** get Array tags (only name or value) to Event*/
    // TODO: Move this logic to tagsService
    public function tagsArray(string $return_type = 'name'): array
    {
        return $this->getTags()->pluck($return_type)->toArray();
    }

    /** Return true if the notification date is from the past */
    public function isPastNotificationDate(): bool
    {
        if (Carbon::now()->greaterThan($this->getNotificationDate())) {
            return true;
        }

        return false;
    }

    public function hasBeenNotified(): bool
    {
        return $this->notification()->count() > 0;
    }

    public function getNotification(): ?Notification
    {
        if (! $this->notification()->count() > 0) {
            return null;
        }

        return $this->notification()->get()->first();
    }
}
