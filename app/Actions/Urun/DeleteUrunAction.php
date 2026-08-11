<?php

namespace App\Actions\Urun;

use App\Models\Urun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteUrunAction
{
    public function handle(Urun $urun): void
    {
        $resimYollari = $urun->resimler()->pluck('yol')->all();

        DB::transaction(function () use ($urun): void {
            Urun::query()
                ->whereKey($urun->getKey())
                ->lockForUpdate()
                ->firstOrFail()
                ->delete();
        });

        Storage::disk('public')->delete($resimYollari);
    }
}
