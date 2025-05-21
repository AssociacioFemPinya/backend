<?php

namespace App;

use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ronda extends Model
{
    use FilterableTrait;
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'rondes';

    protected $primaryKey = 'id_ronda';

    public $timestamps = true;

    protected $fillable = ['event_id', 'board_event_id', 'ronda'];

    protected $guarded = ['id_ronda', 'created_at', 'updated_at'];

    protected static $filterClass = \App\Services\Filters\EventRondesFilter::class;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id_event');
    }

    public function boardEvent(): HasOne
    {
        return $this->hasOne(BoardEvent::class, 'id', 'board_event_id');
    }

    public function getId(): int
    {
        return $this->getAttribute('id_ronda');
    }

    public function getEvent(): Event
    {
        return $this->getAttribute('event');
    }

    public function getEventId(): int
    {
        return $this->getAttribute('event_id');
    }

    public function getBoardEvent(): BoardEvent
    {
        return $this->getAttribute('boardEvent');
    }

    public function getBoardEventId(): int
    {
        return $this->getAttribute('board_event_id');
    }

    public function getRonda(): int
    {
        return $this->getAttribute('ronda');
    }
}
