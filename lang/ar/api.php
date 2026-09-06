<?php

return [

    'auth' => [
        'registered' => 'تم إنشاء الحساب بنجاح.',
        'login_failed' => 'بيانات الدخول غير صحيحة.',
        'account_inactive' => 'حسابك غير نشط.',
        'logged_in' => 'تم تسجيل الدخول بنجاح.',
        'logged_out' => 'تم تسجيل الخروج بنجاح.',
        'phone_not_found' => 'لا يوجد حساب مرتبط بهذا الرقم.',
        'otp_sent' => 'تم إرسال رمز التحقق إلى جوالك.',
        'otp_invalid' => 'رمز التحقق غير صحيح.',
        'otp_expired' => 'انتهت صلاحية رمز التحقق، يرجى طلب رمز جديد.',
        'otp_verified' => 'تم التحقق بنجاح.',
        'password_updated' => 'تم تحديث كلمة المرور بنجاح.',
        'phone_already_confirmed' => 'رقم الجوال مؤكد بالفعل.',
        'phone_code_invalid' => 'رمز تأكيد الجوال غير صحيح.',
        'phone_confirmed' => 'تم تأكيد رقم الجوال بنجاح.',
    ],

    'profile' => [
        'updated' => 'تم تحديث البيانات بنجاح.',
        'password_updated' => 'تم تحديث كلمة المرور بنجاح.',
        'deleted' => 'تم حذف الحساب بنجاح.',
    ],

    'product' => [
        'review_submitted' => 'شكراً لك، تم إرسال تقييمك بنجاح.',
    ],

    'order' => [
        'address_invalid' => 'عنوان التوصيل غير صالح.',
        'cart_empty' => 'سلة التسوق فارغة.',
        'created' => 'تم إنشاء طلبك بنجاح.',
        'status' => [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد التجهيز',
            'in_transit' => 'جاري التوصيل',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
        ],
        'timeline' => [
            'confirmed' => 'تم تأكيد الطلب',
            'in_transit' => 'قيد التوصيل',
            'delivered' => 'تم التسليم',
        ],
    ],

    'cart' => [
        'item_added' => 'تمت إضافة المنتج إلى السلة.',
        'quantity_updated' => 'تم تحديث الكمية.',
        'item_removed' => 'تمت إزالة المنتج من السلة.',
        'coupon_invalid' => 'كود الخصم غير صالح أو منتهي.',
        'coupon_applied' => 'تم تطبيق كود الخصم بنجاح.',
        'coupon_removed' => 'تمت إزالة كود الخصم.',
    ],

    'address' => [
        'default_city' => 'الرياض',
        'stored' => 'تمت إضافة العنوان بنجاح.',
        'updated' => 'تم تحديث العنوان بنجاح.',
        'deleted' => 'تم حذف العنوان بنجاح.',
        'made_default' => 'تم تعيين العنوان كعنوان افتراضي.',
    ],

    'wishlist' => [
        'added' => 'تمت إضافة المنتج إلى المفضلة.',
        'not_found' => 'المنتج غير موجود في المفضلة.',
        'removed' => 'تمت إزالة المنتج من المفضلة.',
    ],

    'contact' => [
        'sent' => 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً.',
    ],

    'notification' => [
        'updated' => 'تم تحديث الإشعار.',
        'all_read' => 'تمت قراءة جميع الإشعارات.',
    ],

    'home' => [
        'banner_title' => 'عروض الشتاء',
        'banner_subtitle' => 'خصم 30% على العود والعنبر',
    ],

    'validation' => [
        'full_name_required' => 'الاسم الكامل مطلوب.',
        'name_required' => 'الاسم مطلوب.',
        'name_too_long' => 'الاسم طويل جداً.',
        'email_required' => 'البريد الإلكتروني مطلوب.',
        'email_invalid' => 'البريد الإلكتروني غير صالح.',
        'email_taken' => 'البريد الإلكتروني مستخدم بالفعل.',
        'phone_required' => 'رقم الجوال مطلوب.',
        'phone_taken' => 'رقم الجوال مستخدم بالفعل.',
        'phone_too_long' => 'رقم الجوال طويل جداً.',
        'password_required' => 'كلمة المرور مطلوبة.',
        'new_password_required' => 'كلمة المرور الجديدة مطلوبة.',
        'current_password_required' => 'كلمة المرور الحالية مطلوبة.',
        'current_password_incorrect' => 'كلمة المرور الحالية غير صحيحة.',
        'password_min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        'password_confirmation_mismatch' => 'تأكيد كلمة المرور غير متطابق.',
        'terms_required' => 'يجب الموافقة على الشروط والأحكام وسياسة الخصوصية.',
        'identifier_required' => ' رقم الجوال مطلوب.',
        'otp_required' => 'رمز التحقق مطلوب.',
        'otp_digits' => 'رمز التحقق يجب أن يتكون من 4 أرقام.',
        'code_required' => 'رمز التأكيد مطلوب.',
        'code_digits' => 'رمز التأكيد يجب أن يتكون من 4 أرقام.',
        'avatar_image' => 'الصورة الرمزية يجب أن تكون ملف صورة.',
        'address_required' => 'عنوان التوصيل مطلوب.',
        'address_not_found' => 'عنوان التوصيل غير موجود.',
        'payment_method_required' => 'طريقة الدفع مطلوبة.',
        'payment_method_unsupported' => 'طريقة الدفع غير مدعومة.',
        'coupon_code_required' => 'كود الخصم مطلوب.',
        'coupon_code_too_long' => 'كود الخصم طويل جداً.',
        'notes_too_long' => 'الملاحظات طويلة جداً.',
        'rating_required' => 'التقييم مطلوب.',
        'rating_between' => 'التقييم يجب أن يكون بين 1 و 5.',
        'review_title_too_long' => 'عنوان التقييم طويل جداً.',
        'review_body_too_long' => 'نص التقييم طويل جداً.',
        'product_required' => 'المنتج مطلوب.',
        'product_not_found' => 'المنتج غير موجود.',
        'wishlist_product_not_found' => 'المنتج المطلوب غير موجود.',
        'variant_not_found' => 'حجم المنتج غير موجود.',
        'quantity_required' => 'الكمية مطلوبة.',
        'quantity_min' => 'الكمية يجب أن تكون 1 على الأقل.',
        'quantity_max' => 'الكمية كبيرة جداً.',
        'recipient_name_required' => 'اسم المستلم مطلوب.',
        'street_required' => 'الشارع مطلوب.',
        'address_label_invalid' => 'نوع العنوان غير صالح.',
        'message_required' => 'الرسالة مطلوبة.',
    ],

];
