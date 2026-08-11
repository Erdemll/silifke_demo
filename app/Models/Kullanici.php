<?php

namespace App\Models;

use Database\Factories\KullaniciFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ad
 * @property string $soyad
 * @property string $mail
 * @property string $sifre
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('kullanicilar')]
#[Fillable(['ad', 'soyad', 'mail', 'sifre'])]
#[Hidden(['sifre'])]
class Kullanici extends Authenticatable
{
    /** @use HasFactory<KullaniciFactory> */
    use HasFactory;

    /**
     * The password column used by the customer guard.
     *
     * @var string
     */
    protected $authPasswordName = 'sifre';

    /**
     * Customers do not use persistent remember tokens in this demo.
     *
     * @var string
     */
    protected $rememberTokenName = '';

    public function yorumlar(): HasMany
    {
        return $this->hasMany(Yorum::class);
    }

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
            'sifre' => 'hashed',
        ];
    }
}
