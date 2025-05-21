<?php

declare(strict_types=1);

namespace App;

use App\Enums\TypeTags;
use App\Traits\TimeStampsGetterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

final class Colla extends Model
{
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'colles';

    protected $primaryKey = 'id_colla';

    public $timestamps = true;

    private static ?Colla $colla = null;

    /** get current Colla from user authenticated */
    public static function getCurrent(): Colla
    {
        if (! self::$colla) {
            self::$colla = Auth::user()->getColla();
        }

        return self::$colla;
    }

    /** Num castellers from colla */
    public function numCastellers(): int
    {
        return Casteller::query()->where('colla_id', $this->getId())->count();
    }

    /** true/false colla has users */
    public function hasUsers(): bool
    {
        return (bool) User::query()
            ->where('colla_id', $this->getId())
            ->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'Super-Admin');
            })
            ->count();
    }

    /** Last login from any user from Colla */
    public function getLastLogin(bool $shortDate = false): array
    {
        $return['user'] = trans('admin.colla_no_users');
        $return['date'] = '-';

        if ($this->hasUsers()) {
            /** @var User $lastAccessUser */
            $lastAccessUser = User::query()
                ->where('colla_id', $this->getId())
                ->whereNotNull('last_access_at')
                ->orderBy('last_access_at', 'desc')
                ->first();

            if ($lastAccessUser) {
                $return['date'] = $lastAccessUser->getLastLogin($shortDate);
                $return['user'] = $lastAccessUser->getName();
            } else {
                $return['user'] = trans('admin.colla_no_users_logged_in');
                $return['date'] = '-';
            }

            return $return;
        }

        return $return;
    }

    /** get path profile image from logo */
    public function getLogoImage(): string
    {
        if ($this->getLogo()) {

            return asset('media/colles/'.$this->getShortName().'/'.$this->getLogo());
        }

        return asset('media/logo.png');
    }

    /** get path profile image from banner */
    public function getBannerImage(): string
    {
        if ($this->getBanner()) {

            return asset('media/colles/'.$this->getShortName().'/'.$this->getBanner());
        }

        return asset('media/banner.png');
    }

    /** get families from Colla*/
    public function getFamilies(): Collection
    {
        return Casteller::query()
            ->select('family')
            ->whereNotNull('family')
            ->where('colla_id', $this->getId())
            ->distinct()
            ->get();
    }

    /** get Colla by External ID */
    public static function getCollaByIdExternal(int $idCollaExternal): ?Colla
    {
        return Colla::query()->where('id_colla_external', $idCollaExternal)->first();
    }

    public function getTags(string $type = TypeTags::CASTELLERS): Collection
    {
        return $this->tags()
            ->where('type', $type)
            ->orderBy('name', 'asc')
            ->get();
    }

    //Relations
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'colla_id', 'id_colla');
    }

    public function events(): ?HasMany
    {
        return $this->hasMany(Event::class, 'colla_id', 'id_colla');
    }

    public function castellers(): ?HasMany
    {
        return $this->hasMany(Casteller::class, 'colla_id', 'id_colla');
    }

    public function boards(): ?HasMany
    {
        return $this->hasMany(Board::class, 'colla_id', 'id_colla');
    }

    public function tags(): ?HasMany
    {
        return $this->hasMany(Tag::class, 'colla_id', 'id_colla');
    }

    public function config(): ?HasOne
    {
        return $this->hasOne(CollaConfig::class, 'colla_id', 'id_colla');
    }

    public function rows(): ?HasMany
    {
        return $this->hasMany(Row::class, 'colla_id', 'id_colla');
    }

    public function periods(): ?HasMany
    {
        return $this->hasMany(Period::class, 'colla_id', 'id_colla');
    }

    // getter relations
    public function getConfig(): ?CollaConfig
    {
        return $this->getAttribute('config');
    }

    public function getCastellers(): ?Collection
    {
        return $this->getAttribute('castellers');
    }

    public function getEvents(): ?Collection
    {
        return $this->getAttribute('events');
    }

    //Properties
    public function getId(): int
    {
        return $this->getAttribute('id_colla');
    }

    public function getIdExternal(): ?int
    {
        return $this->getAttribute('id_colla_external');
    }

    public function getBoards(): ?Collection
    {
        return $this->getAttribute('boards');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getShortName(): ?string
    {
        return $this->getAttribute('shortname');
    }

    public function getEmail(): string
    {
        return $this->getAttribute('email');
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function getCountry(): ?string
    {
        return $this->getAttribute('country');
    }

    public function getCity(): ?string
    {
        return $this->getAttribute('city');
    }

    public function getMaxMembers(): ?int
    {
        return $this->getAttribute('max_members');
    }

    public function getLogo(): ?string
    {
        return $this->getAttribute('logo');
    }

    public function getBanner(): ?string
    {
        return $this->getAttribute('banner');
    }

    public function getColor(): ?string
    {
        return $this->getAttribute('color');
    }

    public function getCurrentPeriod(): ?Period
    {
        return $this->periods()
            ->where('start_period', '<', Carbon::now())
            ->where('end_period', '>', Carbon::now())
            ->first();
    }
}
