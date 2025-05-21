<?php

namespace App;

use App\Enums\NotificationTypeEnum;
use App\Helpers\DateHelper;
use App\Traits\FilterableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Notification represents the intention to Notify a group of Castellers about something.
 *
 * One notification will start generating notification_orders as soon it's notification date has passed
 * A notification will generate the notification_orders when the NotificationReady event is generated.
 */
class Notification extends Model
{
    use FilterableTrait;

    protected $table = 'notifications';

    protected $primaryKey = 'id_notification';

    protected $fillable = ['title', 'data', 'template'];

    protected static $filterClass = \App\Services\Filters\NotificationsFilter::class;

    /** Get notificationID */
    public function getId(): int
    {
        return $this->getAttribute('id_notification');
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

    public function getTypeStr(): string
    {
        return NotificationTypeEnum::getById($this->getType());
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

    /** Get the Notification data (content)*/
    public function getData(): string
    {
        return $this->getAttribute('data');
    }

    /** Set the Notification data (content)*/
    public function setData(string $data)
    {
        $this->setAttribute('data', $data);
    }

    /** Get the Notification template (content)*/
    public function getTemplate(): string
    {
        return $this->getAttribute('template');
    }

    /** Set the Notification template (content)*/
    public function setTemplate(string $template)
    {
        $this->setAttribute('template', $template);
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

    /** Get the userID who created the Notification */
    public function getUserId(): ?int
    {
        return $this->getAttribute('user_id');
    }

    /** Set the userID who created the Notification */
    public function setUserId(int $userId)
    {
        $this->setAttribute('user_id', $userId);
    }

    /** Get the castellerID who created the Notification */
    public function getCastellerId(): ?int
    {
        return $this->getAttribute('casteller_id');
    }

    /** Set the castellerID who created the Notification */
    public function setCastellerId(int $castellerId)
    {
        $this->setAttribute('casteller_id', $castellerId);
    }

    /** Get if Notification is visible for the user */
    public function getNotificationVisible(): bool
    {
        return $this->getAttribute('visible');
    }

    /** Set if Notification is visible for the user */
    public function setNotificationVisible(bool $visible)
    {
        $this->setAttribute('visible', $visible);
    }

    /** Get the creation time date */
    public function getCreatedAt(): string
    {
        return DateHelper::dateTimeToCurrentTimezone($this->getAttribute('created_at'));
    }

    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function user(): ?BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function casteller(): ?BelongsTo
    {
        return $this->belongsTo(Casteller::class, 'casteller_id', 'id_casteller');
    }

    public function orders(): ?HasMany
    {
        return $this->hasMany(NotificationOrder::class, 'notification_id', 'id_notification');
    }

    public function events(): ?BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_notification', 'notification_id', 'event_id');
    }

    public function render(?Casteller $casteller = null, string $template_type = 'mail'): string
    {
        return view('notifications.templates.'.$this->getTemplate().'.'.$template_type, array_merge([
            'casteller' => $casteller,
            'notification' => $this,
        ], unserialize($this->getData())))->render();
    }

    public function getNotificationPreview(): string
    {
        return view('notifications.templates.'.$this->getTemplate().'.mail', array_merge([
            'notification' => $this,
            'casteller' => null,
        ], unserialize($this->getData())))->render();
    }
}
