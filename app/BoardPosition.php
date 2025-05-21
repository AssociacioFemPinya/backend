<?php

declare(strict_types=1);

namespace App;

use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class BoardPosition extends Model
{
    use FilterableTrait;
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'board_position';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $casts = [
        'data' => 'array',
    ];

    protected static $filterClass = \App\Services\Filters\EventBoardPositionsFilter::class;

    //Relations
    public function board(): HasOne
    {
        return $this->hasOne(Board::class, 'id_board', 'board_id');
    }

    public function event(): HasOne
    {
        return $this->hasOne(Event::class, 'id_event', 'event_id');
    }

    public function casteller(): HasOne
    {
        return $this->hasOne(Casteller::class, 'id_casteller', 'casteller_id');
    }

    public function boardEvent(): HasOne
    {
        return $this->hasOne(BoardEvent::class, 'id', 'board_event_id');
    }

    public function row(): BelongsTo
    {
        return $this->belongsTo(Row::class, 'row_id', 'id');
    }

    //Properties
    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getBoard(): Board
    {
        return $this->getAttribute('board');
    }

    public function getBoardId(): int
    {
        return $this->getAttribute('board_id');
    }

    public function getEventId(): int
    {
        return $this->getAttribute('event_id');
    }

    public function getEvent(): Event
    {
        return $this->getAttribute('event');
    }

    public function getCasteller(): Casteller
    {
        return $this->getAttribute('casteller');
    }

    public function getCastellerId(): int
    {
        return $this->getAttribute('casteller_id');
    }

    public function getBoardEvent(): BoardEvent
    {
        return $this->getAttribute('boardEvent');
    }

    public function getBoardEventId(): int
    {
        return $this->getAttribute('board_event_id');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getRowId(): int
    {
        return $this->getAttribute('row_id');
    }

    public function getRow(): Row
    {
        return $this->getAttribute('row');
    }

    public function getBase(): string
    {
        return $this->getAttribute('base');
    }
}
