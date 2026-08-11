<?php

namespace App\Models;

use Database\Factories\UrunResimFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $urun_id
 * @property string $yol
 * @property-read Urun $urun
 */
#[Table('urun_resimler')]
#[Fillable(['urun_id', 'yol'])]
class UrunResim extends Model
{
    /** @use HasFactory<UrunResimFactory> */
    use HasFactory;

    /** @return BelongsTo<Urun, $this> */
    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class);
    }
}
