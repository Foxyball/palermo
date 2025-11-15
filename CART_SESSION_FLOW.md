# Cart Session Flow Documentation

## Overview
This document describes the complete cart session logic flow in the Palermo e-commerce system, from adding items to the mini cart through order completion.

---

## Architecture

### Session Storage
- **Storage Mechanism**: PHP `$_SESSION['cart']` array
- **Data Structure**: Associative array where keys are unique cart keys and values are item details
- **Initialization**: Session initialized in `Cart::__construct()` → `Cart::initialize()`

### Cart Key Generation
Cart items are uniquely identified using a composite key format:
```
{product_id}_{sorted_addon_ids}
```

**Example:**
- Product ID: 5, No addons → Key: `5_`
- Product ID: 5, Addon IDs: [3, 1] → Key: `5_1-3` (sorted)

**Location:** `Cart::generateCartKey()` in `/include/Cart.php` (line 169-175)

---

## Complete Flow: From Product Page to Order

### Phase 1: Product Page - User Interaction

#### 1.1 Product Detail Page Load
**File:** `/product-detail.php`

1. User navigates to product page (e.g., `/art/{slug}`)
2. System fetches product details from database:
   ```php
   $productRepository = new ProductRepository($pdo);
   $product = $productRepository->getBySlug($slug);
   $addons = $productRepository->getProductAddons($product['id']);
   ```
3. Page displays:
   - Product image, name, description
   - Base price in BGN and EUR
   - Available addons (checkboxes)
   - Quantity selector (1-99)
   - "Add to Cart" button

#### 1.2 User Customizes Product
**File:** `/js/product-detail.js`

1. User adjusts quantity using +/- buttons or direct input (lines 31-62)
2. User selects addons by checking checkboxes (line 65-67)
3. JavaScript calculates total price in real-time:
   ```javascript
   totalPrice = basePrice * quantity + (addon prices * quantity)
   ```
4. Price displayed in both BGN and EUR (lines 11-28)

#### 1.3 User Clicks "Add to Cart"
**File:** `/js/product-detail.js` (lines 70-100)

1. Form submission intercepted by JavaScript (line 70)
2. Form data serialized:
   ```javascript
   {
     product_id: 5,
     quantity: 2,
     addons: [1, 3]
   }
   ```
3. Button disabled and shows loading state
4. AJAX POST request sent to `include/cart_add.php`

---

### Phase 2: Server-Side Cart Addition

#### 2.1 Request Validation
**File:** `/include/cart_add.php` (lines 1-25)

1. Session started: `session_start()`
2. Request method validated (must be POST)
3. User authentication checked:
   ```php
   if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
       return ['success' => false, 'redirect' => 'login'];
   }
   ```
4. Input parameters extracted and sanitized

#### 2.2 Cart Processing
**File:** `/include/Cart.php` → `Cart::add()` method (lines 25-66)

**Step-by-step execution:**

1. **Validate quantity** (line 27-29)
   - Must be between 1 and 99
   - Product ID must be positive

2. **Fetch product data** (lines 31-36)
   ```php
   $productRepo->getByIdForCart($productId);
   ```
   Returns: `id`, `name`, `slug`, `image`, `price`

3. **Fetch addon details** if addons selected (lines 38-44)
   ```php
   $addonDetails = $productRepo->getAddonsByIds($addons);
   ```
   For each addon: `id`, `name`, `price`

4. **Calculate addon total price** (lines 152-161)
   ```php
   $addonsTotal = sum of all addon prices
   ```

5. **Generate unique cart key** (line 46)
   ```php
   $cartKey = $productId . '_' . implode('-', sorted($addonIds));
   ```

6. **Calculate item price** (line 47)
   ```php
   $itemPrice = $product['price'] + $addonsTotal;
   ```

7. **Store in session** (lines 49-63)
   
   **If item already exists** (same product + same addons):
   ```php
   $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
   ```
   
   **If new item:**
   ```php
   $_SESSION['cart'][$cartKey] = [
       'product_id' => $productId,
       'name' => $product['name'],
       'slug' => $product['slug'],
       'image' => $product['image'],
       'price' => $product['price'],           // Base price only
       'addons' => $addonDetails,              // Array of addon objects
       'addons_total' => $addonsTotal,         // Sum of addon prices
       'item_price' => $itemPrice,             // Base + addons
       'quantity' => $quantity
   ];
   ```

#### 2.3 Response Generation
**File:** `/include/cart_add.php` (lines 34-42)

1. Cart data retrieved:
   ```php
   $cartData = $cart->getData();
   ```
   
2. JSON response sent:
   ```json
   {
     "success": true,
     "message": "Product added to cart!",
     "cart_count": 5,
     "cart_total": 125.50,
     "items": [...]
   }
   ```

---

### Phase 3: Mini Cart Update

#### 3.1 JavaScript Response Handling
**File:** `/js/product-detail.js` (lines 79-99)

1. Success response received
2. Success alert displayed: `PalermoCart.showAlert()`
3. Mini cart refreshed: `PalermoCart.refresh()`

#### 3.2 Mini Cart Refresh
**File:** `/js/cart.js` → `PalermoCart.refresh()` (lines 46-60)

1. AJAX GET request to `include/cart_data.php`
2. Cart data fetched from session
3. Display updated via `updateDisplay(response)`

#### 3.3 Display Updates
**File:** `/js/cart.js` (lines 91-159)

**Three updates occur:**

1. **Update badge counter** (lines 97-104)
   - Shows total quantity of all items
   - Hidden if cart is empty

2. **Update items list** (lines 106-119)
   - HTML generated for each item (lines 121-155)
   - Shows: image, name, addons, quantity × price
   - Remove button with cart key

3. **Update total price** (lines 157-165)
   - Displays in BGN and EUR
   - Format: `125.50 лв / 64.19 €`

---

### Phase 4: Cart Page

#### 4.1 Cart Page Access
**File:** `/cart.php`

User clicks "View Cart" or navigates to `/cart`

**Data Loading:**
```php
$cart = new Cart($pdo);
$cartData = $cart->getData();
$items = $cartData['items'];
$cartTotal = $cartData['cart_total'];
$cartCount = $cartData['cart_count'];
```

**getData() method** (lines 131-140 in Cart.php):
1. Calls `getTotals()` for count and total
2. Calls `getItems()` to get enriched item array
3. Each item includes `key` and `item_total`

#### 4.2 Cart Operations

**Update Quantity:**
**File:** `/js/cart-page.js` + `/include/cart_update.php`

1. User clicks +/- buttons
2. AJAX POST to `cart_update.php`:
   ```json
   {
     "cart_key": "5_1-3",
     "quantity": 3
   }
   ```
3. Session updated:
   ```php
   $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
   ```

**Remove Item:**
**File:** `/include/cart_remove.php`

1. User clicks "Remove"
2. AJAX POST with cart_key
3. Session updated:
   ```php
   unset($_SESSION['cart'][$cartKey]);
   ```

---

### Phase 5: Checkout

#### 5.1 Checkout Page Load
**File:** `/checkout.php`

**Prerequisites:**
1. User must be logged in
2. Cart must not be empty (redirects if empty)

**Data Displayed:**
```php
$userId = $_SESSION['user_id'];
$userFirstName = $_SESSION['user_first_name'];
$userEmail = $_SESSION['user_email'];
// Pre-fills customer information
```

**Order Summary:**
- All cart items with images
- Addons listed per item
- Quantity × unit price
- Subtotal and total in BGN/EUR

#### 5.2 Order Submission
**File:** `/js/checkout.js` (lines 3-52)

1. User fills required fields:
   - Order address
   - Phone number
   - Optional message

2. Form validated client-side
3. AJAX POST to `include/process_order.php`

---

### Phase 6: Order Processing

#### 6.1 Order Creation
**File:** `/include/process_order.php`

**Validation:**
1. Check POST request
2. Verify user logged in
3. Validate required fields
4. Verify cart not empty

**Process Flow:**

1. **Retrieve cart data** (lines 42-43)
   ```php
   $cart = new Cart($pdo);
   $cartData = $cart->getData();
   ```

2. **Create order in database** (line 58)
   ```php
   $orderId = $orderRepo->createOrder(
       $userId,
       $totalAmount,
       $items,
       $orderAddress,
       $orderPhone,
       $message
   );
   ```

#### 6.2 Database Transaction
**File:** `/repositories/frontend/OrderProcessingRepository.php`

**Transaction begins** (line 20):

1. **Insert order record** (lines 49-68)
   ```sql
   INSERT INTO orders (
       user_id, amount, status_id, message, 
       order_address, order_phone, created_at
   ) VALUES (?, ?, 1, ?, ?, ?, NOW())
   ```
   - status_id = 1 (default: "Pending")
   - Returns: `order_id`

2. **Insert order items** (lines 26-32, 71-88)
   
   For each item in cart:
   ```sql
   INSERT INTO order_items (
       order_id, product_id, unit_price, qty, subtotal
   ) VALUES (?, ?, ?, ?, ?)
   ```
   - unit_price: `item_price` (base + addons)
   - subtotal: `unit_price × quantity`
   - Returns: `order_item_id`

3. **Insert order item addons** (lines 29-31, 90-104)
   
   For each addon in item:
   ```sql
   INSERT INTO order_item_addons (
       order_item_id, addon_id, price
   ) VALUES (?, ?, ?)
   ```

**Transaction commits** (line 34)
- All or nothing approach
- Rollback on any error

#### 6.3 Post-Order Actions
**File:** `/include/process_order.php` (lines 60-93)

1. **Clear cart** (line 60)
   ```php
   $cart->clear(); // $_SESSION['cart'] = []
   ```

2. **Send confirmation email** (lines 62-86)
   - Generate HTML email template
   - Send to user's email
   - Includes order ID, items, address

3. **Return success response** (lines 88-93)
   ```json
   {
     "success": true,
     "message": "Order placed successfully",
     "order_id": 123,
     "redirect": "thank-you"
   }
   ```

---

### Phase 7: Order Confirmation

#### 7.1 Thank You Page
**File:** `/thank-you.php`

1. User redirected to thank-you page
2. Displays success message
3. Link to view orders in account

#### 7.2 Session State
- `$_SESSION['cart']` is now empty `[]`
- Mini cart shows 0 items
- Order data persisted in database tables:
  - `orders`
  - `order_items`
  - `order_item_addons`

---

## Data Flow Summary

### Session Data Structure

```php
$_SESSION['cart'] = [
    '5_1-3' => [                    // Cart Key
        'product_id' => 5,
        'name' => 'Margherita Pizza',
        'slug' => 'margherita-pizza',
        'image' => 'images/products/pizza.jpg',
        'price' => 15.00,            // Base price only
        'addons' => [
            [
                'id' => 1,
                'name' => 'Extra Cheese',
                'price' => 2.00
            ],
            [
                'id' => 3,
                'name' => 'Olives',
                'price' => 1.50
            ]
        ],
        'addons_total' => 3.50,      // 2.00 + 1.50
        'item_price' => 18.50,       // 15.00 + 3.50
        'quantity' => 2
    ],
    '7_' => [                        // Product without addons
        'product_id' => 7,
        'name' => 'Caesar Salad',
        'slug' => 'caesar-salad',
        'image' => 'images/products/salad.jpg',
        'price' => 12.00,
        'addons' => [],
        'addons_total' => 0.00,
        'item_price' => 12.00,
        'quantity' => 1
    ]
];
```

### Database Schema

**orders table:**
```
order_id | user_id | amount | status_id | order_address | order_phone | message | created_at
---------|---------|--------|-----------|---------------|-------------|---------|------------
123      | 45      | 49.00  | 1         | 123 Main St   | 555-1234    | null    | 2024-01-15
```

**order_items table:**
```
id  | order_id | product_id | unit_price | qty | subtotal
----|----------|------------|------------|-----|----------
301 | 123      | 5          | 18.50      | 2   | 37.00
302 | 123      | 7          | 12.00      | 1   | 12.00
```

**order_item_addons table:**
```
id  | order_item_id | addon_id | price
----|---------------|----------|-------
501 | 301           | 1        | 2.00
502 | 301           | 3        | 1.50
```

---

## Key Implementation Details

### 1. Cart Key Strategy
**Why unique keys matter:**
- Same product with different addons = different cart items
- Prevents addon conflicts
- Enables proper quantity management

**Example Scenario:**
```
User adds: Pizza + Cheese → Key: '5_1'
User adds: Pizza + Olives → Key: '5_3'
Result: 2 separate cart items (correct)

User adds: Pizza + Cheese → Key: '5_1'
User adds: Pizza + Cheese → Key: '5_1'
Result: Quantity incremented on same item (correct)
```

### 2. Price Calculation
**Item Price Formula:**
```
item_price = base_price + sum(addon_prices)
item_total = item_price × quantity
cart_total = sum(all item_totals)
```

**Stored vs Calculated:**
- `price`: Base price (stored)
- `addons_total`: Sum of addon prices (stored)
- `item_price`: Combined price (stored)
- `item_total`: Extended price (calculated on-the-fly)
- `cart_total`: Grand total (calculated on-the-fly)

### 3. Currency Display
**Conversion Rate:**
```php
define('BGN_TO_EUR_RATE', 1.95583);
```

**Display Format:**
```
{price_bgn} лв / {price_eur} €
Example: 49.00 лв / 25.05 €
```

### 4. Quantity Constraints
```php
const MIN_QUANTITY = 1;
const MAX_QUANTITY = 99;
```

### 5. User Authentication
**Cart operations require login:**
- Adding items: Login required
- Viewing cart: Login required
- Checkout: Login required
- Mini cart display: Available to all (shows 0 if not logged in)

---

## File Reference Map

### Core Cart Files
| File | Purpose |
|------|---------|
| `/include/Cart.php` | Main Cart class - session management |
| `/include/cart_add.php` | AJAX endpoint - add to cart |
| `/include/cart_data.php` | AJAX endpoint - get cart data |
| `/include/cart_update.php` | AJAX endpoint - update quantity |
| `/include/cart_remove.php` | AJAX endpoint - remove item |
| `/include/process_order.php` | Process checkout and create order |

### Frontend Files
| File | Purpose |
|------|---------|
| `/cart.php` | Cart page view |
| `/checkout.php` | Checkout page view |
| `/product-detail.php` | Product detail page |
| `/thank-you.php` | Order confirmation page |

### JavaScript Files
| File | Purpose |
|------|---------|
| `/js/cart.js` | PalermoCart module - cart operations |
| `/js/product-detail.js` | Product page interactions |
| `/js/cart-page.js` | Cart page interactions |
| `/js/checkout.js` | Checkout form handling |
| `/js/js-cart-palermo.js` | Mini cart dropdown UI |

### Repository Files
| File | Purpose |
|------|---------|
| `/repositories/frontend/ProductRepository.php` | Product data access |
| `/repositories/frontend/OrderProcessingRepository.php` | Order creation |

---

## Example Use Case: Complete Flow

**Scenario:** Customer orders 2 Pizzas with Extra Cheese and 1 Salad

### 1. Add First Item
```
Action: Add to cart (Pizza + Cheese, qty 2)
→ POST include/cart_add.php
→ Cart::add(productId: 5, quantity: 2, addons: [1])
→ Key generated: '5_1'
→ $_SESSION['cart']['5_1'] created
→ item_price = 15.00 + 2.00 = 17.00
→ Response: {cart_count: 2, cart_total: 34.00}
→ Mini cart updated
```

### 2. Add Second Item
```
Action: Add to cart (Salad, qty 1)
→ POST include/cart_add.php
→ Cart::add(productId: 7, quantity: 1, addons: [])
→ Key generated: '7_'
→ $_SESSION['cart']['7_'] created
→ item_price = 12.00
→ Response: {cart_count: 3, cart_total: 46.00}
→ Mini cart updated
```

### 3. View Cart
```
Action: Click "View Cart"
→ Navigate to /cart.php
→ $cart->getData()
→ Display 2 items, total: 46.00 лв / 23.52 €
```

### 4. Proceed to Checkout
```
Action: Click "Proceed to Checkout"
→ Navigate to /checkout.php
→ Verify user logged in
→ Verify cart not empty
→ Display order summary
```

### 5. Place Order
```
Action: Submit checkout form
→ POST include/process_order.php
→ Transaction begins
→ INSERT orders (order_id: 123, amount: 46.00)
→ INSERT order_items (id: 301, product_id: 5, qty: 2, subtotal: 34.00)
→ INSERT order_item_addons (order_item_id: 301, addon_id: 1)
→ INSERT order_items (id: 302, product_id: 7, qty: 1, subtotal: 12.00)
→ Transaction commits
→ Cart cleared: $_SESSION['cart'] = []
→ Email sent
→ Response: {redirect: "thank-you"}
```

### 6. Confirmation
```
Action: Redirect to thank-you page
→ Display success message
→ Cart empty
→ Order saved in database
```

---

## Session Lifecycle

```
Page Load → Session Start → Check Auth → Load Cart Data
                                             ↓
                                        $_SESSION['cart']
                                             ↓
Add Item → Validate → Fetch Product → Generate Key → Store in Session
                                                           ↓
                                                      Update Display
                                                           ↓
View Cart → Retrieve Session Data → Display Items
                                         ↓
Checkout → Validate Cart → Process Order → DB Transaction
                                                 ↓
                                            Clear Session
                                                 ↓
                                          Order Complete
```

---

## Security Considerations

1. **Authentication**: All cart operations require valid user session
2. **Input Validation**: Product IDs, quantities, and addon IDs validated
3. **SQL Injection**: Prepared statements used throughout
4. **XSS Prevention**: Output escaped with `htmlspecialchars()`
5. **CSRF**: Form submissions use POST with session validation
6. **Transaction Safety**: Database rollback on errors

---

## Error Handling

### Client-Side
- Invalid quantity → Constrained to 1-99
- Empty cart → Redirect to cart page
- Network errors → Alert displayed

### Server-Side
- Invalid product → `add()` returns false
- Database errors → Transaction rollback
- Empty cart at checkout → Redirect with message
- Missing required fields → Validation error returned

---

## Performance Optimizations

1. **Session Storage**: Fast in-memory cart access
2. **Minimal Database Queries**: Products fetched only when adding
3. **AJAX Updates**: No page reloads for cart operations
4. **Calculated Fields**: Totals computed on-the-fly, not stored
5. **Transaction Batching**: All order inserts in single transaction

---

## Maintenance Notes

### Adding New Features

**To add a new cart field:**
1. Update `Cart::add()` to store new field
2. Update `$_SESSION['cart']` structure
3. Update display logic in JavaScript
4. Update order processing if needed

**To modify pricing:**
1. Update calculation in `Cart::add()`
2. Update JavaScript price calculation
3. Ensure database schema matches

### Common Issues

**Cart not updating:**
- Check session started
- Verify AJAX endpoint paths
- Check JavaScript console for errors

**Order not creating:**
- Check database transaction logs
- Verify foreign key constraints
- Check required fields validation

**Price mismatch:**
- Verify addon prices fetched correctly
- Check currency conversion rate
- Ensure all calculations use float types

---

## Conclusion

The Palermo cart system uses a session-based approach with database persistence at checkout. The flow is:

1. **Product Selection** → User chooses items with addons
2. **Session Storage** → Items stored in `$_SESSION['cart']`
3. **Real-time Updates** → AJAX keeps mini cart synchronized
4. **Cart Management** → Users can update quantities and remove items
5. **Checkout** → User provides delivery information
6. **Order Processing** → Database transaction creates order records
7. **Session Clear** → Cart emptied after successful order
8. **Confirmation** → User sees thank you page, receives email

This design provides a smooth user experience while maintaining data integrity through transactions and proper validation.
