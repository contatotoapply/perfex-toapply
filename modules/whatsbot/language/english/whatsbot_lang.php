<?php

defined('BASEPATH') || exit('No direct script access allowed');

// connect account
$lang['whatsbot'] = 'WhatsBot';
$lang['connect_account'] = 'Connect Account';
$lang['connect_whatsapp_business'] = 'Connect Whatsapp Business';
$lang['campaigning'] = 'Campaigning';
$lang['business_account_id_description'] = 'Your WhatsApp Business Account (WABA) ID';
$lang['access_token_description'] = 'Your User Access Token after signing up at for an account at Facebook Developers Portal';
$lang['whatsapp_business_account_id'] = 'Whatsapp Business Account ID';
$lang['whatsapp_access_token'] = 'Whatsapp Access Token';
$lang['webhook_callback_url'] = 'Webhook Callback URL';
$lang['verify_token'] = 'Verify Token';
$lang['connect'] = 'Connect';
$lang['whatsapp'] = 'Whatsapp';
$lang['one_click_account_connection'] = 'One Click Account Connection';
$lang['connect_your_whatsapp_account'] = 'Connect Your Whatsapp Account';
$lang['copy'] = 'Copy';
$lang['copied'] = 'Copied!!';
$lang['disconnect'] = 'Disconnect';
$lang['number'] = 'Number';
$lang['number_id'] = 'Number ID';
$lang['quality'] = 'Quality';
$lang['status'] = 'Status';
$lang['business_account_id'] = 'Business Account ID';
$lang['permissions'] = 'Permissions';
$lang['phone_number_id_description'] = 'ID of the phone number connected to the WhatsApp Business API. If you are unsure about it, you can use a GET Phone Number ID request to retrieve it from WhatsApp API ( https://developers.facebook.com/docs/whatsapp/business-management-api/manage-phone-numbers )';
$lang['phone_number_id'] = 'Number ID of the WhatsApp Registered Phone';
$lang['update_details'] = 'Update Details';

$lang['bots'] = 'Bots';
$lang['bots_management'] = 'Bots Management';
$lang['create_template_base_bot'] = 'Create template base bot';
$lang['create_message_bot'] = 'Create message bot';
$lang['type'] = 'Type';
$lang['message_bot'] = 'Message Bot';
$lang['new_template_bot'] = 'New Template Bot';
$lang['new_message_bot'] = 'New Message Bot';
$lang['bot_name'] = 'Bot Name';
$lang['reply_text'] = 'Reply text <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Text that will be sent to the lead or contact. You can also use {companyname}, {crm_url} or any other custom merge fields of lead or contact, or use the \'@\' sign to find available merge fields" data-placement="bottom"></i> <span class="text-muted">(Maximum allowed characters should be 1024)</span>';
$lang['reply_type'] = 'Reply type';
$lang['trigger'] = 'Trigger';
$lang['header'] = 'Header';
$lang['footer_bot'] = 'Footer <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 60" data-placement="bottom"></i>';
$lang['bot_with_reply_buttons'] = 'Option 1: Bot with reply buttons';
$lang['bot_with_button_link'] = 'Option 2: Bot with button link - CTA URL';
$lang['button1'] = 'Button1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button1_id'] = 'Button1 ID <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 256" data-placement="bottom"></i>';
$lang['button2'] = 'Button2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button2_id'] = 'Button2 ID <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 256" data-placement="bottom"></i>';
$lang['button3'] = 'Button3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button3_id'] = 'Button3 ID <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 256" data-placement="bottom"></i>';
$lang['button_name'] = 'Button Name <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button_link'] = 'Button Link';
$lang['enter_name'] = 'Enter Name';
$lang['select_reply_type'] = 'Select reply type';
$lang['enter_bot_reply_trigger'] = 'Enter bot reply trigger';
$lang['enter_header'] = 'Enter header';
$lang['enter_footer'] = 'Enter footer';
$lang['enter_button1'] = 'Enter button1';
$lang['enter_button1_id'] = 'Enter button1 ID';
$lang['enter_button2'] = 'Enter button2';
$lang['enter_button2_id'] = 'Enter button2 ID';
$lang['enter_button3'] = 'Enter button3';
$lang['enter_button3_id'] = 'Enter button3 ID';
$lang['enter_button_name'] = 'Enter button name';
$lang['enter_button_url'] = 'Enter button url';
$lang['on_exact_match'] = 'Reply bot: On exact match';
$lang['when_message_contains'] = 'Reply bot: When message contains';
$lang['when_client_send_the_first_message'] = 'Welcome reply - when lead or client send the first message';
$lang['bot_create_successfully'] = 'Bot create successfully';
$lang['bot_update_successfully'] = 'Bot update successfully';
$lang['bot_deleted_successfully'] = 'Bot deleted successfully';
$lang['templates'] = 'Templates';
$lang['template_data_loaded'] = 'Templates loaded successfully';
$lang['load_templates'] = 'Load Templates';
$lang['template_management'] = 'Template Management';

// campaigns
$lang['campaign'] = 'Campaign';
$lang['campaigns'] = 'Campaigns';
$lang['send_new_campaign'] = 'Send New Campaign';
$lang['campaign_name'] = 'Campaign Name';
$lang['template'] = 'Template';
$lang['scheduled_send_time'] = '<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Per client, based on the contact timezone" data-placement="left"></i>Scheduled Send Time';
$lang['scheduled_time_description'] = 'Per client, based on the contact timezone';
$lang['ignore_scheduled_time_and_send_now'] = 'Ignore scheduled time and send now';
$lang['template'] = 'Template';
$lang['leads'] = 'Leads';
$lang['delivered_to'] = 'Delivered To';
$lang['read_by'] = 'Read by';
$lang['variables'] = 'Variables';
$lang['body'] = 'Body';
$lang['variable'] = 'Variable';
$lang['match_with_selected_field'] = 'Match with a selected field';
$lang['preview'] = 'Preview';
$lang['send_campaign'] = 'Send campaign';
$lang['send_to'] = 'Send to';
$lang['send_campaign'] = 'Send Campaign';
$lang['view_campaign'] = 'View Campaign';
$lang['campaign_daily_task'] = 'Campaign daily task';
$lang['back'] = 'Back';
$lang['phone'] = 'Phone';
$lang['message'] = 'Message';
$lang['currently_type_not_supported'] = 'Currently <strong> %s </strong> template type is not supported!';
$lang['of_your'] = 'of your ';
$lang['contacts'] = 'Contacts';
$lang['select_all_leads'] = 'Select all Leads';
$lang['select_all_note_leads'] = 'If you select this, all future leads are included in this campaign.';
$lang['select_all_note_contacts'] = 'If you select this, all future contacts are included in this campaign.';

$lang['verified_name'] = 'Verified Name';
$lang['mark_as_default'] = 'Mark as default';
$lang['default_number_updated'] = 'Default phone number id updated successfully';
$lang['currently_using_this_number'] = 'Currently using this number';
$lang['leads'] = 'Leads';
$lang['pause_campaign'] = 'Pause Campaign';
$lang['resume_campaign'] = 'Resume Campaign';
$lang['campaign_resumed'] = 'Campaign resumed';
$lang['campaign_paused'] = 'Campaign paused';

//Template
$lang['body_data'] = 'Body Data';
$lang['category'] = 'Category';

// Template bot
$lang['create_new_template_bot'] = 'Create new template bot';
$lang['template_bot'] = 'Template Bot';
$lang['variables'] = 'Variables';
$lang['preview'] = 'Preview';
$lang['template'] = 'Template';
$lang['bot_content_1'] = 'This message will be sent to the contact once the trigger rule is met in the message sent by the contact.';
$lang['save_bot'] = 'Save bot';
$lang['please_select_template'] = 'Please select a template';
$lang['use_manually_define_value'] = 'Use manually define value';
$lang['merge_fields'] = 'Merge Fields';
$lang['template_bot_create_successfully'] = 'Template bot created successfully';
$lang['template_bot_update_successfully'] = 'Template bot updated successfully';
$lang['text_bot'] = 'Text bot';
$lang['option_2_bot_with_link'] = 'Option 2: Bot with button link - Call to Action (CTA) URL';
$lang['option_3_file'] = 'Option 3: Bot with file';
// Bot settings
$lang['bot'] = 'Bot';
$lang['bot_delay_response'] = 'Message send when delay in response is expected';
$lang['bot_delay_response_placeholder'] = 'Give me a moment, I will have the answer shortly';

$lang['whatsbot'] = 'WhatsBot';

//campaigns
$lang['relation_type'] = 'Relation Type';
$lang['select_all'] = 'Select all';
$lang['total'] = 'Total';
$lang['merge_field_note'] = 'Use \'@\' Sign for add merge fields.';
$lang['send_to_all'] = 'Send to All ';
$lang['or'] = 'OR';

$lang['convert_whatsapp_message_to_lead'] = 'Acquire New Lead Automatically(convert new whatsapp messages to lead)';
$lang['leads_status'] = 'Lead status';
$lang['leads_assigned'] = 'Lead assigned';
$lang['whatsapp_auto_lead'] = 'Whatsapp Auto Lead';
$lang['webhooks_label'] = 'Whatsapp received data will be resend to';
$lang['webhooks'] = 'WebHooks';
$lang['enable_webhooks'] = 'Enable WebHooks Re-send';
$lang['chat'] = 'Chat';
$lang['black_listed_phone_numbers'] = 'Black listed phone numbers';
$lang['sent_status'] = 'Sent Status';

$lang['active'] = 'Active';
$lang['approved'] = 'Approved';
$lang['this_month'] = 'this month';
$lang['open_chats'] = 'Open Chats';
$lang['resolved_conversations'] = 'Resolved Conversations';
$lang['messages_sent'] = 'Messages sent';
$lang['account_connected'] = 'Account connected';
$lang['account_disconnected'] = 'Account disconnected';
$lang['webhook_verify_token'] = 'Webhook verify token';
// Chat integration
$lang['chat_message_note'] = 'Message will be send shortly. Please note that if new contact, it will not appear in this list untill the contact start interacting with you!';

$lang['activity_log'] = 'Activity Log';
$lang['whatsapp_logs'] = 'Whatsapp Logs';
$lang['response_code'] = 'Response Code';
$lang['recorded_on'] = 'Recorded On';

$lang['request_details'] = 'Request Details';
$lang['raw_content'] = 'Raw Content';
$lang['headers'] = 'Headers';
$lang['format_type'] = 'Format Type';

// Permission section
$lang['show_campaign'] = 'Show campaign';
$lang['clear_log'] = 'Clear Log';
$lang['log_activity'] = 'Log Activity';
$lang['load_template'] = 'Load Template';

$lang['action'] = 'Action';
$lang['total_parameters'] = 'Total Parameters';
$lang['template_name'] = 'Template Name';
$lang['log_cleared_successfully'] = 'Log cleared successfully';
$lang['whatsbot_stats'] = 'WhatsBot Stats';

$lang['not_found_or_deleted'] = 'Not found or deleted';
$lang['response'] = 'Response';

$lang['select_image'] = 'Select image';
$lang['image'] = 'Image';
$lang['image_deleted_successfully'] = 'Image deleted successfully';
$lang['whatsbot_settings'] = 'Whatsbot Settings';
$lang['maximum_file_size_should_be'] = 'Maximum file size should be ';
$lang['allowed_file_types'] = 'Allowed file types : ';

$lang['send_image'] = 'Send Image';
$lang['send_video'] = 'Send Video';
$lang['send_document'] = 'Send Document';
$lang['record_audio'] = 'Record Audio';
$lang['chat_media_info'] = 'More info for Supported Content-Types & Post-Processing Media Size';
$lang['help'] = 'Help';

// v1.1.0
$lang['clone'] = 'Clone';
$lang['bot_clone_successfully'] = 'Bot clone successfully';
$lang['all_chat'] = 'All Chats';
$lang['from'] = 'From:';
$lang['phone_no'] = 'Phone No:';
$lang['supportagent'] = 'Support Agent';
$lang['assign_chat_permission_to_support_agent'] = 'Assign chat permission to support agent only';
$lang['enable_whatsapp_notification_sound'] = 'Enable whatsapp chat notification sound';
$lang['notification_sound'] = 'Notification Sound';
$lang['trigger_keyword'] = 'Trigger Keyword';
$lang['modal_title'] = 'Select Support Agent';
$lang['close_btn'] = 'Close';
$lang['save_btn'] = 'Save';
$lang['support_agent'] = 'Support Agent';
$lang['change_support_agent'] = 'Change Support Agent';
$lang['replay_message'] = 'You can\'t send message 24 hours is over.';
$lang['support_agent_note'] = '<strong>Note: </strong>When you enable the support agent feature, the lead assignee will automatically be assigned to the chat. Admins can also assign a new agent from the chat page.';
$lang['permission_bot_clone'] = 'Clone Bot';
$lang['remove_chat'] = 'Remove Chat';
$lang['default_message_on_no_match'] = 'Default Reply - if any keyword does not match';
$lang['default_message_note'] = '<strong>Note: </strong>Enabling this option will increase your webhook load. For more info visit this <a href="https://docs.corbitaltech.dev/products/whatsbot/index.html" target="_blank">link</a>.';

$lang['whatsbot_connect_account'] = 'Whatsbot Connect Account';
$lang['whatsbot_message_bot'] = 'Whatsbot Message Bot';
$lang['whatsbot_template_bot'] = 'Whatsbot Template Bot';
$lang['whatsbot_template'] = 'Whatsbot Template';
$lang['whatsbot_campaigns'] = 'Whatsbot Campaigns';
$lang['whatsbot_chat'] = 'Whatsbot Chat';
$lang['whatsbot_log_activity'] = 'Whatsbot Log Activity';
$lang['message_templates_not_exists_note'] = 'Meta template permission missing. Please enable it in your Meta account';

// v1.2.0
$lang['ai_prompt'] = 'AI Prompts';
$lang['ai_prompt_note'] = 'For AI prompts, please input a message to enable the feature, or use AI prompts if already enabled';
$lang['emojis'] = 'Emojis';
$lang['translate'] = 'Translate';
$lang['change_tone'] = 'Change Tone';
$lang['professional'] = 'Professional';
$lang['friendly'] = 'Friendly';
$lang['empathetic'] = 'Empathetic';
$lang['straightforward'] = 'Straightforward';
$lang['simplify_language'] = 'Simplify Language';
$lang['fix_spelling_and_grammar'] = 'Fix Spelling & Grammar';

$lang['ai_integration'] = 'AI Integration';
$lang['open_ai_api'] = 'OpenAI API';
$lang['open_ai_secret_key'] = 'OpenAI Secret Key - <a href="https://platform.openai.com/account/api-keys" target="_blank">Where you can find secret key?</a>';
$lang['chat_text_limit'] = 'Chat Text Limit';
$lang['chat_text_limit_note'] = 'To optimize operational costs, consider limiting the word count of OpenAI\'s chat responses';
$lang['chat_model'] = 'Chat Model';
$lang['openai_organizations'] = 'OpenAi Organizations';
$lang['template_type'] = 'Template Type';
$lang['update'] = 'Update';
$lang['open_ai_key_verification_fail'] = 'OpenAi Key Verification is Pending from settings please connect your openai account';
$lang['enable_wb_openai'] = 'Enable OpenAI in chat';
$lang['webhook_resend_method'] = 'Webhook Resend Method';
$lang['search_language'] = 'Search language...';
$lang['document'] = 'Document';
$lang['select_document'] = 'Select Document';
$lang['attchment_deleted_successfully'] = 'Attchment Deleted Successfully';
$lang['attach_image_video_docs'] = 'Attach Image Video Documents';
$lang['choose_file_type'] = 'Choose File Type';
$lang['max_size'] = 'Max Size: ';

// v1.3.0

// CSV import
$lang['bulk_campaigns']  = 'Bulk Campaigns';
$lang['upload_csv'] = 'Upload CSV';
$lang['upload'] = 'Upload';
$lang['csv_uploaded_successfully'] = 'CSV File Uploaded Successfully';
$lang['please_select_file'] = 'Please Select CSV File';
$lang['phonenumber_field_is_required'] = 'Phonenumber field is required';
$lang['out_of_the'] = 'Out of the';
$lang['records_in_your_csv_file'] = 'records in your CSV file,';
$lang['valid_the_campaign_can_be_sent'] = 'records are valid.<br /> The campaign can be successfully sent to these';
$lang['users'] = 'users';
$lang['campaigns_from_csv_file'] = 'Campaigns from CSV File';
$lang['download_sample'] = 'Download Sample';
$lang['csv_rule_1'] = '1. <b>Phone Number Column Requirement:</b> Your CSV file must include a column named "Phoneno." Each record in this column should contain a valid contact number, correctly formatted with the country code, including the "+" sign. <br /><br />';
$lang['csv_rule_2'] = '2. <b>CSV Format and Encoding:</b> Your CSV data should follow the specified format. The first row of your CSV file must contain the column headers, as shown in the example table. Ensure that your file is encoded in UTF-8 to prevent any encoding issues.';
$lang['please_upload_valid_csv_file'] = 'Please upload valid CSV file';
$lang['please_add_valid_number_in_csv_file'] = 'Please add valid <b>Phoneno</b> in CSV file';
$lang['total_send_campaign_list'] = 'Total send campaign : %s';
$lang['sample_data'] = 'Sample Data';
$lang['firstname'] = 'Firstname';
$lang['lastname'] = 'Lastname';
$lang['phoneno'] = 'Phoneno';
$lang['email'] = 'Email';
$lang['country'] = 'Country';
$lang['download_sample_and_read_rules'] = 'Download Sample File & Read Rules';
$lang['please_wait_your_request_in_process'] = 'Please wait, your request is currently being processed.';
$lang['whatsbot_bulk_campaign'] = 'Whatsbot Bulk Campaigns';
$lang['csv_campaign'] = 'CSV Campaign';

// Canned reply
$lang['canned_reply'] = 'Canned Reply';
$lang['canned_reply_menu'] = 'Canned Reply';
$lang['create_canned_reply'] = 'Create Canned Reply';
$lang['title'] = 'Title';
$lang['desc'] = 'Description';
$lang['public'] = 'Public';
$lang['action'] = 'Action';
$lang['delete_successfully'] = 'Reply deleted.';
$lang['cannot_delete'] = 'Reply cann\'t delete.';
$lang['whatsbot_canned_reply'] = 'Whatsbot Canned Reply';
$lang['reply'] = 'Reply';

//AI Prompts
$lang['ai_prompts'] = 'AI Prompts';
$lang['create_ai_prompts'] = 'Create AI Prompts';
$lang['name'] = 'Name';
$lang['action'] = 'Action';
$lang['prompt_name'] = 'Prompt name';
$lang['prompt_action'] = 'Prompt action';
$lang['whatsbot_ai_prompts'] = 'Whatsbot AI Prompts';

// new chat
$lang['replying_to'] = 'Replying to :';
$lang['download_document'] = 'Download Document';
$lang['custom_prompt'] = 'Custom Prompt';
$lang['canned_replies'] = 'Canned Replies';
$lang['use_@_to_add_merge_fields'] = 'Use \'@\' to add merge fields';
$lang['type_your_message'] = 'Type your message';
$lang['you_cannot_send_a_message_using_this_number'] = 'You cannot send a message using this number.';

// bot flow 
$lang['bot_flow'] = 'Bot Flow';
$lang['create_new_flow'] = 'Create New Flow';
$lang['flow_name'] = 'Flow Name';
$lang['flow'] = 'Flow';
$lang['bot_flow_builder'] = 'Bot Flow Builder';
$lang['you_can_not_upload_file_type'] = 'You can\'t upload <b> %s </b> type of file';
$lang['whatsbot_bot_flow'] = 'Whatsbot Bot Flow';

// v1.3.2
$lang['auto_clear_chat_history'] = 'Auto Clear Chat History';
$lang['enable_auto_clear_chat_history'] = 'Enable Auto Clear Chat History';
$lang['auto_clear_time'] = 'Auto Clear History Time';
$lang['clear_chat_history_note'] = '<strong>Note: </strong> If you enable the auto-clear chat history feature, it will automatically clear the chat history based on the number of days you specify, whenever the cron job runs.';
$lang['source'] = 'Source';
$lang['groups'] = 'Groups';

//  v1.3.3

$lang['click_user_to_chat'] = 'Click User To Chat';
