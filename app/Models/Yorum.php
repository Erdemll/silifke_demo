<?php

namespace App\Models;

use Database\Factories\YorumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $urun_id
 * @property int $kullanici_id
 * @property string $metin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Urun $urun
 * @property-read Kullanici $kullanici
 */
#[Table('yorumlar')]
#[Fillable(['urun_id', 'kullanici_id', 'metin'])]
class Yorum extends Model
{
    /** @use HasFactory<YorumFactory> */
    use HasFactory;

    /** @return BelongsTo<Urun, $this> */
    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class);
    }

    /** @return BelongsTo<Kullanici, $this> */
    public function kullanici(): BelongsTo
    {
        return $this->belongsTo(Kullanici::class);
    }
}
