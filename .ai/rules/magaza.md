---
paths:
  - 'app/Http/Controllers/Magaza/**'
---

# Magaza

## Build public product detail data without lazy loading
Product detail responses include ordered product images, newest-first comments with customer names, and at most four random products excluding the current product. Eager-load every relationship before mapping the Inertia payload.
