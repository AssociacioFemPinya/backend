<?php

return [

    /***********/
    /* GENERAL */
    /***********/

    // MENUS

    "general_menu_help" => "Este es el menú de AYUDA.",
    "general_menu_info" => "Información general: ",
    "general_menu_help_nom" => "Nombre: ",
    "general_menu_help_colla" => "Colla: ",

    "mainMenuHome" => "Inicio",
    "mainMenuEvents" => "Agenda",
    "mainMenuOptions" => "Opciones",
    "mainMenuHelp" => "?",
    "mainMenuTecnica" => "Técnica",

    "eventsActuacio" => "Actuaciones",
    "eventsAssaig" => "Ensayos",
    "eventsActivitat" => "Actividades",
    "eventsUnanswered" => "Sin respuesta",
    "eventsVerifyAttendance" => "Verifica asistencia",

    "eventsAttendanceAllOk" => "Vengo a todo",
    "eventsAttendanceAllNok" => "No vengo a todo",
    "eventsAttendanceAllUnknown" => "No lo sé a todo",

    "eventsAttendanceOk" => "Vengo",
    "eventsAttendanceNok" => "No vengo",
    "eventsAttendanceUnknown" => "No lo sé",

    "tecnicaAttendance" => "Asistencia",
    "tecnicaReminders" => "Notificaciones",
    "tecnicaSearch" => "Búsqueda",

    "optionsLinkingCastellers" => "Enlaza o cambia persona",
    "optionsLanguage" => "Idioma",
    "optionsSwitchTextEmojis" => "Texto o Emojis",
    "optionsLogOut" => "Desvincula persona",
    "optionsURLMember" => "Link web",
    "optionsURLMemberClick" => "Clica en el siguiente enlace para accedir a la web:",
    "optionsForbiddenURLMemberClick" => "No tienes acceso al web. Debes pedirlo a las personas responsables de tu colla.",

    "pinyesPinya" => "Pinya",
    "pinyesRondes" => "Rondas",

    "optionsLinkCasteller" => "Enlaza persona",
    "optionsSwitchCasteller" => "Cambia persona activa",

    "tecnicaSendRemindersExpres" => "Express",
    "tecnicaSendRemindersOnline" => "Online",
    "tecnicaSendRemindersTags" => "Personalizado",


    // NO DISPLAY
    "general_menu_home" => "Este es el menú de INICIO.",
    "general_menu_options" => "Este es el menú de OPCIONES.",
    "general_menu_languages" => "Este es el menú de IDIOMAS.",
    "general_menu_events" => "Este es el menú de los EVENTOS.",
    "general_menu_tecnica" => "Este es el menú de la TÉCNICA.",
    "general_menu_linked_castellers" => "Este es el menú de las PERSONAS ENLAZADAS.",

    // Pinyes
    "mainMenuPinyes" => "Piñas",
    "pinya" => "Piñas",
    "rondes" => "Rondas",
    "pinya_en_obres" => "Esta funcionalidad todavía no está implementada",
    "open_url_pinyes" => "Abrid el siguiente enlace para ver la piña",
    "rondes_empty" => "El evento no tiene Rondas publicadas.",
    "next_event" => "Próximo Evento: ",


    // QUESTIONS

    "general_choose_option" => "Elige una opción, por favor:",
    "general_use_menu" => "En lugar de escribir, usa los botones de menú, por favor: ",
    "general_message_length_is_incorrect" => "Revisa la longitud del texto, por favor. Así no puede ser.",

    /*****************/
    /* CONVERSATIONS */
    /*****************/

    "conversation_welcome" => "Hola, :nameUser, soy el bot del FemPinya y te doy la bienvenida!",
    "conversation_name_reminder" => "Hola, :nameUser",
    "conversation_inactive_casteller" => "Parece que la persona castellera :nameCasteller no tiene el acceso a Telegram activado",


    // CONVERSATION UNLINKED

    "conversation_unlinked_link" => "¡Vaya! Parece que esta cuenta todavía no está vinculada a ningún miembro de la colla.",

    "conversation_unlinked_ask_link_code_question" => "¿Tienes el código para hacer el enlace?",
    "conversation_unlinked_ask_link_code_answer_no" => "Vale. Tienes que pedirlo al personal responsable de tu colla. Aquí te espero, no te preocupes.",

    "conversation_unlinked_ask_insert_link_code_question" => "Pega o escribe el código de enlace personal, haz el favor.",
    "conversation_unlinked_ask_insert_link_code_answer_unknown_code" => "¡Oh! Lo siento, pero parece que el código que has puesto no pertenece a ningún miembro de la colla. ¿Puedes volver a probarlo, por favor?",
    "conversation_unlinked_ask_insert_link_code_answer_correct_code" => "¡Sí! El código que has puesto pertenece a «:nameCasteller» de la colla «:nameColla».",

    "conversation_unlinked_ask_linking_question" => "¿Quieres enlazarlo con esta cuenta de Telegram?",
    "conversation_unlinked_ask_linking_answer_no" => "De acuerdo.",
    "conversation_unlinked_ask_linking_answer_yes" => "De acuerdo. Hago el enlace...",

    "conversation_unlinked_ask_whatelse_question" => "¿Quieres probar con otro código?",
    "conversation_unlinked_ask_whatelse_answer_no" => "Vale. ¡Hasta la vista!",

    "conversation_unlinked_ask_emoji_text" => "¿Quieres interactuar con el bot con Emojis o con texto?",

    "conversation_unlinked_emoji" => "Emojis",
    "conversation_unlinked_text" => "Texto",

    // CONVERSATION OPTIONS

    "conversation_options_ask_linked_castellers_number_of_casteller" => "{0}No tienes ningún enlace personal activo. |{1}Tienes :count enlace personal activo: |[2,*]Tienes :count enlaces personales activos: ",

    "conversation_options_ask_linked_castellers_question" => "{0}Enlázalo con esta cuenta eligiendo la opción disponible: |[1,*]Enlázalo con esta cuenta o cambia de usuario eligiendo una de las opciones disponibles: ",

    "conversation_options_ask_link_new_casteller_question" => "Has elegido hacer otro enlace personal con esta cuenta. ¿Tienes el código para hacerlo?",

    "conversation_options_ask_linsert_link_code_answer_same_casteller" => "¡Este enlace ya está hecho, este es tu código!",
    "conversation_options_ask_linsert_link_code_answer_already_linked_casteller" => "Este código es de alguien que ya está enlazado con esta cuenta.",

    "conversation_options_ask_whatelse_answer_no" => "De acuerdo.",

    "conversation_options_ask_linking_answer_linked" => "¡Ya está el enlace hecho!",

    "conversation_options_ask_offer_linked_castellers_offer_question" => "Elige quien va a ser el usuario activo a partir de ahora",
    "conversation_options_ask_offer_linked_castellers_offer_no_one" => "Ninguno",

    "conversation_options_ask_log_out" => "¿Seguro que quieres desvincular todos tus usuarios del bot de Telegram?",

    // CONVERSATION EVENTS

    // EVENT
    "conversation_events_display_event_admit_options_question" => "¿Admmite respuestas?",

    "conversation_events_display_event_admit_companions_question" => "¿Admite acompañantes?",

    "conversation_events_display_event_admit_ask_question" => "¿Admite modificaciones de asistencia?",

    "conversation_events_display_event_number_of_companions" => "{0}No tienes acompanyantes |{1}Tienes :count acompañante |[2,*]Tienes :count acompañantes ",

    "conversation_events_ask_offer_events_no_events" => "No hay ningún evento programado.",

    "conversation_events_ask_offer_events_no_unanswered_events" => "No hay ningún evento pendiente de responder.",

    "conversation_events_ask_offer_events_no_today_events" => "No hay ningún evento programado para hoy.",

    "conversation_events_ask_offer_events_question" => "¿De que evento quieres modificar la asistencia?",

    "conversation_events_ask_verify_event" => "¿Quieres verificar la asistencia de este evento?",

    "conversation_events_ask_verification_code" => "Escribe el código de verificación para confirmar la asistencia.",

    "conversation_events_ask_set_attendance_updated" => "Se ha actualizado la asistencia.",

    "conversation_events_ask_set_all_attendance_question" => "¿Seguro que quieres modificar la asistencia de todos los eventos del listado?",

    "conversation_events_ask_companions_question" => "¿Número de acompañantes?",

    "conversation_events_ask_options_question" => "Completa otras cuestiones de asistencia ",

    "companions" => "Acompañantes",
    "answers" => "Respuestas",
    "back" => "Acabar",
    "you_have_chosen" => "Tu selección: ",
    "your_answers" => "Tus respuestas: ",
    "your_companions" => "Tus acompañantes: ",


    // CONVERSATION TECNICA

    "reminder_about_event" => "Sobre un evento",
    "reminder_general" => "General",

    "calculating_wait" => "Estoy calculando los datos de la lista y enviando recordatorios. Paciencia, un momentito...",

    "reminder_sent" => "Recordatorio enviado!",

    "reminders" => "Recordatorio",
    "custom_reminder" => "Recordatorio personalizado",
    "reminders_message" => "¡Eh, :name! Todavia no has confirmado la asistencia a:",

    "written_by" => "Escrito por:",

    "conversation_is_ok_message_for_reminder" => "¿Es correcto el texto para el recordatorio?",
    "conversation_is_yes_ok_message_for_reminder" => "Has dicho que SÍ.",
    "conversation_is_no_ok_message_for_reminder" => "Has dicho que NO.",
    "conversation_edit_recordatori_per_enviar_minim_max" => "Escribe el texto que quieres enviar en el recordatorio (mín. 20, máx. 4000 caracteres).",
    "conversation_edit_recordatori_per_enviar_minim_max_warning" => "{1}Alerta, el tamaño del recordatorio es de :count carácter.|[0,*]Alerta, el tamaño del recordatorio es de :count caracteres.",
    "conversation_edit_recordatori_tag" => "¿A qué tag quieres enviar la notificación?",


    "conversation_ask_castellers_attendance_event" => "¿Qué tipo de asistencia quieres consultar?",

    "conversation_ask_offer_event_attendance" => "¿De qué evento quieres consultar la asistencia?",
    "conversation_ask_offer_event_reminder" => "¿De qué evento quieres enviar recordatorio?",

    "conversation_display_member_telegram_token" => "Este es tu código de Telegram:",
    "conversation_display_url_member_click" => "Clica al siguiente enlace para acceder a tu web:",
    "conversation_display_telegram_not_available_for_member" => "Acceso a Telegram desactivado para este miembro.",
    "conversation_display_web_not_available_for_member" => "Aceso web desactivado para este miembro.",

    // CONVERSATION SEARCH

    "conversation_edit_name_search_minim_max" => "Escribe el alias que quieres buscar.",
    "conversation_search_no_casteller_found" => "¡No he encontrado a nadie! Prueba con otros datos.",
    "conversation_search_counter" => "He llegado al registro :count de :total. ¡Tienes que afinar el término de búsqueda!",
    "conversation_search_fount_person" =>  "He encontrado los siguientes resultados en la colla: ",
    "found" => "¡Eureka!",
    /***********/
    /* DRIVERS */
    /***********/

    // WEB

    "driver_web_widget_title" => "Bot FemPinya",
    "driver_web_widget_placeholder" => "Escribe un mensaje...",


    /***********/
    /* HELP */
    /***********/

    "help_tecnica_send_reminders_expres" => "Recordatorio rápido",
    "help_tecnica_send_reminders" => "Recordatori personalitzado para el personal activo que todavía no ha confirmado",
    "help_actuacions" => "Actuaciones",
    "help_assajos" => "Ensayos",
    "help_activitats" => "Actividades",
    "help_unanswered" => "Evento pendiente de responder",
    "help_events_attendance_yes" => "Pon SÍ en la asistencia",
    "help_events_attendance_no" => "Pon NO en la asistencia",
    "help_events_attendance_Unknown" => "Pon NO LO SE en la asistencia",
    "help_events_attendance_AllYes" => "Marca SÍ en todas",
    "help_events_attendance_AllNo" => "Marca NO en todas",
    "help_events_attendance_AllUnknown" => "Marca NO LO SE en todas",
    "help_link_casteller" => "Hacer un enlace personal con esta cuenta",
    "help_text_emoji" => "Cambia la interacción entre texto o Emojis.",
    "help_logout" => "Desvincula todos los usuarios.",
    "help_select_castellers" => "Cambiar la persona activa",
    "help_tecnica_search" => "Buscar miembros de la colla",

    "help_tecnica_attendance" => "Consultar la asistencia",
    "help_tecnica_reminders" => "Enviar recordatorios",
    "help_tecnica_events_reminders" => "Envío de recordatorios de eventos.",
    "help_tecnica_general_reminders" => "Envío de notificaciones generales.",
    "help_tecnica_send_reminders_expres" => "Recordatorio rápido para personas no confirmadas.",
    "help_tecnica_send_reminders_tags" => "Recordatorio a miembros con etiquetas",



];
