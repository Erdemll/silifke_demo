---
paths:
  - config/fortify.php
  - config/auth.php
---

# Config

## Keep public registration disabled
This demo has one administrator authenticated through the existing User model. Keep Fortify public registration disabled so visitors cannot create accounts and gain access to the admin dashboard.

## Use a separate customer session guard
Keep Fortify on the web guard for the single administrator. Storefront customers authenticate through the musteri session guard and the Kullanici provider; their public auth endpoints are /giris, /kayit, and /cikis.
