<?php

namespace App\Models;

use Database\Factories\UrunFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $ad
 * @property string $aciklama
 * @property string $fiyat
 * @property-read UrunResim|null $ilkResim
 * @property-read Collection<int, UrunResim> $resimler
 * @property-read Collection<int, Yorum> $yorumlar
 * @property-read Collection<int, Siparis> $siparisler
 */
#[Table('urunler')]
#[Fillable(['ad', 'aciklama', 'fiyat'])]
class Urun extends Model
{
    /** @use HasFactory<UrunFactory> */
    use HasFactory;

    /** @return HasMany<UrunResim, $this> */
    public function resimler(): HasMany
    {
        return $this->hasMany(UrunResim::class);
    }

    /** @return HasOne<UrunResim, $this> */
    public function ilkResim(): HasOne
    {
        return $this->hasOne(UrunResim::class)->oldestOfMany();
    }

    /** @return HasMany<Yorum, $this> */
    public function yorumlar(): HasMany
    {
        return $this->hasMany(Yorum::class);
    }

    /** @return HasMany<Siparis, $this> */
    public function siparisler(): HasMany
    {
        return $this->hasMany(Siparis::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fiyat' => 'decimal:2',
        ];
    }
}
