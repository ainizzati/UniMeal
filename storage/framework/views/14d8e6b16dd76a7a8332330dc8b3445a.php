<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout & Delivery</title>
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

        /* Delivery Options Styles */
        .delivery-section {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .delivery-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delivery-option:hover {
            border-color: #ec4899;
        }

        .delivery-option.selected {
            border-color: #ec4899;
            background-color: #fdf2f8;
        }

        .delivery-option-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .delivery-radio {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }

        .delivery-option.selected .delivery-radio {
            border-color: #ec4899;
            background-color: #ec4899;
        }

        .delivery-option.selected .delivery-radio::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
        }

        .delivery-info h6 {
            margin: 0;
            color: #374151;
            font-weight: 600;
        }

        .delivery-price {
            font-weight: 600;
            color: #059669;
        }

        .delivery-price.paid {
            color: #6b7280;
        }

        /* Button Styles */
        .btn-back {
            background: #6b7280;
            border: 1px solid #6b7280;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #4b5563;
            border-color: #4b5563;
        }

        .btn-pay {
            background: #ec4899;
            border: 1px solid #ec4899;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            background: #be185d;
            border-color: #be185d;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .logo img {
            max-height: 40px;
        }

        .logo i {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Hide actual radio buttons */
        .delivery-option input[type="radio"] {
            display: none;
        }
    </style>
</head>
<body>

<section class="checkout-section spad">
    <!-- Header with Logo and Breadcrumb -->
    <div class="breacrumb-section">
        <div class="container mb-4">
            <div class="row">
                <div class="col-lg-6">
                    <div class="logo">
                        <a href="<?php echo e(route('checkout.form')); ?>">
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
                        <span style="font-size: 12px; color: #f472b6;">&#x2014;</span>
                        <span style="color: #f472b6; font-weight: bold;">Delivery</span>
                        <span style="font-size: 12px; color: #f472b6;">&#x2014;</span>
                        <i class="fa fa-check-circle" style="color:grey;"></i>
                        <span style="font-size: 12px; color:grey;">&#x2014;</span>
                        <span style="color:grey;">Payment</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form to handle delivery option submission -->
    <form action="<?php echo e(route('checkout.delivery.process')); ?>" method="POST" id="deliveryForm">
        <?php echo csrf_field(); ?>
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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['image'])): ?>
                                                                <images src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" style="max-width: 50px;">
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
                                </table>
                            </div>

                            <div class="discount-coupon mt-3">
                                <h6>Discount Codes</h6>
                                <div class="coupon-form d-flex">
                                    <input type="text" class="form-control" placeholder="Enter your codes" id="couponCode">
                                    <button type="button" class="btn coupon-btn" style="background-color:#f472b6; border:#f472b6;" onclick="applyCoupon()">Apply</button>
                                </div>
                            </div>

                            <ul class="order-table list-unstyled mt-3">
                                <li class="fw-normal">Subtotal <span id="subtotal">RM<?php echo e(number_format($subtotal, 2)); ?></span></li>
                                <li class="fw-normal">Sales tax (6.5%) <span id="sales-tax">RM<?php echo e(number_format($salesTax, 2)); ?></span></li>
                                <li class="fw-normal">Shipping fee <span id="shipping-fee">RM0.00</span></li>
                                <li class="total-price fw-bold">Total <span id="order-total">RM<?php echo e(number_format($subtotal + $salesTax, 2)); ?></span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right: Delivery Options -->
                <div class="col-lg-6">
                    <h4>Delivery Options</h4>
                    <!-- Delivery Options -->
                    <div class="delivery-section">
                            <div class="row">
                                <div class="delivery-option selected" onclick="selectDeliveryOption(this, 'Pick Up', 0)">
                                    <input type="radio" name="delivery_option" value="Pick Up" data-fee="0" checked style="display: none;">
                                    <div class="delivery-option-content">
                                        <div class="delivery-radio"></div>
                                        <div class="delivery-info">
                                            <h6>Pick Up</h6>
                                        </div>
                                    </div>
                                    <div class="delivery-price">FREE</div>
                                </div>

                                <div class="delivery-option" onclick="selectDeliveryOption(this, '15 - 20 Minutes', 3)">
                                    <input type="radio" name="delivery_option" value="15 - 20 Minutes" data-fee="3" style="display: none;">
                                    <div class="delivery-option-content">
                                        <div class="delivery-radio"></div>
                                        <div class="delivery-info">
                                            <h6>15 - 20 Minutes</h6>
                                        </div>
                                    </div>
                                    <div class="delivery-price paid">+RM3</div>
                                </div>

                                <div class="delivery-option" onclick="selectDeliveryOption(this, 'Now', 5)">
                                    <input type="radio" name="delivery_option" value="Now" data-fee="5" style="display: none;">
                                    <div class="delivery-option-content">
                                        <div class="delivery-radio"></div>
                                        <div class="delivery-info">
                                            <h6>Now</h6>
                                        </div>
                                    </div>
                                    <div class="delivery-price paid">+RM5</div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs to pass data to next page -->
                        <input type="hidden" name="delivery_fee" id="delivery_fee" value="0">
                        <input type="hidden" name="final_total" id="final_total" value="<?php echo e($subtotal + $salesTax); ?>">

                    <div class="col-lg-12">
                        <div class="order-btn">
                            <button type="submit" class="site-btn place-btn" style="float: right;">Continue</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    // Initialize variables
    const subtotal = parseFloat("<?php echo e($subtotal); ?>");
    const salesTax = parseFloat("<?php echo e($salesTax); ?>");
    let currentDeliveryFee = 0;

    function selectDeliveryOption(element, optionValue, fee) {
        // Remove selected class from all options
        document.querySelectorAll('.delivery-option').forEach(option => {
            option.classList.remove('selected');
        });

        // Add selected class to clicked option
        element.classList.add('selected');

        // Check the radio button
        const radioButton = element.querySelector('input[type="radio"]');
        radioButton.checked = true;

        // Update delivery fee
        currentDeliveryFee = fee;
        updateTotals();

        // Update hidden inputs
        document.getElementById('delivery_fee').value = fee;
        document.getElementById('final_total').value = (subtotal + salesTax + fee).toFixed(2);
    }

    function updateTotals() {
        const total = subtotal + salesTax + currentDeliveryFee;

        document.getElementById('shipping-fee').textContent = 'RM' + currentDeliveryFee.toFixed(2);
        document.getElementById('order-total').textContent = 'RM' + total.toFixed(2);
    }

    function applyCoupon() {
        const couponCode = document.getElementById('couponCode').value;
        if (couponCode) {
            // Here you would typically make an AJAX call to validate and apply the coupon
            alert('Coupon functionality would be implemented here');
            // For now, just clear the input
            document.getElementById('couponCode').value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateTotals();
    });
</script>

</body>
</html>
<?php /**PATH C:\Users\Syazira Naim\UniMeal\resources\views/checkout/delivery.blade.php ENDPATH**/ ?>