<?php

namespace App;

use App\Enums\TypeTags;
use App\Managers\EventsManager;
use App\Repositories\EventRepository;
use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\ParameterBag;

final class Event extends Model
{
    use FilterableTrait;
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'events';

    protected $primaryKey = 'id_event';

    public $timestamps = true;

    protected static $filterClass = \App\Services\Filters\EventsFilter::class;

    protected $dates = [
        'start_date',
        'open_date',
        'close_date',
    ];

    protected $casts = [
        'companions' => 'boolean',
        'visibility' => 'boolean',
    ];

    const TYPES = [
        '1' => 'assaig',
        '2' => 'actuacio',
        '3' => 'activitat',
    ];

    /** get a Event of open and close data  */
    public function isOpen($date = null): bool
    {
        if (is_null($date)) {
            $date = Carbon::now();
        }

        if ($this->getOpenDate() < $date && $this->getCloseDate() > $date) {

            return true;
        }

        return false;
    }

    /** get Array tags (only name or value) to Event*/
    public function tagsArray(string $return_type = 'name'): array
    {
        return $this->tags->pluck($return_type)->toArray();
    }

    public function castellerTagsArray(string $return_type = 'name'): array
    {
        return $this->getCastellerTags()->pluck($return_type)->toArray();
    }

    /** remove tag form Event
     */
    public function removeTag(Tag $tag): bool
    {
        if ($this->hasTag($tag->getValue()) && $tag->getCollaId() === $this->getCollaId()) {
            if (DB::table('event_tag')->where('event_id', $this->getId())->where('tag_id', $tag->getId())->delete()) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function removeCastellerTag(Tag $tag): bool
    {
        if ($this->hasCastellerTag($tag->getValue()) && $tag->getCollaId() === $this->getCollaId()) {
            if (DB::table('event_casteller_tag')->where('event_id', $this->getId())->where('tag_id', $tag->getId())->delete()) {
                return true;
            }

            return false;
        }

        return false;
    }

    /** return true if Event has a tag*/
    public function hasTag(string $tag_value): bool
    {
        return in_array($tag_value, $this->tagsArray('value'));
    }

    public function hasTags(): bool
    {
        return $this->getTags()->isNotEmpty();
    }

    public function hasCastellerTag(string $tag_value): bool
    {
        return in_array($tag_value, $this->castellerTagsArray('value'));
    }

    public function hasCastellerTags(): bool
    {
        return $this->getCastellerTags()->isNotEmpty();
    }

    /** add personalise attendance answer to Event*/
    public function addAttendanceAnswer(Tag $tag): bool
    {
        if ($tag->getTypeName() === TypeTags::ATTENDANCE && $tag->getCollaId() === $this->getCollaId() && ! $this->hasAttendanceAnswer($tag->getValue())) {
            DB::table('event_attendance_tag')->insert(['event_id' => $this->getId(), 'tag_id' => $tag->getId()]);

            return true;
        }

        return false;
    }

    /** remove attendance personalize answer*/
    public function removeAttendanceAnswer(Tag $tag): bool
    {
        if ($this->hasAttendanceAnswer($tag->getValue()) && $tag->getCollaId() === $this->getCollaId()) {
            if (DB::table('event_attendance_tag')
                ->where('event_id', $this->getId())
                ->where('tag_id', $tag->getId())
                ->delete()
            ) {

                return true;
            }

            return false;
        }

        return false;
    }

    /** return is Event has an attendance personalize answer*/
    public function hasAttendanceAnswer($tag_value): bool
    {
        if (in_array($tag_value, $this->answersArray('VALUE'))) {

            return true;
        }

        return false;
    }

    public function hasAttendanceAnswers(): bool
    {
        return $this->getAttendanceAnswers()->isNotEmpty();
    }

    /** get Array personalise answers (only name or value) to Event*/
    public function answersArray(string $return_type = 'NAME'): array
    {
        $array = [];

        foreach ($this->getAttendanceAnswers() as $answer) {
            if ($return_type === 'NAME') {

                $array[] = $answer->getName();
            } elseif ($return_type === 'VALUE') {

                $array[] = $answer->getValue();
            } elseif ($return_type === 'ID') {

                $array[] = $answer->getId();
            }
        }

        return $array;
    }

    /** get a count of confirmed attendance to event */
    public function countAttenders(): array
    {
        $attendance = [];

        $attendance['ok'] = $this->attendances
            ->where('status', Attendance::getStatusId('ok'))
            ->count();

        $attendance['nok'] = $this->attendances
            ->where('status', Attendance::getStatusId('nok'))
            ->count();

        $attendance['unknown'] = $this->attendances
            ->filter(function ($item) {
                $status = $item->status;

                return $status === Attendance::getStatusId('unknown') || $status === null;
            })
            ->count();

        $attendance['companions'] = $this->attendances
            ->where('status', Attendance::getStatusId('ok'))
            ->sum('companions');

        $attendance['verified_ok'] = $this->attendances
            ->where('status_verified', Attendance::getStatusId('ok'))
            ->count();

        return $attendance;
    }

    public static function getTypes(): array
    {
        $collaConfig = Colla::getCurrent()->getConfig();
        $return[1] = ($collaConfig->getTranslationAssaig()) ?: __('config.translation_'.self::TYPES[1]);
        $return[2] = ($collaConfig->getTranslationActuacio()) ?: __('config.translation_'.self::TYPES[2]);
        $return[3] = ($collaConfig->getTranslationActivitat()) ?: __('config.translation_'.self::TYPES[3]);

        return $return;
    }

    public static function getTypeId(string $type): int
    {
        return array_search($type, self::TYPES);
    }

    public function hasAttachedBoards(): bool
    {
        return $this->boardsEvent()->count() > 0;
    }

    public function hasRondes(): bool
    {
        return $this->rondes()->count() > 0;
    }

    public function getLastRonda(): ?Ronda
    {
        return $this->rondes()->orderBy('ronda', 'desc')->limit(1)->first();
    }

    //Relations
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function multievent(): BelongsTo
    {
        return $this->belongsTo(Multievent::class, 'id_multievent', 'id_multievent');
    }

    public function boards(): ?BelongsToMany
    {
        return $this->belongsToMany(Board::class, 'board_event', 'event_id', 'board_id')->withTimestamps();
    }

    public function tags(): ?BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tag', 'event_id', 'tag_id');
    }

    public function castellerTags(): ?BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_casteller_tag', 'event_id', 'tag_id');
    }

    public function attendanceAnswers(): ?BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_attendance_tag', 'event_id', 'tag_id');
    }

    public function attendances(): ?HasMany
    {
        return $this->hasMany(Attendance::class, 'event_id', 'id_event');
    }

    public function boardsEvent(): ?HasMany
    {
        return $this->hasMany(BoardEvent::class, 'event_id', 'id_event');
    }

    public function rondes(): ?HasMany
    {
        return $this->hasMany(Ronda::class, 'event_id', 'id_event');
    }

    public function notifications(): ?belongsToMany
    {
        return $this->belongsToMany(Notification::class, 'event_notification', 'event_id', 'notification_id');
    }

    //Getters
    public function getId(): int
    {
        return $this->getAttribute('id_event');
    }

    public function getIdExternal(): ?int
    {
        return $this->getAttribute('id_event_external');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    public function getMultievent(): ?Multievent
    {
        return $this->getAttribute('multievent');
    }

    public function getMultieventId(): ?int
    {
        return $this->getAttribute('id_multievent');
    }

    public function belongsToMultievent(): bool
    {
        return $this->id_multievent !== null;
    }

    public function assignToMultievent(Multievent $multievent): bool
    {
        $bag = new ParameterBag();

        $bag->set('id_multievent', $multievent->getId());

        $bag->set('name', $multievent->getName());
        $bag->set('address', $multievent->getAddress());
        $bag->set('location_link', $multievent->getLocationLink());
        $bag->set('comments', $multievent->getComments());
        $bag->set('duration', $multievent->getDuration());
        $bag->set('companions', $multievent->getCompanions());
        $bag->set('visibility', $multievent->getVisibility());
        $bag->set('type', $multievent->getType());

        if ($this->getStartDate() && $multievent->getTime()) {
            $eventDate = $this->getStartDate()->format('Y-m-d');
            $bag->set('start_date', $eventDate.' '.$multievent->getTime());
        }

        if ($multievent->hasCastellerTags()) {
            $bag->set('tags_casteller', $multievent->getCastellerTags()->pluck('value')->toArray());
        }

        $eventsManager = new EventsManager(new EventRepository());
        $eventsManager->updateEvent($this, $bag);

        return true;
    }

    public function detachFromMultievent(): bool
    {
        if ($this->getMultieventId()) {
            $this->id_multievent = null;

            return $this->save();
        }

        return true;
    }

    public function getTags(): Collection
    {
        return $this->tags()->where('type', TypeTags::Events()->value())->get();
    }

    public function getCastellerTags(): Collection
    {
        return $this->castellerTags()->where('type', TypeTags::Castellers()->value())->get();
    }

    public function getAttendanceAnswers(): ?Collection
    {
        return $this->attendanceAnswers()->where('type', TypeTags::Attendance()->value())->get();
    }

    public function getAttendanceAnswersOptions(): array
    {
        $answersOptions = [];
        $answers = $this->getAttendanceAnswers();
        foreach ($answers as $answer) {
            $answersOptions[$answer->getId()] = $answer->getName();
        }

        return $answersOptions;
    }

    public function getRondes(): ?Collection
    {
        return $this->getAttribute('rondes');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getLocationLink(): ?string
    {
        return $this->getAttribute('location_link');
    }

    public function getComments(): ?string
    {
        return $this->getAttribute('comments');
    }

    public function getDuration(): int
    {
        return $this->getAttribute('duration');
    }

    public function getStartDate(): Carbon
    {
        return $this->getAttribute('start_date');
    }

    public function getOpenDate(): ?Carbon
    {
        return $this->getAttribute('open_date');
    }

    public function getCloseDate(): ?Carbon
    {
        return $this->getAttribute('close_date');
    }

    public function getCompanions(): ?bool
    {
        return $this->getAttribute('companions');
    }

    public function getVisibility(): ?bool
    {
        return $this->getAttribute('visibility');
    }

    public function getPhoto(): ?string
    {
        return $this->getAttribute('photo');
    }

    public function getBoards(): ?Collection
    {
        return $this->getAttribute('boards');
    }

    public function getBoardsEvent(): ?Collection
    {
        return $this->getAttribute('boardsEvent');
    }

    public function getType(): ?int
    {
        return $this->getAttribute('type');
    }

    public function getTypeName(): string
    {
        $type = self::TYPES[$this->getAttribute('type')];
        $function = 'getTranslation'.ucwords($type);

        return ($this->getColla()->getConfig()->$function()) ? $this->getColla()->getConfig()->$function() : __('config.translation_'.$type);
    }

    public function getUrlGoogleCalendar(): string
    {
        $title = urlencode($this->name);
        $startDate = $this->getStartDate()->format('Ymd\THis');
        $endDate = $this->getStartDate()->copy()->addMinutes($this->getDuration())->format('Ymd\THis');
        $description = urlencode($this->getComments());
        $location = urlencode($this->getAddress());

        $googleCalendarUrl = "https://www.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$startDate}/{$endDate}&details={$description}&location={$location}";

        return $googleCalendarUrl;
    }
}
