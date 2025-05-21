<?php

namespace App;

use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Period extends Model
{
    use HasFactory;
    use TimeStampsGetterTrait;

    protected $table = 'periods';

    protected $primaryKey = 'id_period';

    protected $start_period = 'start_period';

    public $timestamps = true;

    //Relations
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    //Getters
    public function getId(): int
    {
        return $this->getAttribute('id_period');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getStartPeriod(): string
    {

        return $this->getAttribute('start_period');
    }

    public function getEndPeriod(): string
    {
        return $this->getAttribute('end_period');
    }

    public function getColla(): Colla
    {
        return $this->getAttribute('colla_id');
    }
}
