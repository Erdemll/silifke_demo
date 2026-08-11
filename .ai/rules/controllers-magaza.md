---
paths:
  - 'app/Actions/Magaza/**,app/Http/Controllers/Magaza/**,routes/web.php'
  - 'app/Actions/Magaza/GetSiparislerimAction.php,app/Http/Controllers/Magaza/SiparislerimController.php,routes/web.php'
---

# Controllers Magaza

## Keep cart temporary and checkout authoritative
Store the demo cart in the Laravel session as product ID to quantity only. Keep cart viewing and adding public, but require the musteri guard for checkout. At checkout reload product prices, create one Siparis row per unit inside a transaction with a unique random shipping code, and clear the cart only after success.

## Scope order history to the signed-in customer
The storefront order-history route uses the musteri guard. Query orders only with whereBelongsTo the authenticated Kullanici, sort by tarih then id descending, eager-load product imagery, and paginate ten rows per page.
