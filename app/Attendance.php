<?php

namespace App;

use App\Enums\AttendanceStatus;
use App\Enums\ScaledAttendanceStatus;
use App\Enums\TypeTags;
use App\Helpers\Humans;
use App\Managers\AttendanceManager;
use App\Repositories\AttendanceRepository;
use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\HttpFoundation\ParameterBag;

class Attendance extends Model
{
    use FilterableTrait;
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'attendance';

    protected $primaryKey = 'id_attendance';

    protected static $filterClass = \App\Services\Filters\EventAttendanceFilter::class;

    private const SOURCES = [
        '1' => 'web',
        '2' => 'telegram',
    ];

    private const STATUSES = [
        '1' => 'ok',
        '2' => 'nok',
        '3' => 'unknown',
    ];

    /** set status of a Member / Event attendance*/
    public static function setStatus(int $id_casteller, int $id_event, ?int $status = null, int $source = 1)
    {
        $attendanceManager = new AttendanceManager(new AttendanceRepository());

        $parameterBag = new ParameterBag([
            'status' => $status,
            'source' => $source,
        ]);

        $attendance = self::getAttendanceCastellerEvent($id_casteller, $id_event);

        if (is_null($attendance)) {
            $attendanceManager->createAttendance($id_casteller, $id_event, $parameterBag);

        } else {

            $attendanceManager->updateAttendance($attendance, $parameterBag);
        }
    }

    /** set VerifiedStatus of a Member / Event attendance */
    public static function setStatusVerified(int $id_casteller, int $id_event, ?string $statusVerified = null, int $source = 1)
    {
        $attendanceManager = new AttendanceManager(new AttendanceRepository());

        $parameterBag = new ParameterBag([
            'status_verified' => $statusVerified,
            'source' => $source,
        ]);

        $attendance = self::getAttendanceCastellerEvent($id_casteller, $id_event);

        if (! $attendance) {

            $attendanceManager->createAttendance($id_casteller, $id_event, $parameterBag);
        } else {

            $attendanceManager->updateAttendance($attendance, $parameterBag);
        }

    }

    /** set attendance answers Event / Casteller via AJAX*/
    public static function setAnswers(int $id_casteller, int $id_event, array $answers, int $source = 1)
    {
        $attendanceManager = new AttendanceManager(new AttendanceRepository());

        $parameterBag = new ParameterBag([
            'options' => json_encode($answers),
            'source' => $source,
        ]);

        $attendance = self::getAttendanceCastellerEvent($id_casteller, $id_event);

        if (is_null($attendance)) {

            $attendanceManager->createAttendance($id_casteller, $id_event, $parameterBag);
        } else {

            $attendanceManager->updateAttendance($attendance, $parameterBag);
        }

    }

    /** set Companions Event / Casteller via AJAX */
    public static function setCompanions(int $id_casteller, int $id_event, int $companions, int $source = 1)
    {
        $attendanceManager = new AttendanceManager(new AttendanceRepository());

        $parameterBag = new ParameterBag([
            'companions' => $companions,
            'source' => $source,
        ]);

        $attendance = self::getAttendanceCastellerEvent($id_casteller, $id_event);

        if (is_null($attendance)) {

            $attendanceManager->createAttendance($id_casteller, $id_event, $parameterBag);
        } else {

            $attendanceManager->updateAttendance($attendance, $parameterBag);
        }
    }

    /** get Attendance of Casteller / Event */
    public static function getAttendanceCastellerEvent(int $id_casteller, int $id_event): ?Attendance
    {
        return Attendance::query()
            ->where('casteller_id', $id_casteller)
            ->where('event_id', $id_event)
            ->first();
    }

    /** get Attendance of Casteller for all events*/
    public static function getAttendanceCasteller(int $id_casteller): ?Collection
    {
        return Attendance::query()->where('casteller_id', $id_casteller)->get();
    }

    /** get Attendance of specific Status of one Event for all castellers*/
    public static function getAttendanceEventByStatus(int $id_event, int|string $status): ?Collection
    {
        if (is_string($status)) {
            $status = Attendance::getStatusId($status);
        }

        return Attendance::query()->where('event_id', $id_event)->where('status', $status)->get();
    }

    /** get Attendance of one Event for all castellers*/
    public static function getAttendanceEvent(int $id_event): ?Collection
    {
        return Attendance::query()->where('event_id', $id_event)->get();
    }

    /** get Attendance of all events of one Colla*/
    public static function getAttendanceColla(int|Colla $colla): ?Collection
    {
        if (is_int($colla)) {
            $colla = Colla::query()->find($colla);
        }
        $events = $colla->getEvents()->toArray();

        return Attendance::query()->whereIn('event_id', array_column($events, 'id_event'))->get();
    }

    /** get Scaled Attendance from an attendance */
    public function getScaledAttendance(): ScaledAttendanceStatus
    {
        $verifiedAttendanceStatus = $this->getStatusVerified();
        $attendanceStatus = $this->getStatus();

        if (! is_null($verifiedAttendanceStatus)) {
            if ($verifiedAttendanceStatus === AttendanceStatus::YES) {
                return ScaledAttendanceStatus::YESVERIFIED();
            } elseif ($verifiedAttendanceStatus === AttendanceStatus::NO) {
                return ScaledAttendanceStatus::NO();
            } else {
                return ScaledAttendanceStatus::UNKNOWN();
            }
        } elseif (! is_null($attendanceStatus)) {
            if ($attendanceStatus === AttendanceStatus::YES) {
                return ScaledAttendanceStatus::YES();
            } elseif ($attendanceStatus === AttendanceStatus::NO) {
                return ScaledAttendanceStatus::NO();
            } else {
                return ScaledAttendanceStatus::UNKNOWN();
            }
        } else {
            return ScaledAttendanceStatus::UNKNOWN();
        }

    }

    public static function getSources(): array
    {
        $return[1] = __('attendance.sources_'.self::SOURCES[1]);
        $return[2] = __('attendance.sources_'.self::SOURCES[2]);
        asort($return);

        return $return;
    }

    public static function getSourceId(string $source): int
    {
        return array_search($source, self::SOURCES);
    }

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    public static function getStatusId(string $status): int
    {
        return array_search($status, self::STATUSES);
    }

    //Relations

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id_event');
    }

    public function casteller(): BelongsTo
    {
        return $this->belongsTo(Casteller::class, 'casteller_id', 'id_casteller');
    }

    //Getters

    public function getEvent(): Event
    {
        return $this->getAttribute('event');
    }

    public function getCasteller(): Casteller
    {
        return $this->getAttribute('casteller');
    }

    public function getId(): int
    {
        return $this->getAttribute('id_attendance');
    }

    public function getIdExternal(): ?int
    {
        return $this->getAttribute('id_event_external');
    }

    public function getStatus(): ?int
    {
        return $this->getAttribute('status');
    }

    public function getStatusName(): string
    {
        return self::getStatuses()[$this->getStatus() ?? '3'];
    }

    public function getStatusVerified(): ?int
    {
        return $this->getAttribute('status_verified');
    }

    public function getStatusVerifiedName(): string
    {
        return self::getStatuses()[$this->getStatusVerified() ?? '3'];
    }

    public function getSource(): int
    {
        return $this->getAttribute('source');
    }

    public function getSourceName(): string
    {
        return self::getSources()[$this->getSource()];
    }

    public function getCompanions(): ?int
    {
        return $this->getAttribute('companions');
    }

    public function getOptions(): ?array
    {
        // We only care about selected options if attendance is yes.
        if ($this->getStatus() != AttendanceStatus::YES) {
            return [];
        }

        return json_decode($this->getAttribute('options'));
    }

    public function getOptionsNames(): array
    {
        // We only care about selected options if attendance is yes.
        if ($this->getStatus() != AttendanceStatus::YES) {
            return [];
        }

        $optionNames = [];
        $options = json_decode($this->getAttribute('options'));

        if (! empty($options)) {
            $tags = Tag::currentTags(TypeTags::ATTENDANCE, $this->getEvent()->getColla());
            foreach ($options as $option) {
                $optionNames[] = $tags->find($option)->getName();
            }
            sort($optionNames);
        }

        return $optionNames;
    }

    public function getUpdatedAtParsed(bool $shortDate = false): ?string
    {
        return Humans::parseDate($this->getUpdatedAt(), $shortDate);
    }
}
