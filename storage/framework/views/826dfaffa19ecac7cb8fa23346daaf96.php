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

        /* Breadcrumb Styles */
        .breadcrumb-section {
            background: white;
            padding: 20px 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 30px;
        }

        .breadcrumb-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            font-weight: 600;
            font-size: 16px;
        }

        .breadcrumb-step {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-step.active {
            color: #ec4899;
        }

        .breadcrumb-step.completed {
            color: #ec4899;
        }

        .breadcrumb-step.inactive {
            color: #9ca3af;
        }

        .breadcrumb-divider {
            width: 25px;
            height: 2px;
            background: #e5e7eb;
        }

        .breadcrumb-divider.active {
            background: #ec4899;
        }

        /* Payment */
        .form-check-input.small-radio {
            width: 14px;
            height: 14px;
            margin-top: 0.125rem;
            accent-color: #ec4899;
        }

        .payment-option {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .payment-option:hover {
            border-color: #ec4899;
            background-color: #f8f9fa;
        }

        .payment-option.selected {
            background-color: #ffe4ef;
            border-color: #ec4899;
        }

        .payment-option input[type="radio"]:checked + .form-check-label {
            color: #ec4899;
        }

        .card-details {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
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
                        <a href="<?php echo e(route('checkout.delivery')); ?>">
                            <i class="fa fa-arrow-left" style="color:grey;"></i>
                            <img src="images/unimeal_logo2.png" alt="logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="breadcrumb-text product-more" style="display: flex; justify-content: center; align-items: center; gap: 10px; font-weight: 600; font-size: 16px; color: #f472b6;">
                        <span style="color: #f472b6;">Shipping</span>
                        <span style="font-size: 12px;">&#x2014;</span>
                        <i class="fa fa-check-circle" style="color: #f472b6;"></i>
                        <span style="font-size: 12px;">&#x2014;</span>
                        <span style="color: #f472b6;">Delivery</span>
                        <span style="font-size: 12px;">&#x2014;</span>
                        <i class="fa fa-check-circle" style="color: #f472b6;"></i>
                        <span style="font-size: 12px;">&#x2014;</span>
                        <span style="color: #f472b6; font-weight: bold;">Payment</span>
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
                                   <!-- Inside the table in your checkout view -->
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['image'])): ?>
                                                        <img src="<?php echo e(asset('images/' . $item['image'])); ?>" alt="<?php echo e($item['name']); ?>" style="max-width: 50px;">
                                                    <?php else: ?>
                                                        <span>No image</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td><?php echo e($item['name']); ?></td>
                                                <td>RM<?php echo e(number_format($item['price'], 2)); ?></td>
                                                <td>
                                                    <span style="display: inline-block; width: 40px; text-align: center;">
                                                        <?php echo e($item['quantity']); ?>

                                                    </span>
                                                </td>
                                                <td>RM<?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5">Your cart is empty.</td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>

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
                            <li class="fw-normal">Subtotal <span id="subtotal">RM<?php echo e(number_format($subtotal, 2)); ?></span></li>
                            <li class="fw-normal">Sales tax (6.5%) <span id="sales-tax">RM<?php echo e(number_format($salesTax, 2)); ?></span></li>
                            <li class="fw-normal">Shipping fee <span id="shipping-fee">RM<?php echo e(number_format($shippingFee, 2)); ?></span></li>
                            <li class="total-price">Total <span id="order-total">RM<?php echo e(number_format($orderTotal, 2)); ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right: Payment Methods -->
            <div class="col-lg-6">
                <h4 class="mb-4">Payment Methods</h4>

                <!-- SINGLE FORM for both payment method selection and order processing -->
                <form action="<?php echo e(route('checkout.payment.process')); ?>" method="POST" id="payment-form">
                    <?php echo csrf_field(); ?>

                    <!-- Session-based values -->
                    <input type="hidden" name="matric_no" value="<?php echo e(Auth::guard('student')->user()->matric_no); ?>">
                    <input type="hidden" name="cafeteria_id" value="<?php echo e(session('cafeteria_id')); ?>">
                    <input type="hidden" name="address" value="<?php echo e(session('shipping.address')); ?>">
                    <input type="hidden" name="subtotal" value="<?php echo e(session('subtotal')); ?>">
                    <input type="hidden" name="sales_tax" value="<?php echo e(session('sales_tax')); ?>">
                    <input type="hidden" name="shipping_fee" value="<?php echo e(session('shipping_fee')); ?>">
                    <input type="hidden" name="total_amount" value="<?php echo e(session('order_total')); ?>">

                    <!-- Pay on Delivery -->
                    <div class="payment-option d-flex align-items-start">
                        <input class="form-check-input small-radio me-3" type="radio" name="payment_method" id="cod" value="cash" required>
                        <label class="form-check-label flex-grow-1" for="cod">
                            <strong>Pay on Delivery</strong><br>
                            <small class="text-muted">Pay with cash on delivery</small>
                        </label>
                    </div>

                    <!-- Credit/Debit Cards -->
                    <div class="payment-option d-flex align-items-start">
                        <input class="form-check-input small-radio me-3" type="radio" name="payment_method" id="card" value="credit_card">
                        <label class="form-check-label flex-grow-1" for="card">
                            <strong>Credit/Debit Cards</strong><br>
                            <small class="text-muted">Pay with your Credit / Debit Card</small>
                        </label>
                    </div>

                    <!-- Card Details -->
                    <div class="card-details ms-4" id="card-details" style="display:none;">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <input type="text" class="form-control" placeholder="Card Number" name="card_number">
                            </div>
                            <div class="col-6 mb-2">
                                <input type="text" class="form-control" placeholder="MM / YY" name="expiry_date">
                            </div>
                            <div class="col-6 mb-2">
                                <input type="text" class="form-control" placeholder="CVV" name="cvv">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer -->
                    <div class="payment-option d-flex align-items-start">
                        <input class="form-check-input small-radio me-3" type="radio" name="payment_method" id="bank" value="bank_transfer">
                        <label class="form-check-label flex-grow-1" for="bank">
                            <strong>Direct Bank Transfer</strong><br>
                            <small class="text-muted">Make payment directly through bank account</small>
                        </label>
                    </div>

                    <!-- Other Methods -->
                    <div class="payment-option d-flex align-items-start">
                        <input class="form-check-input small-radio me-3" type="radio" name="payment_method" id="other" value="other">
                        <label class="form-check-label flex-grow-1" for="other">
                            <strong>Other Payment Methods</strong><br>
                            <small class="text-muted">Pay with GPay, PayPal, Paytm, etc.</small>
                        </label>
                    </div>

                    <!-- Place Order Button -->
                    <div class="order-btn mt-4">
                        <button type="submit" class="site-btn place-btn" style="float: right;">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- Shopping Cart Section End -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle card details form visibility
    document.addEventListener("DOMContentLoaded", function () {
        const cardRadio = document.getElementById("card");
        const cardDetails = document.getElementById("card-details");

        document.querySelectorAll('input[name="payment_method"]').forEach((input) => {
            input.addEventListener("change", function () {
                if (cardRadio.checked) {
                    cardDetails.style.display = "block";
                } else {
                    cardDetails.style.display = "none";
                }

                // Optional: highlight selected payment option
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove("selected");
                });
                input.closest(".payment-option").classList.add("selected");
            });
        });
    });
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\uni-meal\resources\views/checkout/payment.blade.php ENDPATH**/ ?>