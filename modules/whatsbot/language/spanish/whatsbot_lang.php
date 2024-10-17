<?php

defined('BASEPATH') || exit('No direct script access allowed');

// conectar cuenta
$lang['whatsbot'] = 'WhatsBot';
$lang['connect_account'] = 'Conectar cuenta';
$lang['connect_whatsapp_business'] = 'Conectar WhatsApp Business';
$lang['campaigning'] = 'Campaña';
$lang['business_account_id_description'] = 'Tu ID de la cuenta de WhatsApp Business (WABA)';
$lang['access_token_description'] = 'Tu token de acceso de usuario después de registrarte en el portal de desarrolladores de Facebook';
$lang['whatsapp_business_account_id'] = 'ID de cuenta de WhatsApp Business';
$lang['whatsapp_access_token'] = 'Token de acceso de WhatsApp';
$lang['webhook_callback_url'] = 'URL de devolución de llamada del webhook';
$lang['verify_token'] = 'Verificar token';
$lang['connect'] = 'Conectar';
$lang['whatsapp'] = 'WhatsApp';
$lang['one_click_account_connection'] = 'Conexión de cuenta con un clic';
$lang['connect_your_whatsapp_account'] = 'Conecta tu cuenta de WhatsApp';
$lang['copy'] = 'Copiar';
$lang['copied'] = '¡Copiado!';
$lang['disconnect'] = 'Desconectar';
$lang['number'] = 'Número';
$lang['number_id'] = 'ID del número';
$lang['quality'] = 'Calidad';
$lang['status'] = 'Estado';
$lang['business_account_id'] = 'ID de cuenta comercial';
$lang['permissions'] = 'Permisos';
$lang['phone_number_id_description'] = 'ID del número de teléfono conectado a la API de WhatsApp Business. Si no estás seguro, puedes usar una solicitud GET Phone Number ID para obtenerlo de la API de WhatsApp (https://developers.facebook.com/docs/whatsapp/business-management-api/manage-phone-numbers)';
$lang['phone_number_id'] = 'ID del número de teléfono registrado en WhatsApp';
$lang['update_details'] = 'Actualizar detalles';

$lang['bots'] = 'Bots';
$lang['bots_management'] = 'Gestión de bots';
$lang['create_template_base_bot'] = 'Crear bot basado en plantilla';
$lang['create_message_bot'] = 'Crear bot de mensajes';
$lang['type'] = 'Tipo';
$lang['message_bot'] = 'Bot de mensajes';
$lang['new_template_bot'] = 'Nuevo bot de plantilla';
$lang['new_message_bot'] = 'Nuevo bot de mensajes';
$lang['bot_name'] = 'Nombre del bot';
$lang['reply_text'] = 'Texto de respuesta <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Texto que se enviará al lead o contacto. También puedes usar {companyname}, {crm_url} u otros campos de combinación personalizados de lead o contacto, o usar el signo \'@\' para encontrar campos de combinación disponibles" data-placement="bottom"></i> <span class="text-muted">(El máximo permitido es 1024 caracteres)</span>';
$lang['reply_type'] = 'Tipo de respuesta';
$lang['trigger'] = 'Desencadenante';
$lang['header'] = 'Encabezado';
$lang['footer_bot'] = 'Pie de página <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 60 caracteres" data-placement="bottom"></i>';
$lang['bot_with_reply_buttons'] = 'Opción 1: Bot con botones de respuesta';
$lang['bot_with_button_link'] = 'Opción 2: Bot con botón de enlace - URL CTA';
$lang['button1'] = 'Botón1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 20 caracteres" data-placement="bottom"></i>';
$lang['button1_id'] = 'ID del botón1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 256 caracteres" data-placement="bottom"></i>';
$lang['button2'] = 'Botón2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 20 caracteres" data-placement="bottom"></i>';
$lang['button2_id'] = 'ID del botón2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 256 caracteres" data-placement="bottom"></i>';
$lang['button3'] = 'Botón3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 20 caracteres" data-placement="bottom"></i>';
$lang['button3_id'] = 'ID del botón3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 256 caracteres" data-placement="bottom"></i>';
$lang['button_name'] = 'Nombre del botón <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="El máximo permitido es 20 caracteres" data-placement="bottom"></i>';
$lang['button_link'] = 'Enlace del botón';
$lang['enter_name'] = 'Introduce el nombre';
$lang['select_reply_type'] = 'Selecciona el tipo de respuesta';
$lang['enter_bot_reply_trigger'] = 'Introduce el desencadenante de la respuesta del bot';
$lang['enter_header'] = 'Introduce el encabezado';
$lang['enter_footer'] = 'Introduce el pie de página';
$lang['enter_button1'] = 'Introduce el botón1';
$lang['enter_button1_id'] = 'Introduce el ID del botón1';
$lang['enter_button2'] = 'Introduce el botón2';
$lang['enter_button2_id'] = 'Introduce el ID del botón2';
$lang['enter_button3'] = 'Introduce el botón3';
$lang['enter_button3_id'] = 'Introduce el ID del botón3';
$lang['enter_button_name'] = 'Introduce el nombre del botón';
$lang['enter_button_url'] = 'Introduce la URL del botón';
$lang['on_exact_match'] = 'Bot de respuesta: En coincidencia exacta';
$lang['when_message_contains'] = 'Bot de respuesta: Cuando el mensaje contiene';
$lang['when_client_send_the_first_message'] = 'Respuesta de bienvenida: Cuando el lead o cliente envía el primer mensaje';
$lang['bot_create_successfully'] = 'Bot creado con éxito';
$lang['bot_update_successfully'] = 'Bot actualizado con éxito';
$lang['bot_deleted_successfully'] = 'Bot eliminado con éxito';
$lang['templates'] = 'Plantillas';
$lang['template_data_loaded'] = 'Plantillas cargadas con éxito';
$lang['load_templates'] = 'Cargar plantillas';
$lang['template_management'] = 'Gestión de plantillas';

// campaigns
$lang['campaign'] = 'Campaña';
$lang['campaigns'] = 'Campañas';
$lang['send_new_campaign'] = 'Enviar Nueva Campaña';
$lang['campaign_name'] = 'Nombre de la Campaña';
$lang['template'] = 'Plantilla';
$lang['scheduled_send_time'] = '<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Por cliente, basado en la zona horaria de contacto" data-placement="left"></i>Hora de Envío Programada';
$lang['scheduled_time_description'] = 'Por cliente, basado en la zona horaria de contacto';
$lang['ignore_scheduled_time_and_send_now'] = 'Ignorar hora programada y enviar ahora';
$lang['template'] = 'Plantilla';
$lang['leads'] = 'Prospectos';
$lang['delivered_to'] = 'Entregado a';
$lang['read_by'] = 'Leído por';
$lang['variables'] = 'Variables';
$lang['body'] = 'Cuerpo';
$lang['variable'] = 'Variable';
$lang['match_with_selected_field'] = 'Coincidir con un campo seleccionado';
$lang['preview'] = 'Vista previa';
$lang['send_campaign'] = 'Enviar campaña';
$lang['send_to'] = 'Enviar a';
$lang['send_campaign'] = 'Enviar Campaña';
$lang['view_campaign'] = 'Ver Campaña';
$lang['campaign_daily_task'] = 'Tarea diaria de campaña';
$lang['back'] = 'Atrás';
$lang['phone'] = 'Teléfono';
$lang['message'] = 'Mensaje';
$lang['currently_type_not_supported'] = 'Actualmente el tipo de plantilla <strong> %s </strong> no es compatible!';
$lang['of_your'] = 'de tu ';
$lang['contacts'] = 'Contactos';
$lang['select_all_leads'] = 'Seleccionar todos los Prospectos';
$lang['select_all_note_leads'] = 'Si seleccionas esto, todos los futuros prospectos están incluidos en esta campaña.';
$lang['select_all_note_contacts'] = 'Si seleccionas esto, todos los futuros contactos están incluidos en esta campaña.';

$lang['verified_name'] = 'Nombre Verificado';
$lang['mark_as_default'] = 'Marcar como predeterminado';
$lang['default_number_updated'] = 'ID de número de teléfono predeterminado actualizado con éxito';
$lang['currently_using_this_number'] = 'Actualmente usando este número';
$lang['leads'] = 'Prospectos';
$lang['pause_campaign'] = 'Pausar Campaña';
$lang['resume_campaign'] = 'Reanudar Campaña';
$lang['campaign_resumed'] = 'Campaña reanudada';
$lang['campaign_paused'] = 'Campaña pausada';

//Template
$lang['body_data'] = 'Datos del Cuerpo';
$lang['category'] = 'Categoría';

// Template bot
$lang['create_new_template_bot'] = 'Crear nuevo bot de plantilla';
$lang['template_bot'] = 'Bot de Plantilla';
$lang['variables'] = 'Variables';
$lang['preview'] = 'Vista previa';
$lang['template'] = 'Plantilla';
$lang['bot_content_1'] = 'Este mensaje se enviará al contacto una vez que se cumpla la regla de activación en el mensaje enviado por el contacto.';
$lang['save_bot'] = 'Guardar bot';
$lang['please_select_template'] = 'Por favor, selecciona una plantilla';
$lang['use_manually_define_value'] = 'Usar valor definido manualmente';
$lang['merge_fields'] = 'Campos Combinados';
$lang['template_bot_create_successfully'] = 'Bot de plantilla creado con éxito';
$lang['template_bot_update_successfully'] = 'Bot de plantilla actualizado con éxito';
$lang['text_bot'] = 'Bot de Texto';
$lang['option_2_bot_with_link'] = 'Opción 2: Bot con botón de enlace - Llamado a la Acción (CTA) URL';
$lang['option_3_file'] = 'Opción 3: Bot con archivo';
// Bot settings
$lang['bot'] = 'Bot';
$lang['bot_delay_response'] = 'Mensaje enviado cuando se espera un retraso en la respuesta';
$lang['bot_delay_response_placeholder'] = 'Dame un momento, tendré la respuesta en breve';

$lang['whatsbot'] = 'WhatsBot';

//campaigns
$lang['relation_type'] = 'Tipo de Relación';
$lang['select_all'] = 'Seleccionar todo';
$lang['total'] = 'Total';
$lang['merge_field_note'] = 'Usa el signo \'@\' para agregar campos combinados.';
$lang['send_to_all'] = 'Enviar a Todos ';
$lang['or'] = 'O';

$lang['convert_whatsapp_message_to_lead'] = 'Adquirir Nuevo Prospecto Automáticamente (convertir nuevos mensajes de whatsapp en prospectos)';
$lang['leads_status'] = 'Estado del prospecto';
$lang['leads_assigned'] = 'Prospecto asignado';
$lang['whatsapp_auto_lead'] = 'Whatsapp Auto Lead';
$lang['webhooks_label'] = 'Los datos de whatsapp recibidos se reenviarán a';
$lang['webhooks'] = 'WebHooks';
$lang['enable_webhooks'] = 'Habilitar Reenvío de WebHooks';
$lang['chat'] = 'Chat';
$lang['black_listed_phone_numbers'] = 'Números de teléfono en lista negra';
$lang['sent_status'] = 'Estado Enviado';

$lang['active'] = 'Activo';
$lang['approved'] = 'Aprobado';
$lang['this_month'] = 'este mes';
$lang['open_chats'] = 'Chats Abiertos';
$lang['resolved_conversations'] = 'Conversaciones Resueltas';
$lang['messages_sent'] = 'Mensajes enviados';
$lang['account_connected'] = 'Cuenta conectada';
$lang['account_disconnected'] = 'Cuenta desconectada';
$lang['webhook_verify_token'] = 'Token de verificación del webhook';
// Chat integration
$lang['chat_message_note'] = 'El mensaje se enviará en breve. Ten en cuenta que si es un nuevo contacto, no aparecerá en esta lista hasta que el contacto comience a interactuar contigo!';

$lang['activity_log'] = 'Registro de Actividades';
$lang['whatsapp_logs'] = 'Registros de Whatsapp';
$lang['response_code'] = 'Código de Respuesta';
$lang['recorded_on'] = 'Grabado En';

$lang['request_details'] = 'Detalles de la Solicitud';
$lang['raw_content'] = 'Contenido en Crudo';
$lang['headers'] = 'Cabeceras';
$lang['format_type'] = 'Tipo de Formato';

// Permission section
$lang['show_campaign'] = 'Mostrar campaña';
$lang['clear_log'] = 'Borrar Registro';
$lang['log_activity'] = 'Registro de Actividades';
$lang['load_template'] = 'Cargar Plantilla';

$lang['action'] = 'Acción';
$lang['total_parameters'] = 'Total de Parámetros';
$lang['template_name'] = 'Nombre de la Plantilla';
$lang['log_cleared_successfully'] = 'Registro borrado con éxito';
$lang['whatsbot_stats'] = 'Estadísticas de WhatsBot';

$lang['not_found_or_deleted'] = 'No encontrado o borrado';
$lang['response'] = 'Respuesta';

$lang['select_image'] = 'Seleccionar imagen';
$lang['image'] = 'Imagen';
$lang['image_deleted_successfully'] = 'Imagen borrada con éxito';
$lang['whatsbot_settings'] = 'Configuraciones de Whatsbot';
$lang['maximum_file_size_should_be'] = 'El tamaño máximo del archivo debe ser ';
$lang['allowed_file_types'] = 'Tipos de archivo permitidos: ';

$lang['send_image'] = 'Enviar Imagen';
$lang['send_video'] = 'Enviar Video';
$lang['send_document'] = 'Enviar Documento';
$lang['record_audio'] = 'Grabar Audio';
$lang['chat_media_info'] = 'Más información sobre Tipos de Contenido Soportados y Tamaño de Medios Post-Procesamiento';
$lang['help'] = 'Ayuda';

// v1.1.0
$lang['clone'] = 'Clonar';
$lang['bot_clone_successfully'] = 'Bot clonado con éxito';
$lang['all_chat'] = 'Todos los Chats';
$lang['from'] = 'De:';
$lang['phone_no'] = 'Teléfono:';
$lang['supportagent'] = 'Agente de Soporte';
$lang['assign_chat_permission_to_support_agent'] = 'Asignar permiso de chat solo al agente de soporte';
$lang['enable_whatsapp_notification_sound'] = 'Habilitar sonido de notificación de chat de whatsapp';
$lang['notification_sound'] = 'Sonido de Notificación';
$lang['trigger_keyword'] = 'Palabra Clave Desencadenante';
$lang['modal_title'] = 'Seleccionar Agente de Soporte';
$lang['close_btn'] = 'Cerrar';
$lang['save_btn'] = 'Guardar';
$lang['support_agent'] = 'Agente de Soporte';
$lang['change_support_agent'] = 'Cambiar Agente de Soporte';
$lang['replay_message'] = 'No puedes enviar un mensaje, han pasado 24 horas.';
$lang['support_agent_note'] = '<strong>Nota: </strong>Cuando habilitas la función del agente de soporte, el asignado al prospecto se asignará automáticamente al chat. Los administradores también pueden asignar un nuevo agente desde la página de chat.';
$lang['permission_bot_clone'] = 'Clonar Bot';
$lang['remove_chat'] = 'Eliminar Chat';
$lang['default_message_on_no_match'] = 'Respuesta Predeterminada - si no coincide con ninguna palabra clave';
$lang['default_message_note'] = '<strong>Nota: </strong>Activar esta opción aumentará la carga de tu webhook. Para más información visita este <a href="https://docs.corbitaltech.dev/products/whatsbot/index.html" target="_blank">enlace</a>.';


$lang['whatsbot_connect_account'] = 'Conectar cuenta de Whatsbot';
$lang['whatsbot_message_bot'] = 'Bot de mensajes de Whatsbot';
$lang['whatsbot_template_bot'] = 'Bot de plantillas de Whatsbot';
$lang['whatsbot_template'] = 'Plantilla de Whatsbot';
$lang['whatsbot_campaigns'] = 'Campañas de Whatsbot';
$lang['whatsbot_chat'] = 'Chat de Whatsbot';
$lang['whatsbot_log_activity'] = 'Registro de actividad de Whatsbot';
$lang['message_templates_not_exists_note'] = 'Permiso de plantilla meta faltante. Por favor habilítalo en tu cuenta de Meta';

// v1.2.0
$lang['ai_prompt'] = 'Prompts de IA';
$lang['ai_prompt_note'] = 'Para los prompts de IA, por favor ingresa un mensaje para habilitar la función, o usa los prompts de IA si ya están habilitados';
$lang['emojis'] = 'Emojis';
$lang['translate'] = 'Traducir';
$lang['change_tone'] = 'Cambiar tono';
$lang['professional'] = 'Profesional';
$lang['friendly'] = 'Amistoso';
$lang['empathetic'] = 'Empático';
$lang['straightforward'] = 'Directo';
$lang['simplify_language'] = 'Simplificar lenguaje';
$lang['fix_spelling_and_grammar'] = 'Corregir ortografía y gramática';

$lang['ai_integration'] = 'Integración de IA';
$lang['open_ai_api'] = 'API de OpenAI';
$lang['open_ai_secret_key'] = 'Clave secreta de OpenAI - <a href="https://platform.openai.com/account/api-keys" target="_blank">¿Dónde puedes encontrar la clave secreta?</a>';
$lang['chat_text_limit'] = 'Límite de texto de chat';
$lang['chat_text_limit_note'] = 'Para optimizar los costos operativos, considera limitar el recuento de palabras de las respuestas de chat de OpenAI';
$lang['chat_model'] = 'Modelo de chat';
$lang['openai_organizations'] = 'Organizaciones de OpenAI';
$lang['template_type'] = 'Tipo de plantilla';
$lang['update'] = 'Actualizar';
$lang['open_ai_key_verification_fail'] = 'La verificación de la clave de OpenAI está pendiente de la configuración, por favor conecta tu cuenta de OpenAI';
$lang['enable_wb_openai'] = 'Habilitar OpenAI en chat';
$lang['webhook_resend_method'] = 'Método de reenvío de webhook';
$lang['search_language'] = 'Buscar idioma...';
$lang['document'] = 'Documento';
$lang['select_document'] = 'Seleccionar documento';
$lang['attchment_deleted_successfully'] = 'Adjunto eliminado con éxito';
$lang['attach_image_video_docs'] = 'Adjuntar imágenes, videos y documentos';
$lang['choose_file_type'] = 'Elegir tipo de archivo';
$lang['max_size'] = 'Tamaño máximo: ';

// v1.3.0

// CSV import
$lang['bulk_campaigns']  = 'Campañas masivas';
$lang['upload_csv'] = 'Subir CSV';
$lang['upload'] = 'Subir';
$lang['csv_uploaded_successfully'] = 'Archivo CSV subido con éxito';
$lang['please_select_file'] = 'Por favor selecciona el archivo CSV';
$lang['phonenumber_field_is_required'] = 'El campo del número de teléfono es obligatorio';
$lang['out_of_the'] = 'De los';
$lang['records_in_your_csv_file'] = 'registros en tu archivo CSV,';
$lang['valid_the_campaign_can_be_sent'] = 'los registros son válidos.<br /> La campaña se puede enviar con éxito a estos';
$lang['users'] = 'usuarios';
$lang['campaigns_from_csv_file'] = 'Campañas desde archivo CSV';
$lang['download_sample'] = 'Descargar muestra';
$lang['csv_rule_1'] = '1. <b>Requisito de columna de número de teléfono:</b> Tu archivo CSV debe incluir una columna llamada "Phoneno". Cada registro en esta columna debe contener un número de contacto válido, formateado correctamente con el código de país, incluyendo el signo "+". <br /><br />';
$lang['csv_rule_2'] = '2. <b>Formato y codificación de CSV:</b> Tus datos CSV deben seguir el formato especificado. La primera fila de tu archivo CSV debe contener los encabezados de columna, como se muestra en la tabla de ejemplo. Asegúrate de que tu archivo esté codificado en UTF-8 para evitar problemas de codificación.';
$lang['please_upload_valid_csv_file'] = 'Por favor, sube un archivo CSV válido';
$lang['please_add_valid_number_in_csv_file'] = 'Por favor agrega un <b>Phoneno</b> válido en el archivo CSV';
$lang['total_send_campaign_list'] = 'Total de campañas enviadas: %s';
$lang['sample_data'] = 'Datos de muestra';
$lang['firstname'] = 'Nombre';
$lang['lastname'] = 'Apellido';
$lang['phoneno'] = 'Número de teléfono';
$lang['email'] = 'Correo electrónico';
$lang['country'] = 'País';
$lang['download_sample_and_read_rules'] = 'Descargar archivo de muestra y leer reglas';
$lang['please_wait_your_request_in_process'] = 'Por favor espera, tu solicitud se está procesando.';
$lang['whatsbot_bulk_campaign'] = 'Campañas masivas de Whatsbot';
$lang['csv_campaign'] = 'Campaña CSV';

// Canned reply
$lang['canned_reply'] = 'Respuesta predeterminada';
$lang['canned_reply_menu'] = 'Respuesta predeterminada';
$lang['create_canned_reply'] = 'Crear respuesta predeterminada';
$lang['title'] = 'Título';
$lang['desc'] = 'Descripción';
$lang['public'] = 'Público';
$lang['action'] = 'Acción';
$lang['delete_successfully'] = 'Respuesta eliminada.';
$lang['cannot_delete'] = 'No se puede eliminar la respuesta.';
$lang['whatsbot_canned_reply'] = 'Respuesta predeterminada de Whatsbot';
$lang['reply'] = 'Responder';

//AI Prompts
$lang['ai_prompts'] = 'Prompts de IA';
$lang['create_ai_prompts'] = 'Crear prompts de IA';
$lang['name'] = 'Nombre';
$lang['action'] = 'Acción';
$lang['prompt_name'] = 'Nombre del prompt';
$lang['prompt_action'] = 'Acción del prompt';
$lang['whatsbot_ai_prompts'] = 'Prompts de IA de Whatsbot';

// new chat
$lang['replying_to'] = 'Respondiendo a:';
$lang['download_document'] = 'Descargar documento';
$lang['custom_prompt'] = 'Prompt personalizado';
$lang['canned_replies'] = 'Respuestas predeterminadas';
$lang['use_@_to_add_merge_fields'] = 'Usa \'@\' para agregar campos de combinación';
$lang['type_your_message'] = 'Escribe tu mensaje';
$lang['you_cannot_send_a_message_using_this_number'] = 'No puedes enviar un mensaje usando este número.';

// bot flow 
$lang['bot_flow'] = 'Flujo de bot';
$lang['create_new_flow'] = 'Crear nuevo flujo';
$lang['flow_name'] = 'Nombre del flujo';
$lang['flow'] = 'Flujo';
$lang['bot_flow_builder'] = 'Constructor de flujo de bot';
$lang['you_can_not_upload_file_type'] = 'No puedes subir un archivo de tipo <b> %s </b>';
$lang['whatsbot_bot_flow'] = 'Flujo de bot de Whatsbot';

// v1.3.2
$lang['auto_clear_chat_history'] = 'Borrar automáticamente el historial de chat';
$lang['enable_auto_clear_chat_history'] = 'Habilitar borrado automático del historial de chat';
$lang['auto_clear_time'] = 'Tiempo de borrado automático del historial';
$lang['clear_chat_history_note'] = '<strong>Nota: </strong> Si habilitas la función de borrado automático del historial de chat, se borrará automáticamente el historial de chat según la cantidad de días que especifiques, cada vez que se ejecute el trabajo cron.';
$lang['source'] = 'Fuente';
$lang['groups'] = 'Grupos';


// v1.3.3
$lang['click_user_to_chat'] = 'Haga clic en el usuario para chatear';
