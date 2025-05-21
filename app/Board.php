<?php

declare(strict_types=1);

namespace App;

use App\Enums\BasesEnum;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Board extends Model
{
    use TimeStampsGetterTrait;

    protected $table = 'boards';

    protected $primaryKey = 'id_board';

    public $timestamps = true;

    protected $casts = [
        'data' => 'array',
        'data_code' => 'array',
    ];

    public const baixName = 'baix';

    private static $colors = ['f44336', '9c27b0', '3f51b5', '2196f3', '009688', '8bc34a', 'ffeb3b', 'ffc107', '607d8b', '795548'];

    /** get num of rows (rengles) of BASE/BOARD */
    public function getArrayBasesRows(): array
    {
        $array = [];

        foreach ($this->getData() as $name => $base) {

            $array[$name] = is_null($base) ? null : array_keys($base['structure']);
        }

        return $array;
    }

    /** get array colors */
    public static function getColors(): array
    {
        return self::$colors;
    }

    /** get base (tags) to Board */
    public function tags(): ?Collection
    {
        return Tag::query()
            ->join('board_tags', 'tags.id_tag', 'board_tags.tag_id')
            ->join('boards', 'board_tags.board_id', 'boards.id_board')
            ->where('id_board', $this->getId())
            ->where('tags.type', 'BOARDS')
            ->select('tags.*')
            ->get();
    }

    /** true/false board has folre */
    public function hasFolre(): bool
    {
        if ($this->getType() === BasesEnum::FOLRE || $this->getType() === BasesEnum::MANILLES || $this->getType() === BasesEnum::PUNTALS) {

            return true;
        }

        return false;
    }

    /** true/false board has mailles */
    public function hasManilles(): bool
    {
        if ($this->getType() === BasesEnum::MANILLES || $this->getType() === BasesEnum::PUNTALS) {

            return true;
        }

        return false;
    }

    /** true/false board has puntals */
    public function hasPuntals(): bool
    {
        if ($this->getType() === BasesEnum::PUNTALS) {

            return true;
        }

        return false;
    }

    /** ready for use */
    public function hasReady(string $base = BasesEnum::PINYA): bool
    {
        return match ($base) {
            BasesEnum::Folre()->value() => isset($this->getData()['folre']) && $this->getData()['folre'] !== null,
            BasesEnum::Manilles()->value() => isset($this->getData()['manilles']) && $this->getData()['manilles'] !== null,
            BasesEnum::Puntals()->value() => isset($this->getData()['puntals']) && $this->getData()['puntals'] !== null,
            default => isset($this->getData()['pinya']) && $this->getData()['pinya'] !== null,
        };
    }

    public function getHtmlBase(string $base = BasesEnum::PINYA): string
    {
        return match ($base) {
            BasesEnum::FOLRE => $this->getHtmlFolre(),
            BasesEnum::MANILLES => $this->getHtmlManilles(),
            BasesEnum::PUNTALS => $this->getHtmlPuntals(),
            default => $this->getHtmlPinya(),
        };
    }

    public function getSvgUrl($base = BasesEnum::PINYA): ?string
    {
        $collaShorName = $this->getColla()->getShortName();
        $filePath = 'media/colles/'.$collaShorName.'/svg/'.$this->getId().'_'.$base.'.svg';

        if (file_exists(public_path($filePath))) {

            return asset($filePath);
        }

        return null;
    }

    //Relations
    public function events(): ?BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'board_event', 'board_id', 'event_id')->withTimestamps();
    }

    public function boardPosition(): HasMany
    {
        return $this->hasMany(BoardPosition::class, 'board_id', 'id_board');
    }

    public function boardEvent(): HasMany
    {
        return $this->hasMany(BoardEvent::class, 'board_id', 'id_board');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(Row::class, 'board_id', 'id_board');
    }

    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    // Properties
    public function getId(): int
    {
        return $this->getAttribute('id_board');
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

    public function getType(): string
    {
        return $this->getAttribute('type');
    }

    public function getData(): ?array
    {
        return $this->getAttribute('data');
    }

    public function getHtmlPinya(): ?string
    {
        return $this->getAttribute('html_pinya');
    }

    public function getHtmlFolre(): ?string
    {
        return $this->getAttribute('html_folre');
    }

    public function getHtmlManilles(): ?string
    {
        return $this->getAttribute('html_manilles');
    }

    public function getHtmlPuntals(): ?string
    {
        return $this->getAttribute('html_puntals');
    }

    public function getIsPublic(): ?boolean
    {
        return (bool) $this->getAttribute('is_public');
    }

    public function getEvents(): ?Collection
    {
        return $this->getAttribute('events');
    }

    public function getRows(): ?Collection
    {
        return $this->getAttribute('rows');
    }
}
