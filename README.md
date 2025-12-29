# Title: UNIMEAL
## GROUP MEMBERS:
1. __NUR SAFIAH ASHIQIN BINTI SHUHANIZAL__ and __2317618__
2. __NURAIN IZZATI BINTI ABD RAUF__ and __2217978__
3. __NURSYAZIRA BINTI MOHD NAIM__ and __2214076__
4. __NUR RAIHAN SYAZWANI BINTI SUHAIMI__ and __2213262__

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

<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/IV1.jpeg" />

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



__2. Input Validation Against HTML Injection__

__Purpose:__

Even with CSP, users could inject harmful HTML that affects layout or causes stored XSS. Server-side validation ensures only clean data is stored.

__Implementation Details:__

-Added regex validation (regex:/^[^<>]*$/) to disallow < and > in sensitive fields (e.g., name, address).

A-pplied in StudentAuthController.php (registration) and CheckoutController.php (checkout forms).

$request->validate([
    'name' => 'required|string|max:255|regex:/^[^<>]*$/',
    'address' => 'required|string|max:500|regex:/^[^<>]*$/',
]);

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

<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/CSRF1.jpeg" />
<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/CSRF2.jpeg" />
<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/CSRF3.jpeg" />
<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/CSRF4.jpeg" />
<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/CSRF5.jpeg" />
<img width="1918" height="1078" alt="Screenshot 2025-12-28 231013" src="images/CSRF6.jpeg" />




Initial Security Audit and Vulnerability Identification

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


4. Transaction Management (ACID Compliance)

- Security Principle: Atomicity, Consistency

- Layer: Database Transaction Control

Critical operations such as order creation and shipping record insertion are wrapped inside database transactions.

```
DB::transaction(function () {
    Order::create([...]);
    Shipping::create([...]);
});
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

__vi. File Security Principles__


## 7.0 References

Athuraliya, A., & Creately. (2022, December 12). Sequence Diagram Tutorial – Complete Guide with Examples. Creately. https://creately.com/guides/sequence-diagram-tutorial/

WhatisSequenceDiagram?(n.d.).https://www.visual-paradigm.com/guide/uml-unified-modeling-language/what-is-sequence-diagram/

</br> 
</br>

