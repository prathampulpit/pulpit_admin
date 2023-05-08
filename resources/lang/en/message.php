<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Error Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'parameters_missing' => 'Validation Error. Required parameter missing.',
    'auth_parameters_missing' => 'Validation Error. Authorization key parameter missing.',
    'otp_success' => 'OTP sent successfully.',
    'user_not_register' => 'The mobile number entered does not exist',
    'user_exit' => 'The mobile number entered already exists in our system',
    'wrong_document' => 'Sorry, You have selected wrong document!',
    'document_upload_success'=> 'Document uploaded successfully.',
    'selfie_failed' => 'Your selfie does not match the image on-file. Please contact our contact center for further assistance.',
    'selfie_verification_failed' => 'Sorry, Your selfie does not verify with your documents. Please use another document!',
    'document_vefify_success' => 'Document verified successfully',
    'complete_register' => 'Thank you for registering for the Ara account. Your account will be verified within 24 hours',    
    'login_success' => 'User login successfully.',
    'wrong_pin' => 'You have entered an incorrect PIN. Please try again',
    'device_not_match' => 'You are already logged in on another device. Would you like to continue with this device?',
    'device_update' => 'Device details updated successfully.',
    'update_pin' => 'Login PIN changed successfully',
    'selcom_api_error' => 'Sorry, Something went wrong. Pleas try again!',
    'otp_wrong' => 'The OTP entered is incorrect. Please try again',
    'register_step' => 'Register step updated successfully.',
    'detach_account' => 'You have removed your account from this device.',
    'selfie_attempts' => 'Selfie verification attempts exceeded. Our team will review your registration information and get back to you.',
    'access_token_expired' => 'You have been logged out after 10 minutes of inactivity',
    'card_already_exit' => 'This card already exists in our system',
    'card_added' => 'Card added successfully',
    'list_of_cards' => 'list of cards',
    'card_details' => 'Card details',
    'wrong_user' => 'Sorry, You have enter wrong user id!',
    'wrong_card' => 'Sorry, You have enter wrong card id!',
    'card_remove' => 'You have successfully removed card ##CARDNUMBER## from this device.',
    'wrong_mobile' => 'The mobile number does not exist in our system',
    'account_number' => 'The Ara account number you have entered does not exist in our system',
    'insufficient_balance' => 'You have insufficient funds to complete this transaction ',
    'wrong_bank_account_number'=>'The account number is invalid. Please try again',
    'bank_details' => 'Bank details',
    'wrong_category' => 'Sorry, You have entered wrong category id!',
    'wrong_wallet' => 'Sorry, You have entered wrong wallet!',    
    'mobile_details' => 'Mobile details',
    'agent_details' => 'Agent details',
    'atm_token_success' => 'ATM token generated successfully',
    'email_exit' => 'This email address already exists in our system',
    'record_update' => 'Record updated successfully.',
    'list_of_currency' => 'List of currency',
    'blank_category' => 'Category list not available!',
    'list_of_products' => 'List of bill payment products',
    'list_of_topups' => 'List of topups',
    'all_transaction' => 'List of all transactions',
    'email_statement' => 'Email statement sent successfully',
    'report_data' => 'List of report data',
    'card_linked' => 'This card is already linked to another account',
    'card_link_active' => 'Your card is now ready for use',
    'all_notification' => 'List of all notifications',
    'remove_notification' => 'Remove notification successfully.',
    'remove_all_notification' => 'Remove all notification successfully.',
    'dispute_transaction' => 'Thank you for submitting your query. Our team is working on resolving it and will reach out shortly',
    'suspend_card' => 'Your card has been suspended successfully. All further transactions will be declined',
    'cancel_card' => 'Your card has been canceled successfully and will be removed from your account. All further transactions on this card will not be processed',
    'change_card_pin' => 'Your card PIN has been changed successfully',
    'list_of_graph' => 'list of digest graph',
    'auth_fail'=>'Sorry, Authentication failed!',
    'wrong_apikey'=>'Sorry, You have passed wrong API KEY!',
    'header_missing'=>'Sorry, Header required parameter missing!',
    'wrong_client_id'=>'Sorry, Your provided client id did not store in our system!',
    'promotional_notification_success'=>'Push message send successfully.',
    'transaction_notification_success'=>'Push message send successfully.',
    'contact_added' => 'Your support request has been submitted successfully.',
    'ara_to_other_country_details' => 'Ara to other country details',
    'lang_change' => 'Language has been changed successfully',
    'notification_change' => 'Notification setting changed successfully', 
    'login_pin_change' => 'Your PIN has been changed successfully',
    'verify_pin' => 'Verify login pin successfully',  
    'trans_failed' => 'Sorry, You have entered wrong trans id Or category id!',
    'list_of_location' => 'List of locations',
    'list_of_othercountry_config' => 'List of other country config',
    'list_of_nationality_identification_config' => 'List of nationality identification config',
    'list_of_support_topics_config' => 'List of support topics config',
    'document_alreay_added' => 'This ID Document has already been linked to another account',
    'version' => 'List of api version',
    'biometric_status_change' => 'Biometric authentication changed successfully', 
    'otp_verify' => 'otp verify successful.',
    'add_vcn_card' => 'VCN card added successfully',
    'ticket_created' => "We've created a support request for you, here is the support ticket #<<tiket_id>>, our support staff will get in touch with you soon.",
    'ticket_already_generated' => 'A support ticket is already in progress for this issue for your Ara account. Please be patient, our support staff will get in touch with you soon.',
    'trans_id_exit' => 'Sorry. Trans id already exist in our system!',
    'pin_hash' => 'Pin hash request successfully generated',
    'wrong_pin_with_attempt' => 'You have entered an incorrect PIN. You are left with <<LEFT_ATTEMPT>> more attempts.',
    'wrong_pin_with_block' => 'You have been locked out for the day because of three invalid attempts during the day.',
    'login_pin_not_match' => 'You have enter wrong old pin.',
    'ussd_disble' => 'Customer not enabled for USSD access.',
    'temp_pin_expired' => 'Temporary pin has expired.',
    'current_language_change' => 'Current language updated successfully.',
    'valid_base64_img' => 'Enter valid base64 image string.',
    'max_no_of_transaction_exist' => 'You have exceeded Daily Transactional limit, please try again tomorrow.',
    'temp_pin_change' => 'Temporary pin already changed.',
    'bubble_added' =>  'Bubble text added successfully.',
    'currency_enable_status_change' => 'Currency setting changed successfully',
    'atm_access_status_change' => 'ATM access changed successfully',
    'complete_register_with_pep_scan' => 'Thank you for registering for the Ara account. Your account will be verified within 24 hours',
    'config_list' =>  'Config lists',    
    'wrong_referral_number' => 'Sorry, You have entered wrong referral code!',
    'success' => 'Success',
    'list' => 'Lists',
    'wrong_otp_with_attempt' => 'You have entered an incorrect OTP. You are left with <<LEFT_ATTEMPT>> more attempts.',
    'wrong_otp_with_block' => 'You have been locked out for <<MIN>> min because of <<ATTEMPT>> invalid attempts.',
    'wrong_otp_with_block_new' =>'You have been locked out due to <<ATTEMPT>> invalid attempts. Please reset PIN using "Forgot PIN" during Login',
    'wrong_pin_format' => 'Your new PIN cannot be sequential digits. Please enter a new PIN.',
    'no_contact_found' => 'No contact found',
    'request_send' => 'Request send successfully.',
    'request_already_send' => 'Sorry, You had already send the request!',
    'request_update' => 'Request update successfully.',
    'request_lists' => 'You have no invite requests. Why not invite your friends and family to join Ara?',
    'not_refer' => 'Sorry, You are not eligible for register. So please send the request to your firend.',
    '404_not_found' => '404 Not Found',
    'selfie_verification_pending' => 'Sorry, Your selfie does not verify with your documents!',
    'used_past_pin' => 'This PIN been used before. Please choose a different PIN.',
    'mobile_money_order_success' => 'Transaction Successfully.',
    'mobile_money_order_failed' => 'Transaction Failed.',
    'mobile_money_order_pending' => 'Your order is being processed. Please wait...',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
