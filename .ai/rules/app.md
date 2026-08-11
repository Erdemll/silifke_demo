---
paths:
  - 'app/**'
---

# App

## Keep admin and customer identities separate
The existing App\Models\User and Fortify session flow represent demo administrators. App\Models\Kullanici represents storefront customers used by commerce metrics and order relations. Protect admin pages with the auth and verified middleware; do not treat Kullanici records as admin accounts.

## Keep product images and order history consistent
Products must retain between 1 and 8 JPEG, PNG, or WebP images stored on the public disk under urunler. Update and delete flows must remove obsolete files as well as database rows. Products referenced by order history must not be deleted.

## Resolve product image URLs by storage source
Keep urun_resimler.yol relative. Paths under seeder_resimler are static files in public/seeder_resimler and must use the UrunResim url accessor with asset(). Uploaded paths under urunler remain on the public disk and must resolve through Storage::disk('public')->url(). Build Inertia image payloads from the accessor instead of duplicating URL logic.
