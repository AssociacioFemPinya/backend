<?php

namespace App;

use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Row extends Model
{
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'rows';

    protected $primaryKey = 'id';

    public $timestamps = true;

    //Relations
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    public function board(): HasOne
    {
        return $this->hasOne(Board::class, 'id_board', 'board_id');
    }

    //Getters
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

    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getDivId(): int
    {
        return $this->getAttribute('div_id');
    }

    public function getRow(): string
    {
        return $this->getAttribute('row');
    }

    public function getCord(): ?int
    {
        return $this->getAttribute('cord');
    }

    public function getSide(): ?string
    {
        return $this->getAttribute('side');
    }

    public function getIdPosition(): string
    {
        return $this->getAttribute('id_position');
    }

    public function getPosition(): string
    {
        return $this->getAttribute('position');
    }

    public function getBase(): string
    {
        return $this->getAttribute('base');
    }
}
