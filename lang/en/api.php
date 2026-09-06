<?php

return [

    'auth' => [
        'registered' => 'Account created successfully.',
        'login_failed' => 'Invalid login credentials.',
        'account_inactive' => 'Your account is inactive.',
        'logged_in' => 'Logged in successfully.',
        'logged_out' => 'Logged out successfully.',
        'phone_not_found' => 'No account is linked to this phone number.',
        'otp_sent' => 'Verification code sent to your phone.',
        'otp_invalid' => 'Invalid verification code.',
        'otp_expired' => 'Verification code has expired, please request a new one.',
        'otp_verified' => 'Verified successfully.',
        'password_updated' => 'Password updated successfully.',
        'phone_already_confirmed' => 'Phone number is already confirmed.',
        'phone_code_invalid' => 'Invalid phone confirmation code.',
        'phone_confirmed' => 'Phone number confirmed successfully.',
    ],

    'profile' => [
        'updated' => 'Profile updated successfully.',
        'password_updated' => 'Password updated successfully.',
        'deleted' => 'Account deleted successfully.',
    ],

    'product' => [
        'review_submitted' => 'Thank you, your review has been submitted successfully.',
    ],

    'order' => [
        'address_invalid' => 'Invalid delivery address.',
        'cart_empty' => 'Your shopping cart is empty.',
        'created' => 'Your order has been placed successfully.',
        'status' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ],
        'timeline' => [
            'confirmed' => 'Order confirmed',
            'in_transit' => 'In delivery',
            'delivered' => 'Delivered',
        ],
    ],

    'cart' => [
        'item_added' => 'Product added to cart.',
        'quantity_updated' => 'Quantity updated.',
        'item_removed' => 'Product removed from cart.',
        'coupon_invalid' => 'Invalid or expired coupon code.',
        'coupon_applied' => 'Coupon applied successfully.',
        'coupon_removed' => 'Coupon removed.',
    ],

    'address' => [
        'default_city' => 'Riyadh',
        'stored' => 'Address added successfully.',
        'updated' => 'Address updated successfully.',
        'deleted' => 'Address deleted successfully.',
        'made_default' => 'Address set as default.',
    ],

    'wishlist' => [
        'added' => 'Product added to wishlist.',
        'not_found' => 'The product is not in your wishlist.',
        'removed' => 'Product removed from wishlist.',
    ],

    'contact' => [
        'sent' => 'Your message has been sent successfully, we will contact you soon.',
    ],

    'notification' => [
        'updated' => 'Notification updated.',
        'all_read' => 'All notifications marked as read.',
    ],

    'home' => [
        'banner_title' => 'Winter Offers',
        'banner_subtitle' => '30% off on Oud and Amber',
    ],

    'validation' => [
        'full_name_required' => 'Full name is required.',
        'name_required' => 'Name is required.',
        'name_too_long' => 'Name is too long.',
        'email_required' => 'Email address is required.',
        'email_invalid' => 'Email address is invalid.',
        'email_taken' => 'Email address is already in use.',
        'phone_required' => 'Phone number is required.',
        'phone_taken' => 'Phone number is already in use.',
        'phone_too_long' => 'Phone number is too long.',
        'password_required' => 'Password is required.',
        'new_password_required' => 'New password is required.',
        'current_password_required' => 'Current password is required.',
        'current_password_incorrect' => 'Current password is incorrect.',
        'password_min' => 'Password must be at least 8 characters.',
        'password_confirmation_mismatch' => 'Password confirmation does not match.',
        'terms_required' => 'You must accept the terms and conditions and privacy policy.',
        'identifier_required' => 'Phone number is required.',
        'otp_required' => 'Verification code is required.',
        'otp_digits' => 'Verification code must be 4 digits.',
        'code_required' => 'Confirmation code is required.',
        'code_digits' => 'Confirmation code must be 4 digits.',
        'avatar_image' => 'Avatar must be an image file.',
        'address_required' => 'Delivery address is required.',
        'address_not_found' => 'Delivery address does not exist.',
        'payment_method_required' => 'Payment method is required.',
        'payment_method_unsupported' => 'Payment method is not supported.',
        'coupon_code_required' => 'Coupon code is required.',
        'coupon_code_too_long' => 'Coupon code is too long.',
        'notes_too_long' => 'Notes are too long.',
        'rating_required' => 'Rating is required.',
        'rating_between' => 'Rating must be between 1 and 5.',
        'review_title_too_long' => 'Review title is too long.',
        'review_body_too_long' => 'Review text is too long.',
        'product_required' => 'Product is required.',
        'product_not_found' => 'Product does not exist.',
        'wishlist_product_not_found' => 'The requested product does not exist.',
        'variant_not_found' => 'Product size does not exist.',
        'quantity_required' => 'Quantity is required.',
        'quantity_min' => 'Quantity must be at least 1.',
        'quantity_max' => 'Quantity is too large.',
        'recipient_name_required' => 'Recipient name is required.',
        'street_required' => 'Street is required.',
        'address_label_invalid' => 'Address type is invalid.',
        'message_required' => 'Message is required.',
    ],

];
