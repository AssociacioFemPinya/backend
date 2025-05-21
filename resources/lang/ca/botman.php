<?php

return [

    /***********/
    /* GENERAL */
    /***********/

    // MENUS

    "general_menu_help" => "Aquest és el menú d'AJUDA.",
    "general_menu_info" => "Informació general: ",
    "general_menu_help_nom" => "Nom: ",
    "general_menu_help_colla" => "Colla: ",

    "mainMenuHome" => "Inici",
    "mainMenuEvents" => "Agenda",
    "mainMenuOptions" => "Opcions",
    "mainMenuHelp" => "?",
    "mainMenuTecnica" => "Tècnica",

    "eventsActuacio" => "Actuacions",
    "eventsAssaig" => "Assajos",
    "eventsActivitat" => "Activitats",
    "eventsUnanswered" => "Sense resposta",
    "eventsVerifyAttendance" => "Verifica assistència",

    "eventsAttendanceAllOk" => "Vinc a tot",
    "eventsAttendanceAllNok" => "No vinc a tot",
    "eventsAttendanceAllUnknown" => "No ho sé a tot",

    "eventsAttendanceOk" => "Vinc",
    "eventsAttendanceNok" => "No vinc",
    "eventsAttendanceUnknown" => "No ho sé",

    "tecnicaAttendance" => "Assistència",
    "tecnicaReminders" => "Notificacions",
    "tecnicaSearch" => "Cerca",

    "optionsLinkingCastellers" => "Enllaça o canvia persona",
    "optionsLanguage" => "Idioma",
    "optionsSwitchTextEmojis" => "Text o Emojis",
    "optionsLogOut" => "Desvincula persona",
    "optionsURLMember" => "Link web",
    "optionsURLMemberClick" => "Clica al següent l'enllaç per accedir a la web:",
    "optionsForbiddenURLMemberClick" => "No tens accés al web. L'has de demanar al personal responsable de la teva colla.",

    "pinyesPinya" => "Pinya",
    "pinyesRondes" => "Rondes",

    "optionsLinkCasteller" => "Enllaça persona",
    "optionsSwitchCasteller" => "Canvia persona activa",

    "tecnicaSendRemindersExpres" => "Express",
    "tecnicaSendRemindersOnline" => "Online",
    "tecnicaSendRemindersTags" => "Personalitzat",


    // NO DISPLAY
    "general_menu_home" => "Aquest és el menú d'INICI.",
    "general_menu_options" => "Aquest és el menú d'OPCIONS.",
    "general_menu_languages" => "Aquest és el menú d'IDIOMES.",
    "general_menu_events" => "Aquest és el menú d'ESDEVENIMENTS.",
    "general_menu_tecnica" => "Aquest és el menú de la TÈCNICA.",
    "general_menu_linked_castellers" => "Aquest és el menú de les persones enllaçades.",

    // Pinyes
    "mainMenuPinyes" => "Pinyes",
    "pinya" => "Pinyes",
    "rondes" => "Rondes",
    "pinya_en_obres" => "Aquesta funcionalitat encara no està implementada",
    "open_url_pinyes" => "Obriu el següent enllaç per veure la pinya",
    "rondes_empty" => "L'esdeveniment no té Rondes publicades.",
    "next_event" => "Proper Esdeveniment: ",


    // QUESTIONS

    "general_choose_option" => "Tria una de les opcions, si us plau:",
    "general_use_menu" => "Fes servir els botons del menú en lloc d'escriure, si us plau:",
    "general_message_length_is_incorrect" => "Revisa la llargada del text, si us plau, així no pot ser.",

    /*****************/
    /* CONVERSATIONS */
    /*****************/

    "conversation_welcome" => "Hola, :nameUser, soc el bot del FemPinya i et dono la benvinguda!",
    "conversation_name_reminder" => "Hola, :nameUser",
    "conversation_inactive_casteller" => "Mmmm... Sembla que la persona castellera :nameCasteller no té l'accés a Telegram activat.",


    // CONVERSATION UNLINKED

    "conversation_unlinked_link" => "Mmmm... Sembla que aquest compte encara no està vinculat a cap membre de la colla.",

    "conversation_unlinked_ask_link_code_question" => "Tens el codi per fer l'enllaç?",
    "conversation_unlinked_ask_link_code_answer_no" => "Entesos. L'has de demanar al responsable de la teva colla. M'espero, no pateixis.",

    "conversation_unlinked_ask_insert_link_code_question" => "Escriu o enganxa el codi d'enllaç personal, si us plau:",
    "conversation_unlinked_ask_insert_link_code_answer_unknown_code" => "Ostres! Em sap greu; el codi que has posat no sembla pertànyer a cap membre de la colla. Pots tornar a intentar-ho, si us plau?",
    "conversation_unlinked_ask_insert_link_code_answer_correct_code" => "d'acord. El codi que has posat correspon a «:nameCasteller» de la colla «:nameColla».",

    "conversation_unlinked_ask_linking_question" => "Vols enllaçar-hi aquest compte del Telegram?",
    "conversation_unlinked_ask_linking_answer_no" => "Entesos.",
    "conversation_unlinked_ask_linking_answer_yes" => "Entesos. Faig l'enllaç...",

    "conversation_unlinked_ask_whatelse_question" => "Vols provar-ho amb un altre codi?",
    "conversation_unlinked_ask_whatelse_answer_no" => "Entesos. A reveure!",

    "conversation_unlinked_ask_emoji_text" => "Vols interactuar amb el bot amb emojis o amb text?",

    "conversation_unlinked_emoji" => "Emojis",
    "conversation_unlinked_text" => "Text",

    // CONVERSATION OPTIONS

    "conversation_options_ask_linked_castellers_number_of_casteller" => "{0}No tens cap enllaç personal actiu. |{1}Tens :count enllaç personal actiu: |[2,*]Tens :count enllaços personals actius: ",

    "conversation_options_ask_linked_castellers_question" => "{0}Enllaça'l amb aquest compte triant l'opció disponible: |[1,*]Enllaça amb aquest compte o canvia d'usuari triant una de les opcions disponibles: ",

    "conversation_options_ask_link_new_casteller_question" => "Has triat fer una altre enllaç personal amb aquest compte. Tens el codi per fer-ho?",

    "conversation_options_ask_linsert_link_code_answer_same_casteller" => "Aquest enllaç ja estava fet; el codi que has posat és el teu!",
    "conversation_options_ask_linsert_link_code_answer_already_linked_casteller" => "Aquest enllaç ja estava fet; el codi que has posat és d'algú que ja havies enllaçat amb aquest compte.",

    "conversation_options_ask_whatelse_answer_no" => "Entesos.",

    "conversation_options_ask_linking_answer_linked" => "Ja està l'enllaç fet!",

    "conversation_options_ask_offer_linked_castellers_offer_question" => "Tria qui vols que sigui l'usuari actiu a partir d'ara:",
    "conversation_options_ask_offer_linked_castellers_offer_no_one" => "Cap",

    "conversation_options_ask_log_out" => "Segur que vols desvincular tots els teus usuaris del bot de Telegram?",

    // CONVERSATION EVENTS

    // EVENT
    "conversation_events_display_event_admit_options_question" => "Admet respostes?",

    "conversation_events_display_event_admit_companions_question" => "Admet acompanyants?",

    "conversation_events_display_event_admit_ask_question" => "Admet modificació d'assistència?",

    "conversation_events_display_event_number_of_companions" => "{0}No tens acompanyants. |{1}Tens :count acompanyant. |[2,*]Tens :count acompanyants.",

    "conversation_events_ask_offer_events_no_events" => "No hi ha cap esdeveniment programat.",

    "conversation_events_ask_offer_events_no_unanswered_events" => "No hi ha cap esdeveniment pendent de respondre.",

    "conversation_events_ask_offer_events_no_today_events" => "No hi ha cap esdeveniment programat per avui.",

    "conversation_events_ask_offer_events_question" => "De quin esdeveniment vols modificar o consultar la teva assistència?",

    "conversation_events_ask_verify_event" => "De quin esdeveniment vols verificar la teva assistència?",

    "conversation_events_ask_verification_code" => "Escriu el codi de verificació per confirmar l'assistència.",

    "conversation_events_ask_set_attendance_updated" => "S'ha actualitzat l'assistència.",

    "conversation_events_ask_set_all_attendance_question" => "Segur que vols modificar l'assistència de tots els esdeveniments llistats?",

    "conversation_events_ask_companions_question" => "Nombre d'acompanyants?",

    "conversation_events_ask_options_question" => "Completa altres questions d'assistència ",

    "companions" => "Acompanyants",
    "answers" => "Respostes personalitzades",
    "back" => "Torna",
    "you_have_chosen" => "La teva selecció: ",
    "your_answers" => "Les teves respostes: ",
    "your_companions" => "El teus acompanyants: ",


    // CONVERSATION TECNICA

    "reminder_about_event" => "Sobre un esdeveniment",
    "reminder_general" => "General",

    "calculating_wait" => "Estic calculant les dades per llistar i enviant els recordatoris. Un momentet...",

    "reminder_sent" => "Recordatori enviat!",

    "reminders" => "Recordatoris",
    "custom_reminder" => "Recordatori personalitzat",
    "reminders_message" => "Ei, :name, encara no has confirmat la teva assistència a: ",

    "written_by" => "Escrit per: ",

    "conversation_is_ok_message_for_reminder" => "És correcte el text del recordatori?",
    "conversation_is_yes_ok_message_for_reminder" => "Has dit que SÍ.",
    "conversation_is_no_ok_message_for_reminder" => "Has dit que NO.",
    "conversation_edit_recordatori_per_enviar_minim_max" => "Escriu el text del recordatori (mín. 20, màx. 4000 caràcters)",
    "conversation_edit_recordatori_per_enviar_minim_max_warning" => "{1}Alerta, la mida del recordatori és d' :count caràcter.|[0,*]Alerta, la mida del recordatori és de :count caràcters.",
    "conversation_edit_recordatori_tag" => "A quin tag vols enviar la notificació?",


    "conversation_ask_castellers_attendance_event" => "Quin tipus d'assistència vols consultar?",

    "conversation_ask_offer_event_attendance" => "De quin esdeveniment vols consultar l'assistència?",
    "conversation_ask_offer_event_reminder" => "De quin esdeveniment vols enviar recordatori?",

    "conversation_display_member_telegram_token" => "Aquest és el teu codi telegram:",
    "conversation_display_url_member_click" => "Clica al següent l'enllaç per accedir a la teva web:",
    "conversation_display_telegram_not_available_for_member" => "Accés telegram desactivat per aquest membre.",
    "conversation_display_web_not_available_for_member" => "Accés web desactivat per aquest membre.",

    // CONVERSATION SEARCH

    "conversation_edit_name_search_minim_max" => "Escriu l'àlies que vols cercar.",
    "conversation_search_no_casteller_found" => "No he trobat ningú. Prova amb unes altres dades.",
    "conversation_search_counter" => "He arribat al registre :count de :total, cal que afinis el terme de cerca.",
    "conversation_search_fount_person" =>  "He trobat el següents resultats a la colla: ",
    "found" => "Eureka!",
    /***********/
    /* DRIVERS */
    /***********/

    // WEB

    "driver_web_widget_title" => "Bot FemPinya",
    "driver_web_widget_placeholder" => "Escriu un missatge...",


    /***********/
    /* HELP */
    /***********/

    "help_tecnica_send_reminders_expres" => "Recordatori ràpid per a les persones actives que encara no han confirmat",
    "help_tecnica_send_reminders" => "Recordatori personalitzat per a les persones actives que encara no han confirmat",
    "help_actuacions" => "Actuacions",
    "help_assajos" => "Assajos",
    "help_activitats" => "Activitats",
    "help_unanswered" => "Esdeveniments pendents de respondre",
    "help_events_attendance_yes" => "Posa SÍ a l'assistència.",
    "help_events_attendance_no" => "Posa NO a l'assistència.",
    "help_events_attendance_Unknown" => "Posa NO HO SÉ a l'assistència.",
    "help_events_attendance_AllYes" => "Marca SÍ a tot.",
    "help_events_attendance_AllNo" => "Marca NO a tot.",
    "help_events_attendance_AllUnknown" => "Marca NO HO SÉ a tot.",
    "help_link_casteller" => "Fa un enllaç personal a aquest compte.",
    "help_text_emoji" => "Canvia la interacció entre text o emojis.",
    "help_logout" => "Desvincula tots els usuaris.",
    "help_select_castellers" => "Canvia la persona activa.",
    "help_tecnica_search" => "Cerca membres de la colla.",

    "help_tecnica_attendance" => "Consulta l'assistència.",
    "help_tecnica_reminders" => "Enviament de recordatoris.",
    "help_tecnica_events_reminders" => "Enviament de recordatoris d'esdeveniments.",
    "help_tecnica_general_reminders" => "Enviament de notificacions generals.",
    "help_tecnica_send_reminders_expres" => "Recordatori ràpid per persones no confirmades.",
    "help_tecnica_send_reminders_tags" => "Recordatori personalitzat a membres amb etiquetes.",



];
