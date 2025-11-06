User clicks "Add to Cart"
    ↓
JavaScript serializes form → AJAX POST to cart_add.php
    ↓
cart_add.php validates user login
    ↓
Cart class fetches product + addons from database
    ↓
Generates unique cart key (product_id + sorted addon IDs)
    ↓
Stores/updates item in $_SESSION['cart']
    ↓
Returns JSON with cart_count, cart_total, items
    ↓
JavaScript updates mini cart badge, items list, total price
    ↓
User sees updated cart dropdown