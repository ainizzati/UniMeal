# Title: UNIMEAL
## GROUP MEMBERS:
1. __NUR SAFIAH ASHIQIN BINTI SHUHANIZAL__ and __2317618__
2. __NURAIN IZZATI BINTI ABD RAUF__ and __2217978__
3. __NURSYAZIRA BINTI MOHD NAIM__ and __2214076__
4. __NUR RAIHAN SYAZWANI BINTI SUHAIMI__ and __2213262__
5. __NUR ADLINA NAJWA BINTI ROSLI__ and __2213362__

## 1.0 Introduction

Food service is one of the important criteria for having a comfortable life as a university student. Throughout the time, the increasing number of students in IIUM had addressed the new problem of the efficiency and services of cafeteria food on the campus. Therefore, UniMeal is introduced to solve this problem. A redesigned web application called UniMeal gathers student orders for food and displays the menus from several cafeterias around IIUM. The main target audience of this web application is IIUM students and also the Mahallah Cafeteria. Students will have better food services to buy food, and the Mahallah Cafeteria will also get the benefit of having a lot of online orders. UniMeal's main goal is to give students access to online apps that will enhance the effectiveness of food services at IIUM in the future.


## 2.0 Objectives
The primary objective of the UniMeal's security enhancement is:
1. To improve input handling and validation
2. To strengthen authentication mechanisms
3. To enforce proper authorization and access control
4. To prevent client-side and server-side attacks such as XSS and CSRF
5. To secure database interactions and prevent SQL injection
6. To enhance file security and prevent unauthorized file access or leakage
7. To align the web application with industry best practices for web application security.


## 3.0 Features and Functionalities
The UniMeal web application aims to deliver an efficient, user-friendly, and centralized platform for food ordering across IIUM campus cafeterias. The key features and functionalities are as follows:

1. User Registration and Login
   - Secure user authentication system for students and cafeteria vendors.
   - User roles: Student (customer) and Vendor (cafeteria admin).

2. Cafeteria and Menu Browsing
   - Browse and view a list of all available cafeterias within the IIUM campus.
   - Access to daily menus, prices, food images, and descriptions.

3. Food Ordering System
   - Add items to the cart and place food orders directly from the selected cafeteria.
   - Real-time availability and estimated preparation time display.

4. Payment Integration
   - Multiple payment options: cash on pickup, online banking, or e-wallet integration.
   - Order receipt generation for records.

5. Order Tracking
   - Live status updates: Order placed → Preparing → Ready for pickup..

6) Cafeteria Dashboard (Vendor Panel)
   - Cafeteria vendors can manage their menus and receive orders.

7) Responsive Design
   - Optimised for desktop for easy access on a laptop.
  
## Web Application Security Enhancements
## i. Input Validation
Input validation is the first line of defense against various web application attacks including SQL injection, XSS, command injection, and business logic exploitation. Both client-side and server-side validation were implemented across all user input points to ensure data integrity and security.

__1. Registation Form Validation__

Implemented Security Controls:

__Client-Side Validation (register_student.blade.php):__

- Real-time password strength checking using JavaScript
- Password confirmation matching before form submission
- Immediate user feedback on validation errors

<img width="1280" height="732" alt="IV1" src="https://github.com/user-attachments/assets/97eddd47-8a73-4f39-957b-b65b071f4dc8" />


__Server-Side Validation (StudentAuthController):__
- Matric Number: Integer type, must be unique in database
- Name: String, maximum 255 characters, regex pattern to prevent HTML tags (/^[^<>]*$/)
- Email: Valid email format, must be unique in database
- Password:
   - Minimum 10 characters
   - Must contain at least one lowercase letter
   - Must contain at least one uppercase letter
   - Must contain at least one digit
   - Must contain at least one special character (@$!%*?&#)
   - Regex pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/
- Password Confirmation: Must match password field

Code Snippet:

```
public function register(Request $request)
{
    $request->validate([
        'matric_no' => 'required|integer|unique:students,matric_no',
        'name' => 'required|string|max:255|regex:/^[^<>]*$/',
        'email' => 'required|email|unique:students,email',
        'password' => [
            'required',
            'string',
            'min:10',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
        ],
    ]);
}
```



__Client-Side JavaScript Validation:__

```
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="password_confirmation"]').value;

    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{10,}$/;

    if (!regex.test(password)) {
        e.preventDefault();
        alert('Password must be at least 10 characters with uppercase, lowercase, number, and special character.');
        return false;
    }

    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
});
```



__2. Login Form Validation__

The login endpoint (/login) handles authentication credentials and must validate input to prevent injection attacks and ensure proper data types.


Implemented Security Controls:
- Email: Required, must be valid email format
- Password: Required, must be string type

Code Snippet:
```
   public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);
}
```



__3. Cart Input Validation__

The cart functionality handles product data that could be manipulated to exploit pricing or inject malicious content.


__Add to Cart Validation (CartController::add)__

Implemented Security Controls:

- Product Name: Required, string, maximum 255 characters
- Price: Required, numeric, minimum 0, maximum 9999.99
- Image Path: Required, string, maximum 500 characters
- Price Sanitization: Uses floatval() with regex to strip non-numeric characters

Code Snippet:

```
   public function add(Request $request)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'price' => 'required|numeric|min:0|max:9999.99',
         'image' => 'required|string|max:500',
      ]);

      // Convert price to float safely
      $price = floatval(preg_replace('/[^0-9.]/', '', $request->price));

      $cart[] = [
         'name' => $validated['name'],
         'price' => $validated['price'],
         'image' => $validated['image'],
         'quantity' => 1,
      ];
   }
```



__Update Cart Quantity Validation (CartController::update)__

Implemented Security Controls:

- Action Parameter: Required, string, must be either "increase" or "decrease"
- Index Parameter: Type-cast to integer, must be non-negative and within cart bounds

Code Snippet:
```
      public function update(Request $request, $index)
   {
      // Validate action parameter
      $validated = $request->validate([
         'action' => 'required|string|in:increase,decrease',
      ]);

      // Validate and type-cast index
      $index = (int) $index;

      if ($index < 0) {
         abort(400);
      }

      $cart = Session::get('cart', []);

      if (!isset($cart[$index])) {
         abort(404);
      }

      // Use validated action
      if ($validated['action'] === 'increase') {
         $cart[$index]['quantity'] += 1;
      } elseif ($validated['action'] === 'decrease' && $cart[$index]['quantity'] > 1) {
         $cart[$index]['quantity'] -= 1;
      }
   }
```



__Remove Cart Item Validation (CartController::remove)__

Implemented Security Controls:

- Index Bounds Checking: Validates index is within valid range before removal
- Type Casting: Converts index to integer to prevent type juggling attacks

Code Snippet:

```
   public function remove($index)
{
    $index = (int) $index;
    $cart = Session::get('cart', []);
    
    // Add bounds checking
    if ($index < 0 || $index >= count($cart)) {
        abort(404, 'Invalid cart item');
    }

    if (!isset($cart[$index])) {
        abort(404, 'Cart item not found');
    }
    
    unset($cart[$index]);
}
```



__4. Checkout Form Validation__

The checkout process handles sensitive shipping and payment information requiring comprehensive validation.



__Shipping Information Validation (CheckoutController::process)__
Implemented Security Controls:

Name:
- Required, string, maximum 255 characters
- Regex pattern to allow only letters and spaces: /^[a-zA-Z\s]+$/


Phone Number:
- Required, string
- Regex pattern for Malaysian phone format: /^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/
- Custom error message for user guidance


Address:
- Required, string, maximum 500 characters
- Regex pattern to prevent HTML tags: /^[^<>]*$/


Cart Quantities:
- Required, integer
- Minimum 1, maximum 100 per item

Code Snippet

```
public function process(Request $request)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-zA-Z\s]+$/',
        ],
        'phone' => [
            'required',
            'string',
            'regex:/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/',
        ],
        'address' => 'required|string|max:500|regex:/^[^<>]*$/',
        'cart.*.quantity' => 'required|integer|min:1|max:100',
    ], [
        'phone.regex' => 'Please make sure the phone number is in Malaysian Phone Format (e.g., 012-3456789 or +6012-3456789).',
    ]);
}
```



__Delivery Option Validation (CheckoutController::processDelivery)__

Implemented Security Controls:

- Delivery Option: Required, string, must be one of: "Pick Up", "15 - 20 Minutes", "Now"
- Server-Side Fee Calculation: Delivery fee calculated server-side instead of trusting client input

Code Snippet:
```
   public function processDelivery(Request $request)
{
    $validated = $request->validate([
        'delivery_option' => 'required|string|in:Pick Up,15 - 20 Minutes,Now',
    ]);

    // Calculate fee server-side based on option - don't trust client
    $deliveryFees = [
        'Pick Up' => 0.00,
        '15 - 20 Minutes' => 3.00,
        'Now' => 5.00,
    ];

    $deliveryFee = $deliveryFees[$validated['delivery_option']];
    Session::put('shipping_fee', $deliveryFee);
}
```



__Payment Method Validation (CheckoutController::processPayment)__

Implemented Security Controls:

- Payment Method: Required, string, must be one of: "cash", "credit_card", "bank_transfer", "other"
- Server-Side Total Recalculation: All monetary values recalculated server-side
- Cart Item Validation:
   - Validates item structure (price, quantity, name fields exist)
   - Validates positive prices and quantities
   - Enforces maximum quantity of 100 per item
   - Validates reasonable total (between 0 and 10,000)

Code Snippet:

```
public function processPayment(Request $request)
{
    // Validate payment method only - don't accept amounts from client
    $validated = $request->validate([
        'payment_method' => 'required|string|in:cash,credit_card,bank_transfer,other',
    ]);

    // Recalculate everything server-side
    $deliveryFees = [
        'Pick Up' => 0.00,
        '15 - 20 Minutes' => 3.00,
        'Now' => 5.00,
    ];

    $deliveryOption = Session::get('delivery_option');
    
    if (!array_key_exists($deliveryOption, $deliveryFees)) {
        Log::error('Invalid delivery option in payment', [
            'user' => $user->matric_no,
            'option' => $deliveryOption
        ]);
        return redirect()->route('checkout.delivery')->with('error', 'Invalid delivery option.');
    }

    $shippingFee = $deliveryFees[$deliveryOption];
    
    // Recalculate cart totals and validate items
    $subtotal = 0;
    foreach ($cart as $item) {
        // Validate each item structure
        if (!isset($item['price']) || !isset($item['quantity']) || !isset($item['name'])) {
            Log::error('Invalid cart item structure', [
                'user' => $user->matric_no,
                'item' => $item
            ]);
            return redirect()->route('cart.show')->with('error', 'Invalid cart data.');
        }
        
        // Validate positive numbers
        if ($item['price'] <= 0 || $item['quantity'] < 1) {
            Log::error('Invalid cart item values', [
                'user' => $user->matric_no,
                'item' => $item
            ]);
            return redirect()->route('cart.show')->with('error', 'Invalid item prices or quantities.');
        }

        // Validate reasonable quantity
        if ($item['quantity'] > 100) {
            Log::warning('Excessive quantity in cart', [
                'user' => $user->matric_no,
                'item' => $item
            ]);
            return redirect()->route('cart.show')->with('error', 'Quantity per item cannot exceed 100.');
        }
        
        $subtotal += $item['price'] * $item['quantity'];
    }

    // Calculate tax and total
    $salesTax = round($subtotal * 0.065, 2);
    $orderTotal = round($subtotal + $salesTax + $shippingFee, 2);

    // Validate amounts are reasonable
    if ($orderTotal <= 0 || $orderTotal > 10000) {
        Log::error('Suspicious order total', [
            'user' => $user->matric_no,
            'total' => $orderTotal
        ]);
        return redirect()->route('cart.show')->with('error', 'Order total is invalid.');
    }

    // Create order with validated, server-calculated values
    Order::create([
        'student_id' => $user->matric_no,
        'total_amount' => $orderTotal, // Server-calculated
        'payment_method' => $validated['payment_method'],
        'shipping_fee' => $shippingFee, // Server-calculated
        'sales_tax' => $salesTax, // Server-calculated
        // ... other fields
    ]);
}
```



__5. Mahallah Selection Validation__

The mahallah (cafeteria) selection endpoint accepts user input that determines which menu to display and must be validated to prevent path traversal and injection attacks.
5.1 Mahallah Name Whitelist Validation

Implemented Security Controls:

- Whitelist Validation: Only accepts predefined mahallah names
- Case-Insensitive Matching: Converts to lowercase before validation
- 404 Response: Returns 404 for invalid mahallah names instead of exposing error details

Code Snippets:
```
public function show(Request $request, $mahallah)
{
    // Whitelist validation
    $allowed = ['siddiq', 'aminah', 'ruqayyah', 'halimah', 'hafsa', 'bilal'];

    if (!in_array(strtolower($mahallah), $allowed)) {
        abort(404);
    }
    
    // Safe to use after validation
    return view('student.food', [
        'mahallah' => ucfirst($mahallah),
        'logo' => strtolower($mahallah) . '.png',
    ]);
}
```



__Search Parameter Validation__

Implemented Security Controls:

- Nullable: Search is optional
- String Type: Must be string
- Maximum Length: 50 characters to prevent DoS
- Alpha-Dash: Only allows letters, numbers, dashes, and underscores
- XSS Prevention: Uses strip_tags() to remove any HTML/script tags

```
// Add validation
$validated = $request->validate([
    'search' => 'nullable|string|max:50|alpha_dash',
]);

$search = $validated['search'] ?? null;

if ($search) {
    // Sanitize for XSS prevention
    $search = strip_tags($search);

    $menus = collect($menus)->filter(function ($item) use ($search) {
        return str_contains(strtolower($item['category']), strtolower($search));
    })->values()->all();
}
```



__6. Order Tracking Validation__

The order tracking endpoint accepts order IDs that must be validated to prevent unauthorized access and type juggling attacks.

Implemented Security Controls:

- Type Casting: Converts ID to integer to prevent type juggling
- Positive Number Validation: Ensures ID is greater than 0
- 404 Response: Returns 404 for invalid IDs

Code Snippet:

```
   public function track($id)
{
    // Add type cast and validate
    $id = (int) $id;

    if ($id <= 0) {
        abort(404);
    }

    $order = Order::with(['orderItems', 'shipping'])->findOrFail($id);
    
    return view('orders.track', compact('order'));
}
```


__Similarly for Receipt Viewing (CheckoutController::receipt)__

```
public function receipt($orderId)
{
    // Type cast and validate ID
    $orderId = (int) $orderId;

    if ($orderId <= 0) {
        abort(404);
    }

    $order = Order::with('orderItems', 'shipping')->findOrFail($orderId);
    
    return view('checkout.receipt', compact('order'));
}
```


## ii. Authentication
__1. Stronger Password Policies__

Student registration (/register/student) and authentication (/login) endpoints were identified as critical entry points where weak credentials could expose the system to brute force and credential stuffing attacks.

Implemented Security Controls:
- Minimum password length increased to 10 characters
- Enforced uppercase and lowercase letters
- Required numeric characters
- Required special symbols
- Enabled compromised password checks using the Have I Been Pwned database
- Uses k-anonymity (only partial hash shared)
- Prevents reuse of known breached passwords
<img width="1919" height="1077" alt="Screenshot 2025-12-28 222457" src="https://github.com/user-attachments/assets/b7301975-52e3-4f7a-b6ad-7626640072f0" />

Code Snippet:

Password::defaults(function () {
    return Password::min(10)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised();
});

__2. Login Attempt Monitoring__

Although SQL injection attempts on the login form were unsuccessful due to input validation and prepared statements, repeated authentication attempts could still indicate brute force or automated attacks.

Implemented Security Controls:
- Logs akk login attempts (successful and failed)
- Automatically removes failed attempts after a successful login
<img width="1919" height="847" alt="Screenshot 2025-12-28 223145" src="https://github.com/user-attachments/assets/eb62d985-3a54-401a-ba09-d714564f8faf" />

__3. Account Lockout Mechanism__

Repeated failed authentication attempts can lead to password guessing attacks even when SQL injection is mitigated.

Lockout Policy
- Maximum 5 failed login attempts
- Lockout duration: 15 minutes
- Reset after successful login or timeout expiry
<img width="1919" height="1020" alt="Screenshot 2025-12-28 223029" src="https://github.com/user-attachments/assets/8562f989-7008-4f09-89ea-015cfae3c04a" />

__4. Session Security Hardening__

Production testing highlighted the need to prevent session fixation, hijacking, and CSRF attacks after successful authentication.

Environment Configuration

SESSION_DRIVER=database

SESSION_LIFETIME=60

SESSION_SECURE_COOKIE=true

SESSION_HTTP_ONLY=true

SESSION_SAME_SITE=strict



### iii. Authorization
__1. Order Authorization Policy__

During authorization testing, an attempt was made for Student B to access Student A’s order by directly modifying the URL (/orders/track/{id}).
Without proper authorization checks, this would constitute an IDOR vulnerability.

Authorization Controls Implemented
- Students can only view orders that belong to them
- Order receipts are protected using the same ownership rule

Security Impact
- Prevents unauthorized order access via URL manipulation
- Mitigates IDOR attacks
- Ensures consistent authroization across controllers
- Replaces manual authorization checks with centralized policy enforcement
<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="https://github.com/user-attachments/assets/d020089c-4e3e-4da1-a3b6-836d0a9496c0" />


__2. Time-Based Order Restrictions (Attribute-Based Authorization)__

Business logic testing identified that orders could be placed at any time, including periods when cafeteria operations should be closed.
This presents a business logic abuse risk, not a technical exploit.

Authorization Controls Implemented
- Orders are only allowed during defined operating hours

Security Impact
- Prevents unauthroized order placement outside business rules
- Mitigates abuse of checkout logic
<img width="1919" height="1079" alt="Screenshot 2025-12-29 051520" src="https://github.com/user-attachments/assets/1187d7af-2b33-4105-bfb9-831faa10dd58" />

## iv. XSS and CSRF Prevention

This section describes the security measures implemented to protect the UniMeal Laravel application against Cross-Site Scripting (XSS) and Cross-Site Request Forgery (CSRF) attacks. The measures cover all relevant pages and forms, including authentication, checkout, and dashboards. The implementation uses a combination of browser-level, application-level, and framework-level protections.

__1. Content Security Policy (CSP) Middleware__

__Purpose:__

CSP prevents XSS attacks by controlling which resources (scripts, styles, images, etc.) the browser can load and execute. It acts as a “whitelist” for allowed resources, blocking malicious scripts even if they are injected.

__Implementation Details:__

-Created middleware ContentSecurityPolicy.php.

-Registered middleware in app.php to apply to all web routes.

-Added HTTP headers including Content-Security-Policy, X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, and Referrer-Policy.

__Key CSP Rules:__

-default-src 'self' → Only load resources from the same domain.

-script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.bunny.net → Allow scripts from site and specific CDNs.

-style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.bunny.net → Allow styles from site and CDNs.

-img-src 'self' data: https: → Allow images from the site, data URIs, and HTTPS sources.

-frame-ancestors 'none' → Prevent clickjacking.

-form-action 'self' → Only allow form submissions to the same domain.

__Security Benefits:__

-Blocks XSS script execution.

-Prevents clickjacking.

-Stops unauthorized AJAX requests.

-Adds an extra layer of defense in depth.

<img width="1280" height="674" alt="CSRF7" src="https://github.com/user-attachments/assets/2b163c6d-f38c-41fb-a494-83c36bf17211" />


__2. Input Validation Against HTML Injection__

__Purpose:__

Even with CSP, users could inject harmful HTML that affects layout or causes stored XSS. Server-side validation ensures only clean data is stored.

__Implementation Details:__

-Added regex validation (regex:/^[^<>]*$/) to disallow < and > in sensitive fields (e.g., name, address).

A-pplied in StudentAuthController.php (registration) and CheckoutController.php (checkout forms).

```
$request->validate([
    'name' => 'required|string|max:255|regex:/^[^<>]*$/',
    'address' => 'required|string|max:500|regex:/^[^<>]*$/',
]);
```
__Security Benefits:__

-Prevents stored XSS attacks.

-Ensures data integrity.

-Complements CSP for defense in depth.

<img width="1920" height="1080" alt="2025-12-29" src="https://github.com/user-attachments/assets/36a9370a-f5ed-4fe9-b470-53c8ddfe1e18" />


__3. CSRF Token Verification__

__Purpose:__

CSRF attacks trick users into performing actions without their consent. Laravel’s built-in CSRF protection prevents this.

__Implementation Details:__

-Verified all forms include @csrf for hidden tokens.

-AJAX requests use <meta name="csrf-token" content="{{ csrf_token() }}">.

-Laravel validates tokens automatically on state-changing requests (POST, PUT, DELETE).

__Security Benefits:__

-Blocks unauthorized form submissions.

-Tokens are session-specific and expire on logout.

-No manual code needed; framework handles validation.


<img width="1280" height="677" alt="CSRF8" src="https://github.com/user-attachments/assets/dfb1fe9b-f5ef-4934-8eaa-ad80b5ab3f33" />


_4. Secure Logout with Session Invalidation_

_Purpose:_

Prevents users from accessing protected pages after logout by clicking the browser back button

_Implementation Details:_


- Session invalidation**: Completely destroys the session
  
- CSRF token regeneration**: Generates new token to prevent reuse
  
- Multi-guard logout**: Logs out from both student and cafeteria guards
  
- Clear user feedback**: Success message after logout

_Implementation Details:_

*Implementation*:
```php
// Logout from all guards
Auth::guard('student')->logout();
Auth::guard('cafeteria')->logout();

// Invalidate the session
$request->session()->invalidate();

// Regenerate CSRF token
$request->session()->regenerateToken();
```

_Security Benefits:_

- Prevents session reuse**: Old session cannot be used after logout
- Blocks back button access**: Browser cache cleared, back button shows login page
- CSRF protection**: New token prevents token replay attacks
- Complete cleanup**: All authentication state removed
- Multi-guard safety**: Works across different user types


## V. Database Security Principles

This section describes the database-level security mechanisms implemented in the UniMeal web application. These controls protect the system even if application-level validation or authentication mechanisms are bypassed. The focus is on preventing SQL injection, enforcing data integrity, limiting database privileges, and ensuring secure error handling.

---

### 1. SQL Injection Prevention Using Prepared Statements

**Security Principle:** Parameterized Queries 
**Technology:** Laravel Eloquent ORM

All database interactions in UniMeal are performed using Laravel’s Eloquent ORM, which automatically utilizes prepared statements. User inputs are never directly concatenated into SQL queries.

**Code snippet:**
```
php
$student = Student::where('email',   $request->email)->first();
```

Security Impact:

- Prevents SQL injection attacks (e.g., ' OR '1'='1')

- Treats all user input as data, not executable SQL

- Effective even if input validation is bypassed

Testing Performed:

Authentication bypass attempts using SQL payloads

- SQL comment injection (' --)

- Single-quote injection (')

Result: All injection attempts were safely rejected.


<img width="1919" height="965" alt="image" src="https://github.com/user-attachments/assets/b8c108fe-5865-4d16-95bd-c2da1eaa6cad" />


This uses Laravel Eloquent ORM which internally creates a prepared statement:

```
$pdo->prepare("SELECT * FROM students WHERE email = ?");
$pdo->execute([$request->email]);
```

The injection payload is treated as literal data, not SQL code.

<img width="1919" height="969" alt="image" src="https://github.com/user-attachments/assets/b4d3748b-898e-4529-8269-f8837b2b5f67" />

2. Mass Assignment Protection

- Security Principle: Least Privilege, Data Integrity

- Location: Eloquent Models

Sensitive database models explicitly define writable attributes using the $fillable property to prevent unauthorized field manipulation.

**Code snippet:**
```
class Order extends Model
{
    protected $fillable = [
        'student_id',
        'total_amount',
        'payment_method',
        'shipping_fee',
        'sales_tax'
    ];
}
```

Security Impact:

Prevents attackers from modifying protected fields (e.g., status, is_admin)
Blocks privilege escalation via crafted requests

Testing Performed:

Attempted to inject non-fillable fields using modified POST requests

<img width="1512" height="801" alt="image" src="https://github.com/user-attachments/assets/54f29fe3-596b-4591-ac1a-c3ddebf6a398" />

<img width="1540" height="784" alt="image" src="https://github.com/user-attachments/assets/46eee399-314f-46f1-a3b6-65d91eb82326" />


<img width="1919" height="969" alt="image" src="https://github.com/user-attachments/assets/83758eeb-367d-48fc-a454-d28e87607cce" />


Result: Unauthorized fields were ignored and not stored.


3. Referential Integrity via Foreign Key Constraints

- Security Principle: Referential Integrity

- Layer: Database Schema

Foreign key constraints are used to enforce valid relationships between database tables.

```
$table->foreign('student_id')
      ->references('matric_no')
      ->on('students')
      ->onDelete('cascade');
```

Security Impact:

- Prevents orphaned records
- Ensures orders are always linked to valid students
- Enforces integrity even if application logic fails

Testing Performed:

- Manual insertion of orders with invalid student_id

- <img width="1919" height="850" alt="image" src="https://github.com/user-attachments/assets/3ec4925b-5857-4b1b-af2b-c513fc830eec" />

Result: Database rejected invalid records.


4. Transaction Management

- Security Principle: Atomicity, Consistency

- Layer: Database Transaction Control

Critical operations such as order creation and shipping record insertion are wrapped inside database transactions.

```
// ============================================
// TRANSACTION WRAPPER 
// ============================================
$order = DB::transaction(function () use ($user, $orderTotal, $shipping, $deliveryOption, $shippingFee, $salesTax, $cart, $validated) {
    
    // Create order
    $order = Order::create([
        'student_id' => $user->matric_no,
        'total_amount' => $orderTotal,
        'address' => $shipping['address'],
        'delivery_option' => $deliveryOption,
        'payment_method' => $validated['payment_method'],
        'shipping_fee' => $shippingFee,
        'sales_tax' => $salesTax,
    ]);

    // Set status explicitly
    $order->status = 'Pending';
    $order->save();

    // Create shipping record
    Shipping::create([
        'order_id' => $order->id,
        'name' => $shipping['name'],
        'phone' => $shipping['phone'],
        'address' => $shipping['address'],
    ]);

    // Create order items
    foreach ($cart as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'name' => $item['name'],
            'price' => $item['price'],
            'image' => $item['image'],
            'quantity' => $item['quantity'],
        ]);
    }

    // IMPORTANT: Return the order so it's accessible outside
    return $order;
});
// Now $order is accessible here for the redirect
```

Security Impact:

- Prevents partial data writes

- Ensures order and payment data remain consistent

- Protects against system failures during checkout

Testing Performed:

Forced exceptions during transaction execution

<img width="1512" height="801" alt="image" src="https://github.com/user-attachments/assets/9fa5f660-3e2c-4b84-8aac-27fb38b5b25f" />

<img width="1568" height="782" alt="image" src="https://github.com/user-attachments/assets/6fb3cb23-4044-474c-8f24-c804a1abb349" />

<img width="1568" height="704" alt="image" src="https://github.com/user-attachments/assets/65e20924-e96a-4bd2-8b83-bd6b8a39dca3" />


Result: All changes were rolled back successfully


5. Secure Error Handling and Information Disclosure Control

Security Principle: Fail Securely, Information Hiding

Environment Configuration:

```
APP_ENV=production
APP_DEBUG=false
```

Security Impact:

- Prevents database and stack trace leakage

- Displays generic error pages (403, 404, 500)

- Logs detailed errors securely on the server

Testing Performed:
Database misconfiguration simulation

Result: Generic error page displayed; sensitive details hidden.


Test: Trigger Database Error

Change to wrong database name in .env file:

DB_DATABASE=uni_meal

<img width="1320" height="672" alt="image" src="https://github.com/user-attachments/assets/a2be0e7d-b280-4491-87f2-c72bdd765601" />

Test: Production Configuration

Change .env file to production settings:

APP_ENV=production
APP_DEBUG=false

<img width="1331" height="681" alt="image" src="https://github.com/user-attachments/assets/17f3a8d3-3ce1-4e20-b68e-6451a75e4363" />

## VI. File Security Principles

This section outlines the comprehensive security audit conducted on the UniMeal Laravel web application, focusing on file security, access control, and secure coding practices.

---

### Key Findings

- No hardcoded credentials
- Proper IDOR protection implemented
- Server-side validation throughout
- `.htaccess` file was missing (now fixed)
- Backup ZIP files in public directory (now removed)
- `APP_DEBUG=true` acceptable for development

---

## Phase 1: Securing Source Code and Sensitive Data

**Principles Addressed:** 1, 3, 5, 7

### 1.1 Credential Security 

#### Configuration Files Analysis

**Files Checked:**
- `config/database.php`
- `config/app.php`

**Findings:**
```php
// SECURE - All use env() function
'host' => env('DB_HOST', '127.0.0.1'),
'database' => env('DB_DATABASE', 'laravel'),
'username' => env('DB_USERNAME', 'root'),
'password' => env('DB_PASSWORD', ''),
'key' => env('APP_KEY'),
```

**Result:** **No vulnerabilities found** - All sensitive data properly uses environment variables.

---

### 1.2 Backup File Management 

#### Scan Results

**Command:**
```powershell
Get-ChildItem -Path . -Recurse -Include *.bak,*.old,*.backup,*.sql,*.zip
```

**Findings:**
- 12 ZIP files found in `public/Source/` directory
- No .bak, .old, or .backup files
- SQL files are Laravel packages (not web-accessible)


#### Remediation

**Step 1: Remove ZIP Files**
```powershell
Remove-Item C:\xampp\htdocs\websec\UniMeal\public\Source\*.zip
```

**Step 2: Verify Removal**
```powershell
# Browser test - all should return 404
http://localhost:8000/Source/bootstrap-4.4.1-dist.zip → 404 Not Found
http://localhost:8000/Source/font-awesome-4.7.0.zip → 404 Not Found 
```

**Result:** **Fixed** - All backup files removed and inaccessible.

---

### 1.3 Hiding Secrets from Static Content

#### JavaScript Files Scan

**Command:**
```powershell
Get-ChildItem -Path public\js,resources\js -Recurse -Include *.js | 
  Select-String -Pattern "password|api_key|secret"
```

**Results:**
- All matches found in **minified 3rd-party libraries** (Bootstrap, jQuery)
- No actual hardcoded credentials
- No API keys (e.g., `sk_live_`, `pk_test_`)
- All matches are legitimate library code

#### Blade Template Scan

**Command:**
```powershell
Get-ChildItem -Path resources\views -Recurse -Include *.blade.php | 
  Select-String -Pattern "TODO|FIXME|HACK|password"
```

**Results:**
- All password matches are legitimate HTML form fields
- No TODO/FIXME/HACK comments found
- No admin credentials in comments
- No revealing debugging information

#### Client-Side Discount Logic 

**Command:**
```powershell
Get-ChildItem -Path resources\views -Recurse -Include *.blade.php | 
  Select-String -Pattern "discountCode|coupon"
```

**Result:** **No client-side discount logic found** - All pricing handled server-side.

---

### 1.4 Browser Source Code Inspection 

#### Test Pages

| Page | URL | Keywords Searched | Findings |
|------|-----|-------------------|----------|
| Login | `/login` | `password`, `api_key`, `secret` | Clean |
| Register | `/register` | `TODO`, `admin`, `sk_` | Clean |
| Checkout | `/checkout` | `stripe`, `pk_`, `total` | Clean |

**Screenshots:**
(<img width="1888" height="932" alt="Image" src="https://github.com/user-attachments/assets/5bd11e23-9f29-4f4f-b3cb-bab8fa68f28e" />)
(<img width="1898" height="928" alt="Image" src="https://github.com/user-attachments/assets/5e1c5310-85c4-42df-b62c-2ba017c9bae1" />)
(<img width="1896" height="922" alt="Image" src="https://github.com/user-attachments/assets/d3d35054-7ba4-43d3-bf93-561c1c3e4a88" />)
(<img width="1895" height="928" alt="Image" src="https://github.com/user-attachments/assets/c2543366-58e6-42f9-ab3d-35f0e79ed026" />)


**Result:** **All client-side code is clean** - No sensitive information exposed.

---

## Phase 2: Mitigating Path Manipulation and Forceful Browsing

**Principles Addressed:** 10, 11

### 2.1 Directory Listing Prevention

#### Browser Testing

**URLs Tested:**
```
http://localhost:8000/js/       → Not Found 
http://localhost:8000/css/      → Not Found 
http://localhost:8000/images/   → Not Found 
http://localhost:8000/uploads/  → 404 Not Found 
http://localhost:8000/storage/  → 404 Not Found 
```

**Result:** **Directory browsing disabled** - Laravel's routing handles all requests.

---

### 2.2 .htaccess File Security

#### Initial Scan

**Command:**
```powershell
Get-Content public\.htaccess
```

**Result:** **CRITICAL VULNERABILITY** - File does not exist!

#### Vulnerability Impact

Without `.htaccess`, the application is vulnerable to:
- Directory listing (users can browse folders)
- Direct access to `.env`, `.git`, etc.
- No URL rewriting (Laravel routing may break)
- No file protection rules

#### Remediation Steps

**Step 1: Create .htaccess**
```powershell
New-Item -Path public\.htaccess -ItemType File
```

**Step 2: Add Security Rules**
```apache
<IfModule mod_negotiation.c>
    Options -MultiViews -Indexes
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Require all denied
</FilesMatch>

<FilesMatch "\.(env|git|htaccess|htpasswd|ini|log|sh|sql|bak|old|backup)$">
    Require all denied
</FilesMatch>
```

**Result:** **Fixed** - Comprehensive security rules implemented.

---

### 2.3 Secure File Handling (IDOR Prevention)

#### OrderController Analysis

**File:** `app/Http/Controllers/OrderController.php`

**Security Features:**

1️. **`history()` Method - Secure**
```php
public function history()
{
    $orders = Order::where('student_id', Auth::guard('student')->id())
        ->with(['orderItems', 'shipping'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('orders.history', compact('orders'));
}
```
- Only fetches orders belonging to authenticated student
- No IDOR vulnerability

2️. **`track($id)` Method - Secure**
```php
public function track($id)
{
    $id = (int) $id;  // Type casting
    if ($id <= 0) {
        abort(404);   // Input validation
    }
    
    $order = Order::with(['orderItems', 'shipping'])->findOrFail($id);
    
    if ($order->student_id !== Auth::guard('student')->id()) {
        abort(403);   // Authorization check
    }
    
    return view('orders.track', compact('order'));
}
```

**Security Controls:**
- Type casting prevents SQL injection
- Input validation rejects invalid IDs
- Authorization check prevents IDOR
- Uses `findOrFail()` for proper error handling

**Test Results:**

| Test Case | URL | Expected | Actual | Status |
|-----------|-----|----------|--------|--------|
| Own order | `/orders/track/5` (logged in as owner) | 200 OK | 200 OK | PASS |
| Other's order | `/orders/track/6` (different user) | 403 Forbidden | 403 Forbidden | PASS |
| Invalid ID | `/orders/track/999` | 404 Not Found | 404 Not Found | PASS |

---

#### CheckoutController::receipt() Analysis

**File:** `app/Http/Controllers/CheckoutController.php`
```php
public function receipt($orderId)
{
    // Type cast and validate ID
    $orderId = (int) $orderId;
    if ($orderId <= 0) {
        abort(404);
    }
    
    $user = Auth::guard('student')->user();
    $order = Order::with('orderItems', 'shipping')->findOrFail($orderId);
    
    // Authorization check
    if ($user->matric_no !== $order->student_id) {
        abort(403); // Prevent others from viewing this receipt
    }
    
    return view('checkout.receipt', compact('order'));
}
```

**Result:** **Secure** - Proper authorization prevents IDOR.

---

#### CartController Analysis

**File:** `app/Http/Controllers/CartController.php`

**Implementation:** Session-based cart (not database)
```php
public function add(Request $request)
{
    // Input validation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0|max:9999.99',
        'image' => 'required|string|max:500',
    ]);
    
    $cart = Session::get('cart', []);
    // Cart operations...
}
```

**Security Analysis:**
- Cart stored in user's session (`Session::get('cart')`)
- Sessions isolated per user
- No user can access another user's session data
- No IDOR risk (session-based, not database IDs)

**Result:** **No IDOR vulnerabilities** - Session isolation prevents cross-user access.

---

### 2.4 Directory Traversal Prevention 

#### Static Code Analysis

**Commands:**
```powershell
# Search for file path operations
Get-ChildItem -Path app\Http\Controllers -Recurse -Include *.php | 
  Select-String -Pattern "storage_path|public_path|download|readfile"

# Search for user input in file paths
Get-ChildItem -Path app\Http\Controllers -Recurse -Include *.php | 
  Select-String -Pattern '\$request->.*file|\$request->.*path'
```

**Results:**
- **0 results** - No file download/upload functionality
- No file path concatenation found
- No user-controlled file paths
- No directory traversal attack surface

**Conclusion:** **Secure by design** - No file handling eliminates attack vector.

---

## Phase 3: Defensive Programming and Functionality Placement

**Principles Addressed:** 8, 9

### 3.1 Server-Side Logic Implementation 

#### CheckoutController - Price Calculation Analysis

**File:** `app/Http/Controllers/CheckoutController.php`

#### 1️. Complete Server-Side Recalculation
```php
public function processPayment(Request $request)
{
    // 5. RECALCULATE EVERYTHING SERVER-SIDE (don't trust session!)
    $deliveryFees = [
        'Pick Up' => 0.00,
        '15 - 20 Minutes' => 3.00,
        'Now' => 5.00,
    ];
    
    $shippingFee = $deliveryFees[$deliveryOption];
    
    // Recalculate cart totals
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    // Server-side tax calculation
    $salesTax = round($subtotal * 0.065, 2);
    
    // Server-side total calculation
    $orderTotal = round($subtotal + $salesTax + $shippingFee, 2);
}
```

**Why This is Secure:**
- Does NOT trust client input for prices
- Does NOT trust hidden form fields
- Does NOT even fully trust session values
- Recalculates EVERYTHING from cart items
- Uses server-defined tax rate (6.5%)
- Uses server-defined shipping fees

---

#### 2️. Whitelist Validation for Delivery Options
```php
// Lines 83-97: Server-side fee mapping
$validated = $request->validate([
    'delivery_option' => 'required|string|in:Pick Up,15 - 20 Minutes,Now',
]);

$deliveryFees = [
    'Pick Up' => 0.00,
    '15 - 20 Minutes' => 3.00,
    'Now' => 5.00,
];

$deliveryFee = $deliveryFees[$validated['delivery_option']];
```

**Security Improvement:**

**BEFORE (Vulnerable - Commented Out):**
```php
// User could manipulate delivery_fee parameter
$validated = $request->validate([
    'delivery_option' => 'required|string',
    'delivery_fee' => 'required|numeric', // User can manipulate this
]);
```

**AFTER (Secure):**
- Whitelist validation (`in:Pick Up,15 - 20 Minutes,Now`)
- Server calculates fee based on validated option
- User cannot inject custom delivery fees

---

#### 3️. Comprehensive Input Validation
```php
foreach ($cart as $item) {
    // Structure validation
    if (!isset($item['price']) || !isset($item['quantity']) || !isset($item['name'])) {
        return redirect()->route('cart.show')
            ->with('error', 'Invalid cart data.');
    }
    
    // Value validation
    if ($item['price'] <= 0 || $item['quantity'] < 1) {
        return redirect()->route('cart.show')
            ->with('error', 'Invalid item prices or quantities.');
    }
    
    // Abuse prevention
    if ($item['quantity'] > 100) {
        return redirect()->route('cart.show')
            ->with('error', 'Quantity per item cannot exceed 100.');
    }
}
```

**Protections:**
- Prevents negative prices
- Prevents zero quantities
- Prevents excessive quantities (DoS prevention)
- Validates data structure

---

#### 4️. Total Amount Sanity Check
```php
if ($orderTotal <= 0 || $orderTotal > 10000) {
    Log::error('Suspicious order total', [
        'user' => $user->matric_no,
        'total' => $orderTotal,
        'subtotal' => $subtotal,
        'tax' => $salesTax,
        'shipping' => $shippingFee
    ]);
    return redirect()->route('cart.show')
        ->with('error', 'Order total is invalid. Please contact support.');
}
```

**Why This Matters:**
- Catches $0.01 manipulation attempts
- Catches unreasonably large orders
- Logs suspicious activity for investigation

---

#### 5️. Database Transaction Safety
```php
try {
    DB::beginTransaction();
    
    $order = Order::create([
        'student_id' => $user->matric_no,
        'total_amount' => $orderTotal, // Server-calculated
        'shipping_fee' => $shippingFee,
        'sales_tax' => $salesTax,
        // ...
    ]);
    
    Shipping::create([...]);
    
    foreach ($cart as $item) {
        OrderItem::create([...]);
    }
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Order creation failed', [...]);
}
```

**Benefits:**
- Atomic operations (all-or-nothing)
- Data consistency guaranteed
- Automatic rollback on errors

---

#### 6️. Security Logging
```php
// Unauthorized access
Log::error('Unauthorized payment attempt', [
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent()
]);

// Order success
Log::info('Order created successfully', [
    'order_id' => $order->id,
    'user' => $user->matric_no,
    'total' => $orderTotal
]);
```

**Security Benefits:**
- Audit trail for fraud detection
- Incident response capability
- Pattern detection for attacks

---

### 3.2 Avoid Security Through Obscurity

#### Test Results

**Commands Executed:**
```powershell
# 1. TODO/FIXME security comments
Get-ChildItem -Path app\Http\Controllers -Recurse -Include *.php | 
  Select-String -Pattern "TODO|FIXME"

# 2. Debug code in production
Get-ChildItem -Path app -Recurse -Include *.php | 
  Select-String -Pattern "dd\(|dump\(|var_dump"

# 3. Hardcoded secrets
Get-ChildItem -Path app,config -Recurse -Include *.php | 
  Select-String -Pattern "secret.*=.*'|api_key.*="

# 4. APP_DEBUG setting
Get-Content .env | Select-String -Pattern "APP_DEBUG|APP_ENV"
```

**Results:**

| Test | Finding | Status |
|------|---------|--------|
| TODO/FIXME comments | 0 found | Clean |
| Debug code | 0 found | Clean |
| Hardcoded secrets | All use `env()` | Secure |
| APP_DEBUG | `true` (development) | Acceptable for dev |

---

#### Environment Configuration

**Current Settings:**
```env
APP_ENV=local
APP_DEBUG=true
```

**Assessment:**
- **Development:** Acceptable (enables debugging)
- **Production:** MUST change to `APP_DEBUG=false`

**Information Disclosure Risk (when DEBUG=true):**

When `APP_DEBUG=true`, Laravel exposes:
1. Full file paths and directory structure
2. Database queries and table schemas
3. Complete stack traces
4. Environment variable values
5. Source code snippets in error messages

**Example Exposed Information:**
```
Error in OrderController.php line 29:
Call to undefined method on null

Stack trace:
#0 /xampp/htdocs/websec/UniMeal/app/Http/Controllers/OrderController.php(29)
#1 /xampp/htdocs/websec/UniMeal/vendor/laravel/framework/...

Environment:
DB_PASSWORD: [value visible in debug output]
```

**Recommendation:**

**For Development (Current):** No changes needed
```env
APP_ENV=local
APP_DEBUG=true
```

**For Production Deployment:** MUST CHANGE
```env
APP_ENV=production
APP_DEBUG=false
```

---

## How to Prevent File Leaks

### Summary of File Leak Prevention Methods

| Threat | Prevention Method | Implementation | Status |
|--------|------------------|----------------|--------|
| **Backup file exposure** | Remove from `public/` | Deleted all `.zip` files | Fixed |
| **.env file exposure** | Outside `public/` + .htaccess | Laravel default + FilesMatch rule | Secure |
| **Directory browsing** | `Options -Indexes` | Added to .htaccess | Implemented |
| **Sensitive file access** | FilesMatch rules | Block .env, .git, .sql, .bak | Implemented |
| **Source code leaks** | No hardcoded credentials | Use `env()` for all secrets | Secure |
| **Storage directory** | Outside `public/` | Laravel default structure | Secure |

---

### Detailed Prevention Steps

#### 1. Backup File Management

**Actions Taken:**
```powershell
# Scan for backup files
Get-ChildItem -Path . -Recurse -Include *.zip,*.bak,*.old,*.sql

# Remove backup files from public/
Remove-Item C:\xampp\htdocs\websec\UniMeal\public\Source\*.zip

# Verify removal
# Browser test: http://localhost:8000/backup.zip → 404 Not Found ✅
```

**Why This Prevents Leaks:**
Backup files can contain:
- Source code
- Database schemas
- Credentials
- Business logic

By removing them from `public/`, attackers cannot download and analyze application internals.

---

#### 2. .env File Protection

**Verification:**
```bash
# .env location (outside public/)
/project-root/.env          ← NOT web-accessible
/project-root/public/       ← Web-accessible directory
```

**Protection Methods:**

**Method 1: Directory Structure**
- `.env` is in root directory (outside `public/`)
- Web server only serves files from `public/`

**Method 2: .htaccess Rule**
```apache
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```
- Blocks all files starting with `.` (dot)
- Includes `.env`, `.git`, `.gitignore`, etc.

**Method 3: .gitignore**
```gitignore
.env
.env.backup
.env.production
```
- Prevents committing to version control
- Protects against GitHub exposure

---

#### 3. .htaccess Security Rules

**File Location:** `public/.htaccess`

**Complete Security Configuration:**
```apache
<IfModule mod_negotiation.c>
    Options -MultiViews -Indexes
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Require all denied
</FilesMatch>

<FilesMatch "\.(env|git|htaccess|htpasswd|ini|log|sh|sql|bak|old|backup)$">
    Require all denied
</FilesMatch>
```

**What Each Rule Does:**

| Rule | Purpose | Blocks |
|------|---------|--------|
| `Options -Indexes` | Disable directory listing | `/js/`, `/css/`, `/uploads/` |
| `<FilesMatch "^\\.">` | Block dot files | `.env`, `.git/`, `.htaccess` |
| `<FilesMatch "\\.(env\|...">` | Block file extensions | `.env`, `.sql`, `.bak`, `.backup` |
| `RewriteRule ^ index.php` | Route through Laravel | Direct file access bypasses |

---

#### 4. Browser Verification Tests

**Test Results:**

| URL | Expected | Actual | Status |
|-----|----------|--------|--------|
| `http://localhost:8000/js/` | 404 | 404 Not Found | Pass |
| `http://localhost:8000/css/` | 404 | 404 Not Found | Pass |
| `http://localhost:8000/.env` | 403/404 | 404 Not Found | Pass |
| `http://localhost:8000/.git/config` | 403/404 | 404 Not Found | Pass |
| `http://localhost:8000/backup.zip` | 404 | 404 Not Found | Pass |
| `http://localhost:8000/database.sql` | 404 | 404 Not Found | Pass |
| `http://localhost:8000/index.php.bak` | 404 | 404 Not Found | Pass |

**Conclusion:** All sensitive files and directories are properly protected.

---

#### 5. Source Code Protection

**Configuration Files:**

All configuration files use `env()` for sensitive data:
```php
// config/database.php
'mysql' => [
    'host' => env('DB_HOST', '127.0.0.1'),        // Secure
    'database' => env('DB_DATABASE', 'forge'),    // Secure
    'username' => env('DB_USERNAME', 'forge'),    // Secure
    'password' => env('DB_PASSWORD', ''),         // Secure
]

// config/app.php
'key' => env('APP_KEY'),                          // Secure
'url' => env('APP_URL', 'http://localhost'),     // Secure
```

**Controller Security:**
```php
// SECURE - No hardcoded credentials
$password = bcrypt($request->password);  // Using Laravel's bcrypt

// INSECURE - Never do this
$password = "admin123";  // Hardcoded password
```

**Verification:**
```powershell
# Scan for hardcoded credentials
Get-ChildItem -Path app\Http\Controllers -Recurse -Include *.php | 
  Select-String -Pattern "password.*=.*['\"]"

# Result: Only legitimate bcrypt usage found
```

---

#### 6. Session and Storage Security

**Laravel Directory Structure:**
```
project-root/
├── app/                    ← Not web-accessible
├── config/                 ← Not web-accessible
├── storage/                ← Not web-accessible
│   ├── app/               
│   ├── framework/         
│   │   └── sessions/      ← Session files stored here
│   └── logs/              
├── vendor/                 ← Not web-accessible
├── .env                    ← Not web-accessible
└── public/                 ← ONLY web-accessible directory
    ├── index.php          
    ├── .htaccess          
    ├── css/               
    ├── js/                
    └── images/            
```

**Security Benefits:**
- Only `public/` is exposed to the web
- Application code in `app/` cannot be accessed directly
- Sessions in `storage/framework/sessions/` are protected
- Logs in `storage/logs/` are not web-accessible

**Session Configuration:**
```php
// config/session.php
'driver' => 'file',
'files' => storage_path('framework/sessions'),  // Outside public/
'http_only' => true,  // Prevent JavaScript access (XSS protection)
'secure' => false,    // Set to true in production (HTTPS only)
'same_site' => 'lax', // CSRF protection
```

---

## Web Server Configuration

### Overview

**Development Server:** PHP Built-in Server (`php artisan serve`)  
**Production Server:** Apache 2.4 (XAMPP)  
**Application Root:** `C:\xampp\htdocs\websec\UniMeal\`  
**Document Root:** `C:\xampp\htdocs\websec\UniMeal\public\`

---

### Configuration 1: Laravel Application Structure

#### Document Root Security
```
Application Directory Structure:
C:\xampp\htdocs\websec\UniMeal\
├── app/                    ← Controllers, Models (NOT web-accessible)
├── config/                 ← Configuration files (NOT web-accessible)
├── database/               ← Migrations, Seeds (NOT web-accessible)
├── storage/                ← Logs, Sessions (NOT web-accessible)
├── vendor/                 ← Dependencies (NOT web-accessible)
├── .env                    ← Environment secrets (NOT web-accessible)
└── public/                 ← ONLY web-accessible directory ✅
    ├── index.php           ← Application entry point
    ├── .htaccess           ← Security rules
    ├── css/
    ├── js/
    └── images/
```

**Security Benefit:**
- Web server (`php artisan serve` or Apache) serves **ONLY** from `public/`
- All sensitive application code is **outside** web root
- Attackers cannot directly access:
  - Controllers (`app/Http/Controllers/`)
  - Models (`app/Models/`)
  - Configuration files (`config/`)
  - Environment variables (`.env`)

**Apache Virtual Host Configuration (Production):**
```apache
<VirtualHost *:80>
    ServerName unimeal.local
    DocumentRoot "C:/xampp/htdocs/websec/UniMeal/public"
    
    <Directory "C:/xampp/htdocs/websec/UniMeal/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

### Configuration 2: .htaccess Security Rules

**File Location:** `public/.htaccess`

#### Rule 1: Disable Directory Indexing
```apache
<IfModule mod_negotiation.c>
    Options -MultiViews -Indexes
</IfModule>

# Additional enforcement
Options -Indexes
```

**Purpose:** Prevents directory listing

**Example:**
- **Without:** Accessing `/js/` shows file list
- **With:** Accessing `/js/` returns 404 Not Found

**Test Results:**
```
http://localhost:8000/js/      → 404 Not Found 
http://localhost:8000/css/     → 404 Not Found 
http://localhost:8000/images/  → 404 Not Found 
```

---

#### Rule 2: URL Rewriting (Laravel Routing)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Purpose:**
- All requests routed through `index.php` (Laravel's front controller)
- Laravel's routing system handles authorization & access control
- Direct file access is prevented

**Security Benefit:**
- Attackers cannot bypass Laravel's middleware
- All routes protected by authentication/authorization checks
- CSRF protection applied automatically

---

#### Rule 3: Protect Dot Files
```apache
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```

**Purpose:** Blocks access to hidden files starting with `.` (dot)

**Protected Files:**
- `.env` - Environment configuration with secrets
- `.git/` - Git repository metadata
- `.gitignore` - Git configuration
- `.htaccess` - Apache configuration itself
- `.editorconfig` - Editor settings

**Test Results:**
```
http://localhost:8000/.env           → 403 Forbidden 
http://localhost:8000/.git/config    → 403 Forbidden 
http://localhost:8000/.gitignore     → 403 Forbidden 
```

---

#### Rule 4: File Extension Blocking
```apache
<FilesMatch "\.(env|git|htaccess|htpasswd|ini|log|sh|sql|bak|old|backup)$">
    Require all denied
</FilesMatch>
```

**Purpose:** Blocks direct access to sensitive file types

**Protected Extensions:**

| Extension | Type | Contents |
|-----------|------|----------|
| `.env` | Environment | Database passwords, API keys |
| `.git` | Version Control | Repository history |
| `.sql` | Database | Database dumps |
| `.bak`, `.old`, `.backup` | Backups | Source code, configs |
| `.log` | Logs | Application errors, user activity |
| `.ini` | Configuration | PHP/server settings |

**Test Results:**
```
http://localhost:8000/database.sql    → 403 Forbidden 
http://localhost:8000/backup.zip      → 404 Not Found 
http://localhost:8000/app.log         → 403 Forbidden 
```

---

### Configuration 3: PHP Settings

#### Development Environment

**Current Settings (.env):**
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

**Security Implications:**

**Acceptable for Development:**
- `APP_DEBUG=true` enables detailed error messages
- Stack traces help with debugging
- Error details shown in browser

**NOT for Production:**
- Exposes file paths: `/xampp/htdocs/websec/UniMeal/app/...`
- Shows database queries
- Reveals environment variables
- Displays source code snippets

---

#### Production Environment Requirements

**Required Settings (.env):**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://unimeal.com
```

**Additional Production Settings:**
```env
# Security
SESSION_SECURE_COOKIE=true    # HTTPS only
SESSION_HTTP_ONLY=true        # Prevent XSS
SESSION_SAME_SITE=strict      # CSRF protection

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error               # Only log errors

# Database
DB_HOST=production-db-server
DB_DATABASE=unimeal_prod
DB_USERNAME=unimeal_user
DB_PASSWORD=<strong-password>
```

---

### Configuration 4: File System Permissions

#### Laravel Directory Permissions

**Recommended Permissions:**
```bash
# Application root
chmod 755 /path/to/UniMeal/

# Public directory (web-accessible)
chmod 755 public/
chmod 644 public/index.php
chmod 644 public/.htaccess

# Writable directories
chmod 775 storage/
chmod 775 bootstrap/cache/

# Recursive for storage
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

**Ownership (Linux/Production):**
```bash
# Set ownership
chown -R www-data:www-data /path/to/UniMeal/

# Or for specific user
chown -R username:www-data /path/to/UniMeal/
```

**Security Benefits:**

| Directory | Permission | Owner | Writable | Web-Accessible |
|-----------|------------|-------|----------|----------------|
| `public/` | 755 | www-data | No | Yes |
| `storage/` | 775 | www-data | Yes | No |
| `bootstrap/cache/` | 775 | www-data | Yes | No |
| `app/` | 755 | www-data | No | No |
| `.env` | 644 | www-data | No | No |

---

### Configuration 5: Session Security

**Session Configuration:**
```php
// config/session.php
return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => 120,  // 2 hours
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),  // Outside public/
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'unimeal_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', false),  // HTTPS in production
    'http_only' => true,   // Prevent JavaScript access (XSS protection)
    'same_site' => 'lax',  // CSRF protection
];
```

**Security Features:**

1️. **Session Storage Location**
- Sessions stored in `storage/framework/sessions/`
- **NOT** in web-accessible `public/` directory
- Protected by file system permissions

2️. **HTTP-Only Flag**
```php
'http_only' => true,
```
- Prevents JavaScript from accessing session cookies
- Mitigates XSS attacks

3️. **Secure Flag (Production)**
```php
'secure' => env('SESSION_SECURE_COOKIE', true),  // Production
```
- Ensures cookies only sent over HTTPS
- Prevents man-in-the-middle attacks

4️. **SameSite Attribute**
```php
'same_site' => 'lax',
```
- Prevents CSRF attacks
- Cookies not sent on cross-site requests

---

### Configuration 6: Apache Module Requirements

**Required Apache Modules (XAMPP):**
```apache
# httpd.conf or apache2.conf

LoadModule rewrite_module modules/mod_rewrite.so     # URL rewriting
LoadModule negotiation_module modules/mod_negotiation.so
LoadModule dir_module modules/mod_dir.so
LoadModule authz_core_module modules/mod_authz_core.so
```

**Verification:**
```powershell
# Check loaded modules
httpd -M | Select-String "rewrite"

# Expected output:
# rewrite_module (shared)
```

**Why These Modules Are Needed:**

| Module | Purpose | Used For |
|--------|---------|----------|
| `mod_rewrite` | URL rewriting | Laravel routing, .htaccess rules |
| `mod_negotiation` | Content negotiation | Multiple file types |
| `mod_authz_core` | Authorization | `Require all denied` directives |

---

### Configuration Summary Table

| **Configuration** | **Setting** | **Security Benefit** |
|-------------------|-------------|---------------------|
| **Document Root** | `public/` only | Application code hidden from web |
| **Directory Indexing** | `Options -Indexes` | Cannot browse directories |
| **Dot File Protection** | `<FilesMatch "^\\.">` | Blocks .env, .git access |
| **File Extension Block** | Block .env, .sql, .bak | Prevents sensitive file download |
| **URL Rewriting** | All → index.php | Laravel handles routing & auth |
| **Session Storage** | `storage/framework/` | Outside public/, not web-accessible |
| **Debug Mode** | `false` in production | No information disclosure |
| **File Permissions** | 755 public, 775 storage | Proper read/write separation |
| **HTTP-Only Cookies** | `true` | XSS protection |
| **Secure Cookies** | `true` (HTTPS) | MITM protection |
| **SameSite** | `lax` | CSRF protection |

---

---

## Conclusion

### Security Assessment Summary

The UniMeal Laravel application demonstrates **professional-grade security implementation** with:

- Zero hardcoded credentials
- Comprehensive IDOR protection
- Exceptional server-side validation
- Proper file security configuration
- No client-side business logic vulnerabilities

### Vulnerabilities Found & Fixed

| Vulnerability | Severity | Status |
|--------------|----------|--------|
| Missing .htaccess file | Critical | Fixed |
| Backup files in public/ | Medium | Fixed |
| APP_DEBUG=true | Low (dev only) | Acceptable for development |

### Security Strengths

1. **Server-Side Price Calculation** - Complete recalculation with no client trust
2. **IDOR Prevention** - Ownership checks on all sensitive resources
3. **Input Validation** - Comprehensive validation with abuse prevention
4. **Transaction Safety** - Database transactions ensure data integrity
5. **Security Logging** - Audit trail for all critical operations
6. **Code Quality** - Evidence of security awareness (commented vulnerable code)

---

## References

- Laravel Security Best Practices: https://laravel.com/docs/security
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- Assignment 8 Documentation: `SECURITY_ENHANCEMENTS.md`, `AUTHORIZATION_ENHANCEMENTS.md`
```

## 7.0 References

Athuraliya, A., & Creately. (2022, December 12). Sequence Diagram Tutorial – Complete Guide with Examples. Creately. https://creately.com/guides/sequence-diagram-tutorial/

WhatisSequenceDiagram?(n.d.).https://www.visual-paradigm.com/guide/uml-unified-modeling-language/what-is-sequence-diagram/

</br> 
</br>

