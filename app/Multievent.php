<?php

namespace App;

use App\Enums\TypeTags;
use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

final class Multievent extends Model
{
    use FilterableTrait;
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'multievents';

    protected $primaryKey = 'id_multievent';

    public $timestamps = true;

    protected static $filterClass = \App\Services\Filters\MultieventsFilter::class;

    protected $fillable = [
        'colla_id',
        'name',
        'address',
        'location_link',
        'comments',
        'duration',
        'time',
        'companions',
        'visibility',
        'type',
        'photo',
    ];

    protected $dates = [];

    protected $casts = [
        'companions' => 'boolean',
        'visibility' => 'boolean',
    ];

    const TYPES = [
        '1' => 'assaig',
        '2' => 'actuacio',
        '3' => 'activitat',
    ];

    public static function detachEvent(Event $event): bool
    {
        if ($event->id_multievent) {
            $event->id_multievent = null;

            return $event->save();
        }

        return true;
    }

    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'id_multievent', 'id_multievent');
    }

    public function tags(): ?BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'multievent_tag', 'multievent_id', 'tag_id');
    }

    public function castellerTags(): ?BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'multievent_casteller_tag', 'multievent_id', 'tag_id');
    }

    public function getId(): int
    {
        return $this->getAttribute('id_multievent');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getComments(): ?string
    {
        return $this->getAttribute('comments');
    }

    public function getDuration(): int
    {
        return $this->getAttribute('duration');
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

    public function getLocationLink(): ?string
    {
        return $this->getAttribute('location_link');
    }

    public function getEvents(): Collection
    {
        return $this->events()->get();
    }

    public function getType(): ?int
    {
        return $this->getAttribute('type');
    }

    public function getTime(): ?string
    {
        return $this->getAttribute('time');
    }

    public function getHour(): ?string
    {
        if ($this->getTime()) {
            return date('H', strtotime($this->getTime()));
        }

        return null;
    }

    public function getMinute(): ?string
    {
        if ($this->getTime()) {
            return date('i', strtotime($this->getTime()));
        }

        return null;
    }

    public function getTypeName(): string
    {
        $type = self::TYPES[$this->getAttribute('type')];
        $function = 'getTranslation'.ucwords($type);

        return ($this->getColla()->getConfig()->$function())
            ? $this->getColla()->getConfig()->$function()
            : __('config.translation_'.$type);
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

    public function tagsArray(string $return_type = 'name'): array
    {
        return $this->tags->pluck($return_type)->toArray();
    }

    public function removeTag(Tag $tag): bool
    {
        if ($this->hasTag($tag->getValue()) && $tag->getCollaId() === $this->getCollaId()) {
            if (DB::table('multievent_tag')->where('multievent_id', $this->getId())->where('tag_id', $tag->getId())->delete()) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function hasTag(string $tag_value): bool
    {
        return in_array($tag_value, $this->tagsArray('value'));
    }

    public function hasTags(): bool
    {
        return $this->getTags()->isNotEmpty();
    }

    public function getTags(): Collection
    {
        return $this->tags()->where('type', TypeTags::Events()->value())->get();
    }

    public function getCastellerTags(): Collection
    {
        return $this->castellerTags()->where('type', TypeTags::Castellers()->value())->get();
    }

    public function castellerTagsArray(string $return_type = 'name'): array
    {
        return $this->getCastellerTags()->pluck($return_type)->toArray();
    }

    public function addCastellerTag(Tag $tag): bool
    {
        if ($tag->getTypeName() === TypeTags::CASTELLERS && $tag->getCollaId() === $this->getCollaId() && ! $this->hasCastellerTag($tag->getValue())) {
            DB::table('multievent_casteller_tag')->insert(['multievent_id' => $this->getId(), 'tag_id' => $tag->getId()]);

            return true;
        }

        return false;
    }

    public function removeCastellerTag(Tag $tag): bool
    {
        if ($this->hasCastellerTag($tag->getValue()) && $tag->getCollaId() === $this->getCollaId()) {
            if (DB::table('multievent_casteller_tag')->where('multievent_id', $this->getId())->where('tag_id', $tag->getId())->delete()) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function hasCastellerTag(string $tag_value): bool
    {
        return in_array($tag_value, $this->castellerTagsArray('value'));
    }

    public function hasCastellerTags(): bool
    {
        return $this->getCastellerTags()->isNotEmpty();
    }
}
