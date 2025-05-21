<?php

declare(strict_types=1);

namespace App;

use App\Enums\TypeTags;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'tags';

    protected $primaryKey = 'id_tag';

    public $timestamps = true;

    public const TAG_ALL = 'all';

    /** return true/false if tag is used  */
    public function isUsed(): bool
    {
        $count = null;
        if ($this->getType() === TypeTags::Castellers()->value() || $this->getType() === TypeTags::Positions()->value()) {

            $count = DB::table('casteller_tag')->where('tag_id', $this->getId())->count();
        } elseif ($this->getType() === TypeTags::Events()->value()) {

            $count = DB::table('event_tag')->where('tag_id', $this->getId())->count();
        } elseif ($this->getType() === TypeTags::Attendance()->value()) {

            $count = DB::table('event_attendance_tag')->where('tag_id', $this->getId())->count();
        } elseif ($this->getType() === TypeTags::Boards()->value()) {

            $count = DB::table('board_tags')->where('tag_id', $this->getId())->count();
        }

        return (bool) $count;
    }

    /** @deprecated */
    public static function currentTags(string $type = TypeTags::CASTELLERS, ?Colla $colla = null, bool $used = false): Collection
    {
        if (! $colla) {
            $colla = Colla::getCurrent();
        }

        if (! $used) {

            return Tag::query()->where('colla_id', $colla->getId())->where('type', $type)->orderBy('name')->get();
        } else {
            $tags = Tag::query()->where('colla_id', $colla->getId())->where('type', $type)->orderBy('name')->get();

            foreach ($tags as $key => $tag) {
                if (! $tag->isUsed()) {
                    unset($tags[$key]);
                }
            }

            return $tags;
        }
    }

    /** get groups Tags from Colla*/
    public static function groups(string $type = TypeTags::CASTELLERS, ?Colla $colla = null): Collection
    {
        if (! $colla) {
            $colla = Colla::getCurrent();
        }

        return Tag::query()
            ->select('group')
            ->where('colla_id', $colla->getId())->where('type', $type)
            ->orderBy('group', 'asc')
            ->distinct()
            ->get();
    }

    public static function validName(string $name): bool
    {
        if (! $name || $name === ' ' || $name === 'null') {

            return false;
        }

        //No symbols, special characters
        $rexSafety = "/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i";

        if (preg_match($rexSafety, $name)) {

            return false;
        }

        return true;
    }

    public static function validNameAttendance(string $name): bool
    {

        if (! $name || $name === ' ' || $name === 'null') {

            return false;
        }

        //No symbols, special characters
        $rexSafety = "/[\^<\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i";

        if (preg_match($rexSafety, $name)) {

            return false;
        }

        return true;
    }

    /** get Tag by External ID */
    public static function getTagByIdExternal(int $idCastellerExternal, Colla $colla): ?Casteller
    {
        return Tag::query()
            ->where('id_tag_external', $idCastellerExternal)
            ->where('colla_id', $colla->getId())
            ->first();
    }

    /** get Tag by Name */
    public static function getTagByName(string $name, Colla $colla): ?Tag
    {
        return Tag::query()
            ->where('name', $name)
            ->where('colla_id', $colla->getId())
            ->first();
    }

    //Relations
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function casteller(): ?BelongsToMany
    {
        return $this->belongsToMany(Casteller::class, 'casteller_tag', 'tag_id', 'casteller_id')
            ->where('castellers.colla_id', $this->getCollaId());
    }

    public function event(): ?BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_tag', 'event_id', 'tag_id');
    }

    //getters
    public function getId(): int
    {
        return $this->getAttribute('id_tag');
    }

    public function getIdExternal(): ?int
    {
        return $this->getAttribute('id_tag_external');
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

    public function getValue(): string
    {
        return $this->getAttribute('value');
    }

    public function getGroup(): string
    {
        return $this->getAttribute('group');
    }

    public function getType(): string
    {
        return $this->getAttribute('type');
    }
}
