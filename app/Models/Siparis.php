<?php

namespace App\Models;

use Database\Factories\SiparisFactory;
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
 * @property string $fiyat
 * @property string|null $kargo_kodu
 * @property Carbon $tarih
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Urun $urun
 * @property-read Kullanici $kullanici
 */
#[Table('siparisler')]
#[Fillable(['urun_id', 'kullanici_id', 'fiyat', 'kargo_kodu', 'tarih'])]
class Siparis extends Model
{
    /** @use HasFactory<SiparisFactory> */
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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fiyat' => 'decimal:2',
            'tarih' => 'datetime',
        ];
    }
}
