<?php

defined('BASEPATH') || exit('No direct script access allowed');

// connect account
$lang['whatsbot'] = 'WhatsBot';
$lang['connect_account'] = 'Connecter le compte';
$lang['connect_whatsapp_business'] = 'Connecter Whatsapp Business';
$lang['campaigning'] = 'Campagne';
$lang['business_account_id_description'] = 'Votre ID de compte WhatsApp Business (WABA)';
$lang['access_token_description'] = 'Votre jeton d’accès utilisateur après vous être inscrit sur le portail des développeurs Facebook';
$lang['whatsapp_business_account_id'] = 'ID de compte Whatsapp Business';
$lang['whatsapp_access_token'] = 'Jeton d’accès Whatsapp';
$lang['webhook_callback_url'] = 'URL de rappel Webhook';
$lang['verify_token'] = 'Vérifier le jeton';
$lang['connect'] = 'Connecter';
$lang['whatsapp'] = 'Whatsapp';
$lang['one_click_account_connection'] = 'Connexion au compte en un clic';
$lang['connect_your_whatsapp_account'] = 'Connectez votre compte Whatsapp';
$lang['copy'] = 'Copier';
$lang['copied'] = 'Copié !!';
$lang['disconnect'] = 'Déconnecter';
$lang['number'] = 'Numéro';
$lang['number_id'] = 'ID du numéro';
$lang['quality'] = 'Qualité';
$lang['status'] = 'Statut';
$lang['business_account_id'] = 'ID de compte professionnel';
$lang['permissions'] = 'Permissions';
$lang['phone_number_id_description'] = 'ID du numéro de téléphone connecté à l’API WhatsApp Business. Si vous n’êtes pas sûr, vous pouvez utiliser une requête GET Phone Number ID pour le récupérer depuis l’API WhatsApp (https://developers.facebook.com/docs/whatsapp/business-management-api/manage-phone-numbers)';
$lang['phone_number_id'] = 'ID du numéro du téléphone enregistré sur WhatsApp';
$lang['update_details'] = 'Mettre à jour les détails';

$lang['bots'] = 'Bots';
$lang['bots_management'] = 'Gestion des bots';
$lang['create_template_base_bot'] = 'Créer un bot de modèle de base';
$lang['create_message_bot'] = 'Créer un bot de message';
$lang['type'] = 'Type';
$lang['message_bot'] = 'Bot de message';
$lang['new_template_bot'] = 'Nouveau bot de modèle';
$lang['new_message_bot'] = 'Nouveau bot de message';
$lang['bot_name'] = 'Nom du bot';
$lang['reply_text'] = 'Texte de réponse <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Texte qui sera envoyé au prospect ou au contact. Vous pouvez également utiliser {companyname}, {crm_url} ou tout autre champ de fusion personnalisé du prospect ou du contact, ou utiliser le signe \'@\' pour trouver les champs de fusion disponibles" data-placement="bottom"></i> <span class="text-muted">(Le nombre maximum de caractères autorisés est de 1024)</span>';
$lang['reply_type'] = 'Type de réponse';
$lang['trigger'] = 'Déclencheur';
$lang['header'] = 'En-tête';
$lang['footer_bot'] = 'Pied de page <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 60" data-placement="bottom"></i>';
$lang['bot_with_reply_buttons'] = 'Option 1 : Bot avec boutons de réponse';
$lang['bot_with_button_link'] = 'Option 2 : Bot avec lien de bouton - URL CTA';
$lang['button1'] = 'Bouton1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 20" data-placement="bottom"></i>';
$lang['button1_id'] = 'ID du Bouton1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 256" data-placement="bottom"></i>';
$lang['button2'] = 'Bouton2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 20" data-placement="bottom"></i>';
$lang['button2_id'] = 'ID du Bouton2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 256" data-placement="bottom"></i>';
$lang['button3'] = 'Bouton3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 20" data-placement="bottom"></i>';
$lang['button3_id'] = 'ID du Bouton3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 256" data-placement="bottom"></i>';
$lang['button_name'] = 'Nom du bouton <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Le nombre maximum de caractères autorisés est de 20" data-placement="bottom"></i>';
$lang['button_link'] = 'Lien du bouton';
$lang['enter_name'] = 'Entrer le nom';
$lang['select_reply_type'] = 'Sélectionner le type de réponse';
$lang['enter_bot_reply_trigger'] = 'Entrer le déclencheur de réponse du bot';
$lang['enter_header'] = 'Entrer l’en-tête';
$lang['enter_footer'] = 'Entrer le pied de page';
$lang['enter_button1'] = 'Entrer le bouton1';
$lang['enter_button1_id'] = 'Entrer l’ID du bouton1';
$lang['enter_button2'] = 'Entrer le bouton2';
$lang['enter_button2_id'] = 'Entrer l’ID du bouton2';
$lang['enter_button3'] = 'Entrer le bouton3';
$lang['enter_button3_id'] = 'Entrer l’ID du bouton3';
$lang['enter_button_name'] = 'Entrer le nom du bouton';
$lang['enter_button_url'] = 'Entrer l’URL du bouton';
$lang['on_exact_match'] = 'Bot de réponse : Sur correspondance exacte';
$lang['when_message_contains'] = 'Bot de réponse : Quand le message contient';
$lang['when_client_send_the_first_message'] = 'Réponse de bienvenue - quand le prospect ou le client envoie le premier message';
$lang['bot_create_successfully'] = 'Bot créé avec succès';
$lang['bot_update_successfully'] = 'Bot mis à jour avec succès';
$lang['bot_deleted_successfully'] = 'Bot supprimé avec succès';
$lang['templates'] = 'Modèles';
$lang['template_data_loaded'] = 'Modèles chargés avec succès';
$lang['load_templates'] = 'Charger les modèles';
$lang['template_management'] = 'Gestion des modèles';


// campaigns
$lang['campaign'] = 'Campagne';
$lang['campaigns'] = 'Campagnes';
$lang['send_new_campaign'] = 'Envoyer Nouvelle Campagne';
$lang['campaign_name'] = 'Nom de la Campagne';
$lang['template'] = 'Modèle';
$lang['scheduled_send_time'] = '<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Par client, en fonction du fuseau horaire du contact" data-placement="left"></i>Heure d\'envoi planifiée';
$lang['scheduled_time_description'] = 'Par client, en fonction du fuseau horaire du contact';
$lang['ignore_scheduled_time_and_send_now'] = 'Ignorer l\'heure planifiée et envoyer maintenant';
$lang['template'] = 'Modèle';
$lang['leads'] = 'Leads';
$lang['delivered_to'] = 'Livré à';
$lang['read_by'] = 'Lu par';
$lang['variables'] = 'Variables';
$lang['body'] = 'Corps';
$lang['variable'] = 'Variable';
$lang['match_with_selected_field'] = 'Correspondre avec un champ sélectionné';
$lang['preview'] = 'Aperçu';
$lang['send_campaign'] = 'Envoyer la campagne';
$lang['send_to'] = 'Envoyer à';
$lang['send_campaign'] = 'Envoyer Campagne';
$lang['view_campaign'] = 'Voir Campagne';
$lang['campaign_daily_task'] = 'Tâche quotidienne de campagne';
$lang['back'] = 'Retour';
$lang['phone'] = 'Téléphone';
$lang['message'] = 'Message';
$lang['currently_type_not_supported'] = 'Actuellement, le type de modèle <strong> %s </strong> n\'est pas pris en charge !';
$lang['of_your'] = 'de vos ';
$lang['contacts'] = 'Contacts';
$lang['select_all_leads'] = 'Sélectionner tous les Leads';
$lang['select_all_note_leads'] = 'Si vous sélectionnez ceci, tous les futurs leads sont inclus dans cette campagne.';
$lang['select_all_note_contacts'] = 'Si vous sélectionnez ceci, tous les futurs contacts sont inclus dans cette campagne.';

$lang['verified_name'] = 'Nom Vérifié';
$lang['mark_as_default'] = 'Définir par défaut';
$lang['default_number_updated'] = 'ID du numéro de téléphone par défaut mis à jour avec succès';
$lang['currently_using_this_number'] = 'Utilisation actuellement de ce numéro';
$lang['leads'] = 'Leads';
$lang['pause_campaign'] = 'Mettre en Pause la Campagne';
$lang['resume_campaign'] = 'Reprendre la Campagne';
$lang['campaign_resumed'] = 'Campagne reprise';
$lang['campaign_paused'] = 'Campagne mise en pause';

//Template
$lang['body_data'] = 'Données du Corps';
$lang['category'] = 'Catégorie';

// Template bot
$lang['create_new_template_bot'] = 'Créer un nouveau bot modèle';
$lang['template_bot'] = 'Bot Modèle';
$lang['variables'] = 'Variables';
$lang['preview'] = 'Aperçu';
$lang['template'] = 'Modèle';
$lang['bot_content_1'] = 'Ce message sera envoyé au contact une fois que la règle de déclenchement est remplie dans le message envoyé par le contact.';
$lang['save_bot'] = 'Enregistrer le bot';
$lang['please_select_template'] = 'Veuillez sélectionner un modèle';
$lang['use_manually_define_value'] = 'Utiliser la valeur définie manuellement';
$lang['merge_fields'] = 'Champs de Fusion';
$lang['template_bot_create_successfully'] = 'Bot modèle créé avec succès';
$lang['template_bot_update_successfully'] = 'Bot modèle mis à jour avec succès';
$lang['text_bot'] = 'Bot Texte';
$lang['option_2_bot_with_link'] = 'Option 2 : Bot avec lien bouton - Appel à l\'Action (CTA) URL';
$lang['option_3_file'] = 'Option 3 : Bot avec fichier';
// Bot settings
$lang['bot'] = 'Bot';
$lang['bot_delay_response'] = 'Message envoyé lorsque le délai de réponse est attendu';
$lang['bot_delay_response_placeholder'] = 'Donnez-moi un instant, j\'aurai la réponse sous peu';

$lang['whatsbot'] = 'WhatsBot';

//campaigns
$lang['relation_type'] = 'Type de Relation';
$lang['select_all'] = 'Sélectionner tout';
$lang['total'] = 'Total';
$lang['merge_field_note'] = 'Utilisez le signe \'@\' pour ajouter des champs de fusion.';
$lang['send_to_all'] = 'Envoyer à Tous ';
$lang['or'] = 'OU';

$lang['convert_whatsapp_message_to_lead'] = 'Acquérir un Nouveau Lead Automatiquement (convertir les nouveaux messages whatsapp en lead)';
$lang['leads_status'] = 'Statut du Lead';
$lang['leads_assigned'] = 'Lead assigné';
$lang['whatsapp_auto_lead'] = 'Whatsapp Auto Lead';
$lang['webhooks_label'] = 'Les données whatsapp reçues seront renvoyées à';
$lang['webhooks'] = 'WebHooks';
$lang['enable_webhooks'] = 'Activer le Renvoi des WebHooks';
$lang['chat'] = 'Chat';
$lang['black_listed_phone_numbers'] = 'Numéros de téléphone blacklistés';
$lang['sent_status'] = 'Statut Envoyé';

$lang['active'] = 'Actif';
$lang['approved'] = 'Approuvé';
$lang['this_month'] = 'ce mois-ci';
$lang['open_chats'] = 'Chats Ouverts';
$lang['resolved_conversations'] = 'Conversations Résolues';
$lang['messages_sent'] = 'Messages envoyés';
$lang['account_connected'] = 'Compte connecté';
$lang['account_disconnected'] = 'Compte déconnecté';
$lang['webhook_verify_token'] = 'Jeton de vérification du Webhook';
// Chat integration
$lang['chat_message_note'] = 'Le message sera envoyé sous peu. Veuillez noter que si le contact est nouveau, il n\'apparaîtra pas dans cette liste tant que le contact ne commence pas à interagir avec vous !';

$lang['activity_log'] = 'Journal d\'Activité';
$lang['whatsapp_logs'] = 'Journaux Whatsapp';
$lang['response_code'] = 'Code de Réponse';
$lang['recorded_on'] = 'Enregistré le';
$lang['request_details'] = 'Détails de la Demande';
$lang['raw_content'] = 'Contenu Brut';
$lang['headers'] = 'En-têtes';
$lang['format_type'] = 'Type de Format';

// Permission section
$lang['show_campaign'] = 'Afficher la campagne';
$lang['clear_log'] = 'Effacer le Journal';
$lang['log_activity'] = 'Journaliser l\'Activité';
$lang['load_template'] = 'Charger le Modèle';

$lang['action'] = 'Action';
$lang['total_parameters'] = 'Total des Paramètres';
$lang['template_name'] = 'Nom du Modèle';
$lang['log_cleared_successfully'] = 'Journal effacé avec succès';
$lang['whatsbot_stats'] = 'Statistiques WhatsBot';

$lang['not_found_or_deleted'] = 'Non trouvé ou supprimé';
$lang['response'] = 'Réponse';

$lang['select_image'] = 'Sélectionner une image';
$lang['image'] = 'Image';
$lang['image_deleted_successfully'] = 'Image supprimée avec succès';
$lang['whatsbot_settings'] = 'Paramètres WhatsBot';
$lang['maximum_file_size_should_be'] = 'La taille de fichier maximale doit être ';
$lang['allowed_file_types'] = 'Types de fichiers autorisés : ';

$lang['send_image'] = 'Envoyer une Image';
$lang['send_video'] = 'Envoyer une Vidéo';
$lang['send_document'] = 'Envoyer un Document';
$lang['record_audio'] = 'Enregistrer un Audio';
$lang['chat_media_info'] = 'Plus d\'infos sur les Types de Contenu Supportés & Taille de Média Post-Processing';
$lang['help'] = 'Aide';

// v1.1.0
$lang['clone'] = 'Cloner';
$lang['bot_clone_successfully'] = 'Bot cloné avec succès';
$lang['all_chat'] = 'Tous les Chats';
$lang['from'] = 'De :';
$lang['phone_no'] = 'Numéro de Téléphone :';
$lang['supportagent'] = 'Agent de Support';
$lang['assign_chat_permission_to_support_agent'] = 'Attribuer l\'autorisation de chat uniquement à l\'agent de support';
$lang['enable_whatsapp_notification_sound'] = 'Activer le son de notification de chat WhatsApp';
$lang['notification_sound'] = 'Son de Notification';
$lang['trigger_keyword'] = 'Mot-Clé Déclencheur';
$lang['modal_title'] = 'Sélectionner l\'Agent de Support';
$lang['close_btn'] = 'Fermer';
$lang['save_btn'] = 'Enregistrer';
$lang['support_agent'] = 'Agent de Support';
$lang['change_support_agent'] = 'Changer l\'Agent de Support';
$lang['replay_message'] = 'Vous ne pouvez pas envoyer de message, 24 heures sont écoulées.';
$lang['support_agent_note'] = '<strong>Remarque : </strong>Lorsque vous activez la fonction d\'agent de support, l\'attributaire du lead sera automatiquement affecté au chat. Les administrateurs peuvent également attribuer un nouvel agent depuis la page de chat.';
$lang['permission_bot_clone'] = 'Cloner le Bot';
$lang['remove_chat'] = 'Supprimer le Chat';
$lang['default_message_on_no_match'] = 'Réponse par Défaut - si aucun mot-clé ne correspond';
$lang['default_message_note'] = '<strong>Remarque : </strong>Activer cette option augmentera votre charge de webhook. Pour plus d\'informations, visitez ce <a href="https://docs.corbitaltech.dev/products/whatsbot/index.html" target="_blank">lien</a>.';

$lang['whatsbot_connect_account'] = 'Compte WhatsBot Connecté';
$lang['whatsbot_message_bot'] = 'Bot de Message WhatsBot';
$lang['whatsbot_template_bot'] = 'Bot de Modèle WhatsBot';
$lang['whatsbot_template'] = 'Modèle WhatsBot';
$lang['whatsbot_campaigns'] = 'Campagnes WhatsBot';
$lang['whatsbot_chat'] = 'Chat WhatsBot';
$lang['whatsbot_log_activity'] = 'Journal d\'Activité WhatsBot';
$lang['message_templates_not_exists_note'] = 'Permission de modèle méta manquante. Veuillez l\'activer dans votre compte Meta.';

// v1.2.0
$lang['ai_prompt'] = 'Invites IA';
$lang['ai_prompt_note'] = 'Pour les invites IA, veuillez saisir un message pour activer la fonction, ou utilisez des invites IA si déjà activées';
$lang['emojis'] = 'Émojis';
$lang['translate'] = 'Traduire';
$lang['change_tone'] = 'Changer de Ton';
$lang['professional'] = 'Professionnel';
$lang['friendly'] = 'Amical';
$lang['empathetic'] = 'Empathique';
$lang['straightforward'] = 'Franc';
$lang['simplify_language'] = 'Simplifier le Langage';
$lang['fix_spelling_and_grammar'] = 'Corriger l\'Orthographe & la Grammaire';

$lang['ai_integration'] = 'Intégration IA';
$lang['open_ai_api'] = 'API OpenAI';
$lang['open_ai_secret_key'] = 'Clé Secrète OpenAI - <a href="https://platform.openai.com/account/api-keys" target="_blank">Où trouver la clé secrète ?</a>';
$lang['chat_text_limit'] = 'Limite de Texte de Chat';
$lang['chat_text_limit_note'] = 'Pour optimiser les coûts opérationnels, envisagez de limiter le nombre de mots des réponses de chat d\'OpenAI';
$lang['chat_model'] = 'Modèle de Chat';
$lang['openai_organizations'] = 'Organisations OpenAi';
$lang['template_type'] = 'Type de Modèle';
$lang['update'] = 'Mettre à Jour';
$lang['open_ai_key_verification_fail'] = 'La Vérification de la Clé OpenAi est en Attente dans les paramètres, veuillez connecter votre compte OpenAI';
$lang['enable_wb_openai'] = 'Activer OpenAI dans le chat';
$lang['webhook_resend_method'] = 'Méthode de Renvoi du Webhook';
$lang['search_language'] = 'Rechercher une langue...';
$lang['document'] = 'Document';
$lang['select_document'] = 'Sélectionner le Document';
$lang['attchment_deleted_successfully'] = 'Pièce Jointe Supprimée Avec Succès';
$lang['attach_image_video_docs'] = 'Joindre des Images, Vidéos, Documents';
$lang['choose_file_type'] = 'Choisir le Type de Fichier';
$lang['max_size'] = 'Taille Max : ';
// v1.3.0

// CSV import
$lang['bulk_campaigns']  = 'Campagnes en Vrac';
$lang['upload_csv'] = 'Télécharger CSV';
$lang['upload'] = 'Télécharger';
$lang['csv_uploaded_successfully'] = 'Fichier CSV téléchargé avec succès';
$lang['please_select_file'] = 'Veuillez sélectionner un fichier CSV';
$lang['phonenumber_field_is_required'] = 'Le champ numéro de téléphone est requis';
$lang['out_of_the'] = 'Sur les';
$lang['records_in_your_csv_file'] = 'enregistrements dans votre fichier CSV,';
$lang['valid_the_campaign_can_be_sent'] = 'les enregistrements sont valides.<br /> La campagne peut être envoyée avec succès à ces';
$lang['users'] = 'utilisateurs';
$lang['campaigns_from_csv_file'] = 'Campagnes à partir du fichier CSV';
$lang['download_sample'] = 'Télécharger un Échantillon';
$lang['csv_rule_1'] = '1. <b>Exigence de la colonne Numéro de Téléphone :</b> Votre fichier CSV doit inclure une colonne nommée "Phoneno." Chaque enregistrement dans cette colonne doit contenir un numéro de contact valide, correctement formaté avec l\'indicatif du pays, incluant le signe "+" . <br /><br />';
$lang['csv_rule_2'] = '2. <b>Format et Encodage CSV :</b> Vos données CSV doivent suivre le format spécifié. La première ligne de votre fichier CSV doit contenir les en-têtes de colonne, comme indiqué dans le tableau d\'exemple. Assurez-vous que votre fichier est encodé en UTF-8 pour éviter tout problème d\'encodage.';
$lang['please_upload_valid_csv_file'] = 'Veuillez télécharger un fichier CSV valide';
$lang['please_add_valid_number_in_csv_file'] = 'Veuillez ajouter un <b>Phoneno</b> valide dans le fichier CSV';
$lang['total_send_campaign_list'] = 'Total des campagnes envoyées : %s';
$lang['sample_data'] = 'Données d\'Exemple';
$lang['firstname'] = 'Prénom';
$lang['lastname'] = 'Nom';
$lang['phoneno'] = 'Numéro de Téléphone';
$lang['email'] = 'Email';
$lang['country'] = 'Pays';
$lang['download_sample_and_read_rules'] = 'Télécharger le Fichier Échantillon & Lire les Règles';
$lang['please_wait_your_request_in_process'] = 'Veuillez patienter, votre demande est en cours de traitement.';
$lang['whatsbot_bulk_campaign'] = 'Campagnes en Vrac WhatsBot';
$lang['csv_campaign'] = 'Campagne CSV';

// Canned reply
$lang['canned_reply'] = 'Réponse Préréglée';
$lang['canned_reply_menu'] = 'Réponse Préréglée';
$lang['create_canned_reply'] = 'Créer une Réponse Préréglée';
$lang['title'] = 'Titre';
$lang['desc'] = 'Description';
$lang['public'] = 'Public';
$lang['action'] = 'Action';
$lang['delete_successfully'] = 'Réponse supprimée.';
$lang['cannot_delete'] = 'La réponse ne peut pas être supprimée.';
$lang['whatsbot_canned_reply'] = 'Réponse Préréglée WhatsBot';
$lang['reply'] = 'Répondre';

//AI Prompts
$lang['ai_prompts'] = 'Invites IA';
$lang['create_ai_prompts'] = 'Créer des Invites IA';
$lang['name'] = 'Nom';
$lang['action'] = 'Action';
$lang['prompt_name'] = 'Nom de l\'Invite';
$lang['prompt_action'] = 'Action de l\'Invite';
$lang['whatsbot_ai_prompts'] = 'Invites IA WhatsBot';

// new chat
$lang['replying_to'] = 'Répondant à :';
$lang['download_document'] = 'Télécharger le Document';
$lang['custom_prompt'] = 'Invite Personnalisée';
$lang['canned_replies'] = 'Réponses Préréglées';
$lang['use_@_to_add_merge_fields'] = 'Utilisez \'@\' pour ajouter des champs de fusion';
$lang['type_your_message'] = 'Tapez votre message';
$lang['you_cannot_send_a_message_using_this_number'] = 'Vous ne pouvez pas envoyer de message en utilisant ce numéro.';

// bot flow 
$lang['bot_flow'] = 'Flux du Bot';
$lang['create_new_flow'] = 'Créer un Nouveau Flux';
$lang['flow_name'] = 'Nom du Flux';
$lang['flow'] = 'Flux';
$lang['bot_flow_builder'] = 'Constructeur de Flux du Bot';
$lang['you_can_not_upload_file_type'] = 'Vous ne pouvez pas télécharger le type de fichier <b> %s </b>';
$lang['whatsbot_bot_flow'] = 'Flux du Bot WhatsBot';

// v1.3.2
$lang['auto_clear_chat_history'] = 'Effacement Automatique de l\'Historique des Chats';
$lang['enable_auto_clear_chat_history'] = 'Activer l\'Effacement Automatique de l\'Historique des Chats';
$lang['auto_clear_time'] = 'Temps d\'Effacement Automatique de l\'Historique';
$lang['clear_chat_history_note'] = '<strong>Remarque : </strong> Si vous activez la fonction d\'effacement automatique de l\'historique des chats, cela effacera automatiquement l\'historique des chats en fonction du nombre de jours que vous spécifiez, chaque fois que le travail cron s\'exécute.';
$lang['source'] = 'Source';
$lang['groups'] = 'Groupes';

// v1.3.3
$lang['click_user_to_chat'] = 'Cliquez sur l\'utilisateur pour discuter';
