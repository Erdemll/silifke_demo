---
paths:
  - routes/web.php
---

# Routes

## Keep the storefront catalog public
The home catalog and product detail routes are public so visitors can browse without a customer session. Customer-only commerce actions should use the musteri guard; admin management remains under web auth.
