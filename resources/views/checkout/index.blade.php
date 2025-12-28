<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout & Payment</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .coupon-btn {
            color: white;
        }

        .checkout-section {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .order-summary h4 {
            margin-bottom: 20px;
        }
        .checkout-form h4 {
            color: #252525;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .checkout-form label {
            color: #252525;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .checkout-form label span {
            color: #d85d5c;
        }

        .checkout-form input {
            width: 100%;
            height: 46px;
            border: 2px solid #ebebeb;
            margin-bottom: 25px;
            padding-left: 15px;
        }

        .checkout-form input.street-first {
            margin-bottom: 20px;
        }

        .checkout-form .create-item {
            padding-top: 15px;
        }

        .checkout-form .create-item label {
            position: relative;
            cursor: pointer;
            padding-left: 32px;
            margin-bottom: 0;
            font-size: 14px;
            color: #252525;
        }

        .checkout-form .create-item label input {
            position: absolute;
            visibility: hidden;
        }

        .checkout-form .create-item label input:checked~span {
            background: #e7ab3c;
            border-color: #e7ab3c;
        }

        .checkout-form .create-item label .checkmark {
            position: absolute;
            left: 0;
            top: 3px;
            height: 13px;
            width: 13px;
            border: 2px solid #B2B2B2;
            border-radius: 2px;
        }

        .checkout-form .create-item label .checkmark:after {
            left: 0;
            top: 0;
            width: 9px;
            height: 6px;
            border: solid #ffffff;
            border-width: 2px 2px 0px 0px;
            -webkit-transform: rotate(127deg);
            -ms-transform: rotate(127deg);
            transform: rotate(127deg);
        }

        .checkout-form .place-order .order-total {
            border: 2px solid #ebebeb;
            padding-left: 40px;
            padding-right: 40px;
            padding-top: 22px;
            padding-bottom: 35px;
        }

        .checkout-form .place-order .order-total .order-table {
            margin-bottom: 30px;
        }

        .checkout-form .place-order .order-total .order-table li {
            list-style: none;
            color: #252525;
            font-size: 14px;
            font-weight: 400;
            text-transform: capitalize;
            padding-bottom: 8px;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .checkout-form .place-order .order-total .order-table li:first-child {
            padding-top: 0;
        }

        .checkout-form .place-order .order-total .order-table li.fw-normal {
            font-weight: 400;
            text-transform: capitalize;
        }

        .checkout-form .place-order .order-total .order-table li.fw-normal span {
            font-weight: 400;
            color: #666;
        }

        .checkout-form .place-order .order-total .order-table li.total-price {
            font-weight: 700;
            font-size: 16px;
            border-top: 1px solid #e5e5e5;
            padding-top: 15px;
            margin-top: 10px;
        }

        .checkout-form .place-order .order-total .order-table li.total-price span {
            color: #252525;
            font-weight: 700;
        }

        .checkout-form .place-order .order-total .payment-check {
            margin-bottom: 50px;
        }

        .checkout-form .place-order .order-total .payment-check .pc-item label {
            position: relative;
            cursor: pointer;
            padding-left: 32px;
            margin-bottom: 0;
            font-size: 14px;
            color: #252525;
        }

        .checkout-form .place-order .order-total .payment-check .pc-item label input {
            position: absolute;
            visibility: hidden;
        }

        .checkout-form .place-order .order-total .payment-check .pc-item label input:checked~span {
            background: #e7ab3c;
            border-color: #e7ab3c;
        }

        .checkout-form .place-order .order-total .payment-check .pc-item label .checkmark {
            position: absolute;
            left: 0;
            top: 3px;
            height: 13px;
            width: 13px;
            border: 2px solid #B2B2B2;
            border-radius: 2px;
        }

        .checkout-form .place-order .order-total .payment-check .pc-item label .checkmark:after {
            left: 0;
            top: 0;
            width: 9px;
            height: 6px;
            border: solid #ffffff;
            border-width: 2px 2px 0px 0px;
            -webkit-transform: rotate(127deg);
            -ms-transform: rotate(127deg);
            transform: rotate(127deg);
        }

        .checkout-form .place-order .order-total .order-btn {
            text-align: center;
        }

        .checkout-form .place-order .order-total .order-btn .place-btn {
            padding: 13px 40px 11px;
            background: #000000;
            border-color: #000000;
        }

        .cart-table {
            margin-bottom: 20px;
        }

        .cart-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th,
        .cart-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .cart-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .cart-table img {
            max-width: 50px;
            height: auto;
        }

        .discount-coupon h6 {
            margin-bottom: 10px;
            font-weight: 600;
        }

        .coupon-form {
            gap: 0;
        }

        .coupon-form input {
            border-radius: 4px 0 0 4px;
            border-right: none;
            margin-bottom: 0;
            height: 40px;
        }

        .coupon-form .coupon-btn {
            border-radius: 0 4px 4px 0;
            height: 40px;
            padding: 0 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .site-btn {
            background: #f472b6;
            border: 1px solid #f472b6;
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .site-btn:hover {
            background: #e11d48;
            border-color: #e11d48;
        }

        .logo img {
            max-height: 40px;
        }

        .logo i {
            margin-right: 10px;
            font-size: 18px;
        }
    </style>
</head>
<body>

<!-- Shopping Cart Section Begin -->
<section class="checkout-section spad">
    <div class="breacrumb-section">
        <div class="container mb-4">
            <div class="row">
                <div class="col-lg-6">
                    <div class="logo">
                        <a href="{{ route('cart.show') }}">
                            <i class="fa fa-arrow-left" style="color:grey;"></i>
                            <img src="images/unimeal_logo2.png" alt="logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="breadcrumb-text product-more" style="display: flex; justify-content: center; align-items: center; gap: 10px; font-weight: 600; font-size: 16px; color: #f472b6;">
                        <span style="color: #f472b6; font-weight: bold;">Shipping</span>
                        <span style="font-size: 12px;">&#x2014;</span>
                        <i class="fa fa-check-circle" style="color:grey;"></i>
                        <span style="font-size: 12px; color:grey;">&#x2014;</span>
                        <span style="color:grey;">Delivery</span>
                        <span style="font-size: 12px; color:grey;">&#x2014;</span>
                        <i class="fa fa-check-circle" style="color:grey;"></i>
                        <span style="font-size: 12px; color:grey;">&#x2014;</span>
                        <span style="color:grey;">Payment</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container checkout-form">
        <div class="row">
            <!-- Left: Order Summary -->
            <div class="col-lg-6">
                <div class="place-order order-summary">
                    <h4>Order Summary</h4>
                    <div class="order-total">
                        <div class="cart-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th class="p-name">Product Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cart as $item)
                                                <tr>
                                                    <td>
                                                        @if (!empty($item['image']))
                                                            <images src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="max-width: 50px;">
                                                        @else
                                                            <span>No image</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item['name'] }}</td>
                                                    <td>RM{{ number_format($item['price'], 2) }}</td>
                                                    <td>
                                                        <span style="display: inline-block; width: 40px; text-align: center;">
                                                            {{ $item['quantity'] }}
                                                        </span>
                                                    </td>
                                                    <td>RM{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">Your cart is empty.</td>
                                                </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="discount-coupon mt-3">
                            <h6>Discount Codes</h6>
                            <form class="coupon-form d-flex">
                                <input type="text" class="form-control" placeholder="Enter your codes">
                                <button type="submit" class="btn coupon-btn" style="background-color:#f472b6; border:#f472b6;">Apply</button>
                            </form>
                        </div>

                        <ul class="order-table list-unstyled mt-3">
                            <li class="fw-normal">Subtotal <span id="subtotal">RM{{ number_format($subtotal, 2) }}</span></li>
                            <li class="fw-normal">Sales tax (6.5%) <span id="sales-tax">RM{{ number_format($salesTax, 2) }}</span></li>
                            <li class="fw-normal">Shipping fee <span id="shipping-fee">RM0.00</span></li>
                            <li class="total-price fw-bold">Total <span id="order-total">RM{{ number_format($total, 2) }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right: Shipping Information -->
            <div class="col-lg-6">
                <h4>Shipping Information</h4>

                <!-- Wrap everything in a POST form -->
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf

                    <div class="col-lg-12">
                        <label for="name">Full Name <span>*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    @error('phone')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="col-lg-12">
                        <label for="phone">Phone Number <span>*</span></label>
                        <small class="form-text text-muted"> 
                             <br> Please enter your phone number in Malaysian phone format (e.g., 012-3456789 or +6012-3456789) </small>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g., 012-3456789 or +6012-3456789">
                    </div>

                    <div class="col-lg-12">
                        <label for="address">Address <span>*</span></label>
                        <textarea id="address" name="address" rows="3" required
                                style="width: 100%; padding: 10px; border: 2px solid #ebebeb; border-radius: 4px; margin-bottom: 25px;"></textarea>
                    </div>

                    <div class="col-lg-12">
                        <div class="order-btn" style="float: right;">
                            <button type="submit" class="site-btn place-btn">Next</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</section>
<!-- Shopping Cart Section End -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    function parseMoney(text) {
            return parseFloat(text.replace(/[^\d.-]/g, ''));
        }

    document.querySelectorAll('input[name="delivery_option"]').forEach(radio => {
        radio.addEventListener('change', function () {
        const shippingFee = parseFloat(this.value);
        const subtotal = parseMoney(document.getElementById('subtotal').textContent);
        const salesTax = subtotal * 0.065;
        const total = subtotal + salesTax + shippingFee;

        document.getElementById('shipping-fee').textContent = shippingFee.toFixed(2);
        document.getElementById('sales-tax').textContent = salesTax.toFixed(2);
        document.getElementById('order-total').textContent = total.toFixed(2);
        });
    });
</script>
</body>
</html>
