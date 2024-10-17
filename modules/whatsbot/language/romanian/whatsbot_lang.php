<?php

defined('BASEPATH') || exit('No direct script access allowed');

// connect account
$lang['whatsbot'] = 'WhatsBot';
$lang['connect_account'] = 'Conectare cont';
$lang['connect_whatsapp_business'] = 'Conectare WhatsApp Business';
$lang['campaigning'] = 'Campanie';
$lang['business_account_id_description'] = 'ID-ul contului dvs. WhatsApp Business (WABA)';
$lang['access_token_description'] = 'Tokenul de acces al utilizatorului după înregistrarea pe portalul Facebook Developers';
$lang['whatsapp_business_account_id'] = 'ID-ul contului WhatsApp Business';
$lang['whatsapp_access_token'] = 'Token de acces WhatsApp';
$lang['webhook_callback_url'] = 'URL-ul de apel al webhook-ului';
$lang['verify_token'] = 'Verificare token';
$lang['connect'] = 'Conectare';
$lang['whatsapp'] = 'WhatsApp';
$lang['one_click_account_connection'] = 'Conectare cont cu un singur clic';
$lang['connect_your_whatsapp_account'] = 'Conectați contul dvs. WhatsApp';
$lang['copy'] = 'Copiază';
$lang['copied'] = 'Copiat!!';
$lang['disconnect'] = 'Deconectare';
$lang['number'] = 'Număr';
$lang['number_id'] = 'ID număr';
$lang['quality'] = 'Calitate';
$lang['status'] = 'Stare';
$lang['business_account_id'] = 'ID cont de afaceri';
$lang['permissions'] = 'Permisiuni';
$lang['phone_number_id_description'] = 'ID-ul numărului de telefon conectat la API-ul WhatsApp Business. Dacă nu sunteți sigur, puteți folosi o solicitare GET Phone Number ID pentru a-l obține din API-ul WhatsApp (https://developers.facebook.com/docs/whatsapp/business-management-api/manage-phone-numbers)';
$lang['phone_number_id'] = 'ID-ul numărului de telefon înregistrat WhatsApp';
$lang['update_details'] = 'Actualizare detalii';

$lang['bots'] = 'Boti';
$lang['bots_management'] = 'Gestionare Boti';
$lang['create_template_base_bot'] = 'Creează bot bazat pe șablon';
$lang['create_message_bot'] = 'Creează bot de mesaj';
$lang['type'] = 'Tip';
$lang['message_bot'] = 'Bot de mesaj';
$lang['new_template_bot'] = 'Bot nou bazat pe șablon';
$lang['new_message_bot'] = 'Bot nou de mesaj';
$lang['bot_name'] = 'Nume bot';
$lang['reply_text'] = 'Text de răspuns <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Textul care va fi trimis lead-ului sau contactului. Puteți folosi {companyname}, {crm_url} sau orice alte câmpuri personalizate de îmbinare, sau folosiți semnul \'@\' pentru a găsi câmpurile disponibile de îmbinare" data-placement="bottom"></i> <span class="text-muted">(Numărul maxim de caractere permis este 1024)</span>';
$lang['reply_type'] = 'Tip de răspuns';
$lang['trigger'] = 'Declanșator';
$lang['header'] = 'Antet';
$lang['footer_bot'] = 'Subsol <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 60" data-placement="bottom"></i>';
$lang['bot_with_reply_buttons'] = 'Opțiunea 1: Bot cu butoane de răspuns';
$lang['bot_with_button_link'] = 'Opțiunea 2: Bot cu link de buton - URL CTA';
$lang['button1'] = 'Buton1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 20" data-placement="bottom"></i>';
$lang['button1_id'] = 'ID Buton1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 256" data-placement="bottom"></i>';
$lang['button2'] = 'Buton2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 20" data-placement="bottom"></i>';
$lang['button2_id'] = 'ID Buton2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 256" data-placement="bottom"></i>';
$lang['button3'] = 'Buton3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 20" data-placement="bottom"></i>';
$lang['button3_id'] = 'ID Buton3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 256" data-placement="bottom"></i>';
$lang['button_name'] = 'Nume buton <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Numărul maxim de caractere permis este 20" data-placement="bottom"></i>';
$lang['button_link'] = 'Link buton';
$lang['enter_name'] = 'Introduceți numele';
$lang['select_reply_type'] = 'Selectați tipul de răspuns';
$lang['enter_bot_reply_trigger'] = 'Introduceți declanșatorul de răspuns al botului';
$lang['enter_header'] = 'Introduceți antetul';
$lang['enter_footer'] = 'Introduceți subsolul';
$lang['enter_button1'] = 'Introduceți buton1';
$lang['enter_button1_id'] = 'Introduceți ID-ul buton1';
$lang['enter_button2'] = 'Introduceți buton2';
$lang['enter_button2_id'] = 'Introduceți ID-ul buton2';
$lang['enter_button3'] = 'Introduceți buton3';
$lang['enter_button3_id'] = 'Introduceți ID-ul buton3';
$lang['enter_button_name'] = 'Introduceți numele butonului';
$lang['enter_button_url'] = 'Introduceți URL-ul butonului';
$lang['on_exact_match'] = 'Bot de răspuns: La potrivire exactă';
$lang['when_message_contains'] = 'Bot de răspuns: Când mesajul conține';
$lang['when_client_send_the_first_message'] = 'Răspuns de bun venit - când lead-ul sau clientul trimite primul mesaj';
$lang['bot_create_successfully'] = 'Bot creat cu succes';
$lang['bot_update_successfully'] = 'Bot actualizat cu succes';
$lang['bot_deleted_successfully'] = 'Bot șters cu succes';
$lang['templates'] = 'Șabloane';
$lang['template_data_loaded'] = 'Șabloanele au fost încărcate cu succes';
$lang['load_templates'] = 'Încărcați șabloanele';
$lang['template_management'] = 'Gestionarea șabloanelor';
// campaigns
$lang['campaign'] = 'Campanie';
$lang['campaigns'] = 'Campanii';
$lang['send_new_campaign'] = 'Trimite Noua Campanie';
$lang['campaign_name'] = 'Numele Campaniei';
$lang['template'] = 'Șablon';
$lang['scheduled_send_time'] = '<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Per client, based on the contact timezone" data-placement="left"></i>Timpul Programat de Trimitere';
$lang['scheduled_time_description'] = 'Per client, pe baza fusului orar al contactului';
$lang['ignore_scheduled_time_and_send_now'] = 'Ignoră timpul programat și trimite acum';
$lang['template'] = 'Șablon';
$lang['leads'] = 'Lead-uri';
$lang['delivered_to'] = 'Livrat Către';
$lang['read_by'] = 'Citit de';
$lang['variables'] = 'Variabile';
$lang['body'] = 'Corp';
$lang['variable'] = 'Variabilă';
$lang['match_with_selected_field'] = 'Se potrivește cu un câmp selectat';
$lang['preview'] = 'Previzualizare';
$lang['send_campaign'] = 'Trimite campania';
$lang['send_to'] = 'Trimite către';
$lang['send_campaign'] = 'Trimite Campania';
$lang['view_campaign'] = 'Vizualizează Campania';
$lang['campaign_daily_task'] = 'Sarcina zilnică a campaniei';
$lang['back'] = 'Înapoi';
$lang['phone'] = 'Telefon';
$lang['message'] = 'Mesaj';
$lang['currently_type_not_supported'] = 'Tipul de șablon <strong> %s </strong> nu este suportat în prezent!';
$lang['of_your'] = 'din ';
$lang['contacts'] = 'Contacte';
$lang['select_all_leads'] = 'Selectează toate Lead-urile';
$lang['select_all_note_leads'] = 'Dacă selectezi asta, toate lead-urile viitoare sunt incluse în această campanie.';
$lang['select_all_note_contacts'] = 'Dacă selectezi asta, toate contactele viitoare sunt incluse în această campanie.';

$lang['verified_name'] = 'Nume Verificat';
$lang['mark_as_default'] = 'Marchează ca implicit';
$lang['default_number_updated'] = 'Numărul de telefon implicit a fost actualizat cu succes';
$lang['currently_using_this_number'] = 'Utilizează în prezent acest număr';
$lang['leads'] = 'Lead-uri';
$lang['pause_campaign'] = 'Pauză Campanie';
$lang['resume_campaign'] = 'Reluare Campanie';
$lang['campaign_resumed'] = 'Campania a fost reluată';
$lang['campaign_paused'] = 'Campania a fost suspendată';

//Template
$lang['body_data'] = 'Date Corp';
$lang['category'] = 'Categorie';

// Template bot
$lang['create_new_template_bot'] = 'Creează nou bot șablon';
$lang['template_bot'] = 'Bot Șablon';
$lang['variables'] = 'Variabile';
$lang['preview'] = 'Previzualizare';
$lang['template'] = 'Șablon';
$lang['bot_content_1'] = 'Acest mesaj va fi trimis contactului odată ce regula de declanșare este îndeplinită în mesajul trimis de contact.';
$lang['save_bot'] = 'Salvează bot';
$lang['please_select_template'] = 'Te rugăm să selectezi un șablon';
$lang['use_manually_define_value'] = 'Utilizează valoare definită manual';
$lang['merge_fields'] = 'Câmpuri de Fuzionare';
$lang['template_bot_create_successfully'] = 'Bot șablon creat cu succes';
$lang['template_bot_update_successfully'] = 'Bot șablon actualizat cu succes';
$lang['text_bot'] = 'Bot Text';
$lang['option_2_bot_with_link'] = 'Opțiunea 2: Bot cu buton link - Apel la Acțiune (CTA) URL';
$lang['option_3_file'] = 'Opțiunea 3: Bot cu fișier';
// Bot settings
$lang['bot'] = 'Bot';
$lang['bot_delay_response'] = 'Mesaj trimis când se așteaptă o întârziere în răspuns';
$lang['bot_delay_response_placeholder'] = 'Dă-mi un moment, voi avea răspunsul în scurt timp';

$lang['whatsbot'] = 'WhatsBot';

//campaigns
$lang['relation_type'] = 'Tip Relație';
$lang['select_all'] = 'Selectează tot';
$lang['total'] = 'Total';
$lang['merge_field_note'] = 'Utilizează semnul \'@\' pentru a adăuga câmpuri de fuzionare.';
$lang['send_to_all'] = 'Trimite către Toți ';
$lang['or'] = 'SAU';

$lang['convert_whatsapp_message_to_lead'] = 'Obține Automat un Nou Lead (transformă mesajele noi de WhatsApp în lead)';
$lang['leads_status'] = 'Starea Lead-urilor';
$lang['leads_assigned'] = 'Lead asignat';
$lang['whatsapp_auto_lead'] = 'Whatsapp Auto Lead';
$lang['webhooks_label'] = 'Datele primite prin Whatsapp vor fi retransmise către';
$lang['webhooks'] = 'WebHooks';
$lang['enable_webhooks'] = 'Activează Retransmiterea WebHooks';
$lang['chat'] = 'Chat';
$lang['black_listed_phone_numbers'] = 'Numere de telefon blocate';
$lang['sent_status'] = 'Starea Trimiterii';

$lang['active'] = 'Activ';
$lang['approved'] = 'Aprobat';
$lang['this_month'] = 'această lună';
$lang['open_chats'] = 'Chats Deschise';
$lang['resolved_conversations'] = 'Conversații Rezolvate';
$lang['messages_sent'] = 'Mesaje trimise';
$lang['account_connected'] = 'Cont conectat';
$lang['account_disconnected'] = 'Cont deconectat';
$lang['webhook_verify_token'] = 'Token de verificare Webhook';
// Chat integration
$lang['chat_message_note'] = 'Mesajul va fi trimis în curând. Te rugăm să reții că, dacă este un nou contact, nu va apărea în această listă până când contactul începe să interacționeze cu tine!';

$lang['activity_log'] = 'Jurnal Activitate';
$lang['whatsapp_logs'] = 'Jurnale Whatsapp';
$lang['response_code'] = 'Cod Răspuns';
$lang['recorded_on'] = 'Înregistrat Pe';

$lang['request_details'] = 'Detalii Cerere';
$lang['raw_content'] = 'Conținut Brut';
$lang['headers'] = 'Antete';
$lang['format_type'] = 'Tip Format';

// Permission section
$lang['show_campaign'] = 'Afișează campania';
$lang['clear_log'] = 'Șterge Jurnal';
$lang['log_activity'] = 'Jurnal Activitate';
$lang['load_template'] = 'Încarcă Șablon';

$lang['action'] = 'Acțiune';
$lang['total_parameters'] = 'Total Parametrii';
$lang['template_name'] = 'Numele Șablonului';
$lang['log_cleared_successfully'] = 'Jurnal șters cu succes';
$lang['whatsbot_stats'] = 'Statistici WhatsBot';

$lang['not_found_or_deleted'] = 'Nu a fost găsit sau a fost șters';
$lang['response'] = 'Răspuns';

$lang['select_image'] = 'Selectează imaginea';
$lang['image'] = 'Imagine';
$lang['image_deleted_successfully'] = 'Imagine ștearsă cu succes';
$lang['whatsbot_settings'] = 'Setări Whatsbot';
$lang['maximum_file_size_should_be'] = 'Dimensiunea maximă a fișierului ar trebui să fie ';
$lang['allowed_file_types'] = 'Tipuri de fișiere permise: ';

$lang['send_image'] = 'Trimite Imagine';
$lang['send_video'] = 'Trimite Videoclip';
$lang['send_document'] = 'Trimite Document';
$lang['record_audio'] = 'Înregistrează Audio';
$lang['chat_media_info'] = 'Mai multe informații pentru Tipurile de Conținut Acceptate & Dimensiunea Media de Procesat';
$lang['help'] = 'Ajutor';

// v1.1.0
$lang['clone'] = 'Clonare';
$lang['bot_clone_successfully'] = 'Bot clonat cu succes';
$lang['all_chat'] = 'Toate conversațiile';
$lang['from'] = 'De la:';
$lang['phone_no'] = 'Număr de telefon:';
$lang['supportagent'] = 'Agent de suport';
$lang['assign_chat_permission_to_support_agent'] = 'Atribuie permisiunea de chat agentului de suport doar';
$lang['enable_whatsapp_notification_sound'] = 'Activează sunetul de notificare WhatsApp';
$lang['notification_sound'] = 'Sunet de notificare';
$lang['trigger_keyword'] = 'Cuvânt cheie declanșator';
$lang['modal_title'] = 'Selectați agentul de suport';
$lang['close_btn'] = 'Închide';
$lang['save_btn'] = 'Salvează';
$lang['support_agent'] = 'Agent de suport';
$lang['change_support_agent'] = 'Schimbă agentul de suport';
$lang['replay_message'] = 'Nu poți trimite mesaj, 24 de ore au trecut.';
$lang['support_agent_note'] = '<strong>Notă: </strong>Când activezi funcția agentului de suport, persoana responsabilă de lead va fi atribuită automat conversației. Adminii pot de asemenea să aloce un nou agent din pagina de chat.';
$lang['permission_bot_clone'] = 'Clonare Bot';
$lang['remove_chat'] = 'Elimină conversația';
$lang['default_message_on_no_match'] = 'Răspuns implicit - dacă nu se potrivește niciun cuvânt cheie';
$lang['default_message_note'] = '<strong>Notă: </strong>Activarea acestei opțiuni va crește încărcătura webhook-ului. Pentru mai multe informații, vizitați acest <a href="https://docs.corbitaltech.dev/products/whatsbot/index.html" target="_blank">link</a>.';

$lang['whatsbot_connect_account'] = 'Conectare cont Whatsbot';
$lang['whatsbot_message_bot'] = 'Bot de mesaje Whatsbot';
$lang['whatsbot_template_bot'] = 'Bot de șabloane Whatsbot';
$lang['whatsbot_template'] = 'Șablon Whatsbot';
$lang['whatsbot_campaigns'] = 'Campanii Whatsbot';
$lang['whatsbot_chat'] = 'Chat Whatsbot';
$lang['whatsbot_log_activity'] = 'Activitate jurnal Whatsbot';
$lang['message_templates_not_exists_note'] = 'Permisiunea pentru șabloanele Meta lipsește. Vă rugăm să o activați în contul dvs. Meta';

// v1.2.0
$lang['ai_prompt'] = 'Solicitări AI';
$lang['ai_prompt_note'] = 'Pentru solicitările AI, vă rugăm să introduceți un mesaj pentru a activa funcția, sau utilizați solicitările AI dacă sunt deja activate';
$lang['emojis'] = 'Emojis';
$lang['translate'] = 'Traducere';
$lang['change_tone'] = 'Schimbă tonul';
$lang['professional'] = 'Profesional';
$lang['friendly'] = 'Prietenos';
$lang['empathetic'] = 'Empatic';
$lang['straightforward'] = 'Direct';
$lang['simplify_language'] = 'Simplifică limbajul';
$lang['fix_spelling_and_grammar'] = 'Corectează ortografia și gramatica';

$lang['ai_integration'] = 'Integrare AI';
$lang['open_ai_api'] = 'API OpenAI';
$lang['open_ai_secret_key'] = 'Cheie secretă OpenAI - <a href="https://platform.openai.com/account/api-keys" target="_blank">Unde găsești cheia secretă?</a>';
$lang['chat_text_limit'] = 'Limită de text pentru chat';
$lang['chat_text_limit_note'] = 'Pentru a optimiza costurile operaționale, luați în considerare limitarea numărului de cuvinte din răspunsurile chat OpenAI';
$lang['chat_model'] = 'Model de chat';
$lang['openai_organizations'] = 'Organizații OpenAI';
$lang['template_type'] = 'Tip șablon';
$lang['update'] = 'Actualizare';
$lang['open_ai_key_verification_fail'] = 'Verificarea cheii OpenAI este în așteptare din setări, vă rugăm să conectați contul dvs. OpenAI';
$lang['enable_wb_openai'] = 'Activează OpenAI în chat';
$lang['webhook_resend_method'] = 'Metodă de retransmitere webhook';
$lang['search_language'] = 'Caută limbaj...';
$lang['document'] = 'Document';
$lang['select_document'] = 'Selectați documentul';
$lang['attchment_deleted_successfully'] = 'Atașament șters cu succes';
$lang['attach_image_video_docs'] = 'Atașați imagini, videoclipuri, documente';
$lang['choose_file_type'] = 'Alegeți tipul de fișier';
$lang['max_size'] = 'Dimensiune maximă: ';

// v1.3.0

// Import CSV
$lang['bulk_campaigns']  = 'Campanii în masă';
$lang['upload_csv'] = 'Încărcați CSV';
$lang['upload'] = 'Încărcați';
$lang['csv_uploaded_successfully'] = 'Fișier CSV încărcat cu succes';
$lang['please_select_file'] = 'Vă rugăm să selectați fișier CSV';
$lang['phonenumber_field_is_required'] = 'Câmpul număr de telefon este obligatoriu';
$lang['out_of_the'] = 'Din';
$lang['records_in_your_csv_file'] = 'înregistrări în fișierul dvs. CSV,';
$lang['valid_the_campaign_can_be_sent'] = 'înregistrările sunt valide.<br /> Campania poate fi trimisă cu succes acestor';
$lang['users'] = 'utilizatori';
$lang['campaigns_from_csv_file'] = 'Campanii din fișierul CSV';
$lang['download_sample'] = 'Descarcă exemplu';
$lang['csv_rule_1'] = '1. <b>Cerința coloanei număr de telefon:</b> Fișierul dvs. CSV trebuie să conțină o coloană numită "Phoneno". Fiecare înregistrare din această coloană ar trebui să conțină un număr de contact valid, formatat corect cu codul de țară, inclusiv semnul "+" . <br /><br />';
$lang['csv_rule_2'] = '2. <b>Format și codificare CSV:</b> Datele dvs. CSV ar trebui să urmeze formatul specificat. Prima linie a fișierului CSV trebuie să conțină anteturile coloanelor, așa cum este arătat în tabelul de exemplu. Asigurați-vă că fișierul dvs. este codificat în UTF-8 pentru a preveni problemele de codificare.';
$lang['please_upload_valid_csv_file'] = 'Vă rugăm să încărcați un fișier CSV valid';
$lang['please_add_valid_number_in_csv_file'] = 'Vă rugăm să adăugați un număr <b>Phoneno</b> valid în fișierul CSV';
$lang['total_send_campaign_list'] = 'Total campanii trimise: %s';
$lang['sample_data'] = 'Date exemplu';
$lang['firstname'] = 'Prenume';
$lang['lastname'] = 'Nume';
$lang['phoneno'] = 'Număr de telefon';
$lang['email'] = 'Email';
$lang['country'] = 'Țară';
$lang['download_sample_and_read_rules'] = 'Descarcă fișierul exemplu și citește regulile';
$lang['please_wait_your_request_in_process'] = 'Vă rugăm să așteptați, solicitarea dvs. este în curs de procesare.';
$lang['whatsbot_bulk_campaign'] = 'Campanii în masă Whatsbot';
$lang['csv_campaign'] = 'Campanie CSV';

// Răspunsuri predefinite
$lang['canned_reply'] = 'Răspuns predefinit';
$lang['canned_reply_menu'] = 'Răspuns predefinit';
$lang['create_canned_reply'] = 'Creează răspuns predefinit';
$lang['title'] = 'Titlu';
$lang['desc'] = 'Descriere';
$lang['public'] = 'Public';
$lang['action'] = 'Acțiune';
$lang['delete_successfully'] = 'Răspuns șters.';
$lang['cannot_delete'] = 'Răspunsul nu poate fi șters.';
$lang['whatsbot_canned_reply'] = 'Răspuns predefinit Whatsbot';
$lang['reply'] = 'Răspunde';

// Solicitarile AI
$lang['ai_prompts'] = 'Solicitări AI';
$lang['create_ai_prompts'] = 'Creează solicitări AI';
$lang['name'] = 'Nume';
$lang['action'] = 'Acțiune';
$lang['prompt_name'] = 'Nume solicitare';
$lang['prompt_action'] = 'Acțiune solicitare';
$lang['whatsbot_ai_prompts'] = 'Solicitări AI Whatsbot';

// conversație nouă
$lang['replying_to'] = 'Răspunzând la:';
$lang['download_document'] = 'Descarcă documentul';
$lang['custom_prompt'] = 'Solicitare personalizată';
$lang['canned_replies'] = 'Răspunsuri predefinite';
$lang['use_@_to_add_merge_fields'] = 'Folosiți \'@\' pentru a adăuga câmpuri de îmbinare';
$lang['type_your_message'] = 'Tipează mesajul tău';
$lang['you_cannot_send_a_message_using_this_number'] = 'Nu poți trimite un mesaj folosind acest număr.';

// flux bot 
$lang['bot_flow'] = 'Flux Bot';
$lang['create_new_flow'] = 'Creează un nou flux';
$lang['flow_name'] = 'Numele fluxului';
$lang['flow'] = 'Flux';
$lang['bot_flow_builder'] = 'Constructor de flux Bot';
$lang['you_can_not_upload_file_type'] = 'Nu poți încărca un fișier de tip <b> %s </b>';
$lang['whatsbot_bot_flow'] = 'Flux Bot Whatsbot';

// v1.3.2
$lang['auto_clear_chat_history'] = 'Ștergere automată a istoricului conversațiilor';
$lang['enable_auto_clear_chat_history'] = 'Activează ștergerea automată a istoricului conversațiilor';
$lang['auto_clear_time'] = 'Timp de ștergere automată a istoricului';
$lang['clear_chat_history_note'] = '<strong>Notă: </strong>Dacă activați funcția de ștergere automată a istoricului conversațiilor, aceasta va șterge automat istoricul conversațiilor pe baza numărului de zile specificat, ori de câte ori rulează cron job-ul.';
$lang['source'] = 'Sursă';
$lang['groups'] = 'Grupuri';

// v1.3.3
$lang['click_user_to_chat'] = 'Faceți clic pe utilizator pentru a discuta';
