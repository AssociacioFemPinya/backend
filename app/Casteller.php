<?php

declare(strict_types=1);

namespace App;

use App\Enums\CastellersStatusEnum;
use App\Enums\TypeTags;
use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Casteller extends Model
{
    use FilterableTrait;
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'castellers';

    protected $primaryKey = 'id_casteller';

    public $timestamps = true;

    protected static $filterClass = \App\Services\Filters\CastellersFilter::class;

    protected $guarded = ['id_casteller', 'id_casteller_external', 'created_at', 'updated_at'];

    protected $dates = [
        'birthdate',
        'subscription_date',
    ];

    const STATUSES = [
        '1' => 'novell',
        '2' => 'actiu',
        '3' => 'inactiu',
        '4' => 'lesionat',
    ];

    /** get photo path of a casteller*/
    public function getProfileImage(string $type = 'med'): string
    {
        $colla = $this->getColla();

        if (! $this->getPhoto()) {

            return asset('media/avatars/avatar.jpg');
        } else {

            return asset('media/colles/'.$colla->getShortName().'/castellers/'.$this->getPhoto().'-'.$type.'.png');
        }
    }

    /** get display name on casteller by configuration */
    public function getDisplayName(string $config = ' [alias]  [name] [last_name]'): string
    {
        return $this->getAlias();

        //$lastName = substr($this->getLastName(), 0, strpos($this->getLastName(), ' '));
        //return str_replace(['[name]', '[last_name]'], [$this->getName(), $lastName], $config);
    }

    /** remove tag of a casteller*/
    public function removeTag(Tag $tag): bool
    {
        if ($tag->getCollaId() === $this->getCollaId()) {
            return (bool) DB::table('casteller_tag')
                ->where('casteller_id', $this->getId())
                ->where('tag_id', $tag->getId())
                ->delete();
        }

        return false;
    }

    /** return true if Casteller has a Tag*/
    public function hasTag(string $tag_value): bool
    {
        return in_array($tag_value, $this->tagsArray('value'));
    }

    public function hasTags(): bool
    {
        return $this->getTags()->isNotEmpty();
    }

    /** get Array tags (only name or value) to Casteller*/
    public function tagsArray(string $return_type = 'name'): array
    {
        return $this->getTags()->pluck($return_type)->toArray();
    }

    /** Get age from birthdate. */
    public function getAge(): ?int
    {
        if (! $this->getBirthdate()) {
            return null;
        }

        return $this->getBirthdate()->diffInYears(Carbon::now());
    }

    /** get Attendance on upcoming/past Events */
    public function getAttendanceEvents(string $type = 'upcoming', int $num = 6): ?Collection
    {
        $events = Event::query()->where('colla_id', $this->getCollaId());

        if ($type === 'upcoming') {
            $events = $events
                ->whereDate('start_date', '>', Carbon::now())
                ->orderBy('start_date', 'asc')
                ->limit($num)
                ->get();
        } else {
            $events = $events
                ->whereDate('start_date', '<', Carbon::now())
                ->orderBy('start_date', 'asc')
                ->limit($num)
                ->get();
        }

        if (! $events) {
            return null;
        }

        //TODO refactor with relations!
        foreach ($events as $event) {
            $event->attendance = Attendance::getAttendanceCastellerEvent($this->getId(), $event->getId());
        }

        return $events;
    }

    /** TODO move into repository */
    public static function getCastellerByTelegramToken($telegramToken): ?Casteller
    {
        $castellerConfig = CastellerConfig::query()
            ->where('telegram_token', $telegramToken)
            ->first();

        if ($castellerConfig) {
            return $castellerConfig->getCasteller();
        }

        return $castellerConfig;
    }

    /**
     * TODO move into repository
     * get Castellers found in the Search
     */
    public static function getCastellersBySearchString(string $searchString, int $colla): ?Collection
    {
        return Casteller::query()
            ->where('alias', 'LIKE', '%'.$searchString.'%')
            ->where('colla_id', $colla)
            //->take($limit)
            ->get();
    }

    /**
     * TODO move into repository
     * get Casteller by External ID
     */
    public static function getCastellerByIdExternal(int $idCastellerExternal): ?Casteller
    {
        return Casteller::query()
            ->where('id_casteller_external', $idCastellerExternal)
            ->first();
    }

    public static function getCastellerByAlias(string $alias, int $colla): ?Casteller
    {
        return Casteller::query()
            ->where('alias', $alias)
            ->where('colla_id', $colla)
            ->first();
    }

    public function hasLinkedCasteller(Casteller $linkedCasteller): bool
    {
        return in_array($linkedCasteller, $this->getLinkedCastellers()->toArray());
    }

    public static function getStatuses(): array
    {
        $return = [];
        $return[1] = __('casteller.status_'.self::STATUSES[1]);
        $return[2] = __('casteller.status_'.self::STATUSES[2]);
        $return[3] = __('casteller.status_'.self::STATUSES[3]);
        $return[4] = __('casteller.status_'.self::STATUSES[4]);
        asort($return);

        return $return;
    }

    public function getEventAttendance(int $eventId): ?Attendance
    {
        return Attendance::query()
            ->where('event_id', $eventId)
            ->where('casteller_id', $this->getId())
            ->first();
    }

    public function getRelativeHeight(): int
    {
        $baseline = $this->getColla()->getConfig()->getHeightBaseline();
        $height = $this->getHeight() - $baseline;

        return (int) $height;
    }

    public function getRelativeShoulderHeight(): int
    {
        $baseline = $this->getColla()->getConfig()->getShoulderHeightBaseline();
        $height = $this->getShoulderHeight() - $baseline;

        return (int) $height;
    }

    //relations
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function tags(): ?BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'casteller_tag', 'casteller_id', 'tag_id');
    }

    public function linkedCastellers(): ?BelongsToMany
    {
        return $this->belongsToMany(self::class, 'casteller_relationship', 'id_casteller', 'casteller_id');
    }

    public function parents(): ?BelongsToMany
    {
        return $this->belongsToMany(self::class, 'casteller_relationship', 'casteller_id', 'id_casteller');
    }

    public function castellerTelegram(): ?HasOne
    {
        return $this->hasOne(CastellerTelegram::class, 'casteller_id', 'id_casteller');
    }

    public function castellerConfig(): ?HasOne
    {
        return $this->hasOne(CastellerConfig::class, 'casteller_id', 'id_casteller');
    }

    public function authConfig(): ?HasOne
    {
        return $this->hasOne(AuthConfig::class, 'casteller_id', 'id_casteller');
    }

    public function attendance(): ?HasMany
    {
        return $this->hasMany(Attendance::class, 'casteller_id', 'id_casteller');
    }

    public function boardPosition(): ?HasMany
    {
        return $this->hasMany(BoardPosition::class, 'casteller_id', 'id_casteller');
    }

    //getters
    public function getId(): int
    {
        return $this->getAttribute('id_casteller');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    public function getTags(): Collection
    {
        return $this->tags->where('type', TypeTags::Castellers()->value());
    }

    public function getPosition(): ?Tag
    {
        return $this->tags->where('type', TypeTags::Positions()->value())->first();
    }

    public function getLinkedCastellers(): ?Collection
    {
        return $this->getAttribute('linkedCastellers');
    }

    public function getParents(): ?Collection
    {
        return $this->getAttribute('parents');
    }

    public function getCastellerTelegram(): ?CastellerTelegram
    {
        return $this->getAttribute('castellerTelegram');
    }

    public function getCastellerConfig(): ?CastellerConfig
    {
        return $this->getAttribute('castellerConfig');
    }

    public function getIdExternal(): ?int
    {
        return $this->getAttribute('id_casteller_external');
    }

    public function getStatus(): int
    {
        return $this->getAttribute('status');
    }

    public function getStatusName(): string
    {
        return self::getStatuses()[$this->getStatus()];
    }

    public function getNumSoci(): ?string
    {
        return $this->getAttribute('num_soci');
    }

    public function getNationality(): ?string
    {
        return $this->getAttribute('nationality');
    }

    public function getNationalIdNumber(): ?string
    {
        return $this->getAttribute('national_id_number');
    }

    public function getNationalIdType(): ?string
    {
        return $this->getAttribute('national_id_type');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getLastName(): ?string
    {
        return $this->getAttribute('last_name');
    }

    public function getFullName(): ?string
    {
        $fullName = '';
        $name = $this->getName();
        $lastName = $this->getLastName();

        if ($name) {
            $fullName = $name;
        }
        if ($lastName) {
            $fullName .= ' '.$lastName;
        }

        return trim($fullName);
    }

    public function getAlias(): string
    {
        return $this->getAttribute('alias');
    }

    public function getGender(): ?int
    {
        return $this->getAttribute('gender');
    }

    public function getBirthdate(): ?Carbon
    {
        return $this->getAttribute('birthdate');
    }

    public function getSubscriptionDate(): ?Carbon
    {
        return $this->getAttribute('subscription_date');
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    public function getEmail2(): ?string
    {
        return $this->getAttribute('email2');
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function getPhoneMobile(): ?string
    {
        return $this->getAttribute('mobile_phone');
    }

    public function getPhoneEmergency(): ?string
    {
        return $this->getAttribute('emergency_phone');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getZipCode(): ?string
    {
        return $this->getAttribute('postal_code');
    }

    public function getCity(): ?string
    {
        return $this->getAttribute('city');
    }

    public function getComarca(): ?string
    {
        return $this->getAttribute('comarca');
    }

    public function getProvince(): ?string
    {
        return $this->getAttribute('province');
    }

    public function getCountry(): ?string
    {
        return $this->getAttribute('country');
    }

    public function getComments(): ?string
    {
        return $this->getAttribute('comments');
    }

    public function getPhoto(): ?string
    {
        return $this->getAttribute('photo');
    }

    public function getHeight(): ?float
    {
        return $this->getAttribute('height');
    }

    public function getWeight(): ?float
    {
        return $this->getAttribute('weight');
    }

    public function getShoulderHeight(): ?float
    {
        return $this->getAttribute('shoulder_height');
    }

    public function getInteractionType(): ?int
    {
        return $this->getAttribute('interaction_type');
    }

    public function setInteractionType(int $interactionType)
    {
        return $this->setAttribute('interaction_type', $interactionType);
    }

    public function hasInteractionType(): bool
    {
        return ! empty($this->getInteractionType());
    }

    public function getLanguage(): ?string
    {
        return $this->getAttribute('language');
    }

    public function setLanguage(string $language)
    {
        return $this->setAttribute('language', $language);
    }

    public function isActivePinya(): bool
    {
        $activePinyaStatuses = [
            CastellersStatusEnum::NOOB,
            CastellersStatusEnum::ACTIVE,
        ];

        return in_array($this->getAttribute('status'), $activePinyaStatuses);
    }

    //getters custom

    public function getLinkedCastellersExcludeActive(): array
    {
        $return = [
            'all' => Collection::empty(),
            'enabled' => Collection::empty(),
            'disabled' => Collection::empty(),
        ];

        $linkedCastellersNotActive = $this->linkedCastellers()
            ->where('casteller_id', '!=', $this->getCastellerTelegram()->getCastellerActiveId())
            ->get();

        if (! $linkedCastellersNotActive->isEmpty()) {
            $linkedCastellersNotActive->sort(function ($a, $b) {
                return strcasecmp($a->getDisplayName(), $b->getDisplayName());
            })->values();
            $return['all'] = $linkedCastellersNotActive;
            $return['enabled'] = $linkedCastellersNotActive->filter(function ($casteller) {
                return $casteller->getCastellerConfig()->getTelegramEnabled() === 1;
            })->values();
            $return['disabled'] = $linkedCastellersNotActive->filter(function ($casteller) {
                return $casteller->getCastellerConfig()->getTelegramEnabled() === 0;
            })->values();
        }

        return $return;
    }
}
