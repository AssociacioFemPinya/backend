<?php

return [

    /***********/
    /* GENERAL */
    /***********/

    // MENUS

    "general_menu_help" => "Menu d'AIDE.",
    "general_menu_info" => "Information générale: ",
    "general_menu_help_nom" => "Nom: ",
    "general_menu_help_colla" => "Colla: ",

    "mainMenuHome" => "Accueil",
    "mainMenuEvents" => "Agenda",
    "mainMenuOptions" => "Options",
    "mainMenuHelp" => "?",
    "mainMenuTecnica" => "Technique",

    "eventsActuacio" => "Actuations",
    "eventsAssaig" => "Assajos",
    "eventsActivitat" => "Activités",
    "eventsUnanswered" => "Sans réponse",
    "eventsVerifyAttendance" => "Vérifier la présence",

    "eventsAttendanceAllOk" => "Je viens à tout",
    "eventsAttendanceAllNok" => "Je ne viens à rien",
    "eventsAttendanceAllUnknown" => "Je ne sais pas pour tout",

    "eventsAttendanceOk" => "Je viens",
    "eventsAttendanceNok" => "Je ne viens pas",
    "eventsAttendanceUnknown" => "Je ne sais pas",

    "tecnicaAttendance" => "Présence",
    "tecnicaReminders" => "Rappels",
    "tecnicaSearch" => "Rechercher",

    "optionsLinkingCastellers" => "Lier ou changer de personne",
    "optionsLanguage" => "Langue",
    "optionsSwitchTextEmojis" => "Texte ou Emojis",
    "optionsLogOut" => "Délier la personne",
    "optionsURLMember" => "Lien web",
    "optionsURLMemberClick" => "Clique sur le lien suivant pour accéder au site web:",
    "optionsForbiddenURLMemberClick" => "Vous n'avez pas accès au web. Merci de demander accès à l'administrateur du site.",

    "pinyesPinya" => "Pinya",
    "pinyesRondes" => "Rondes",

    "optionsLinkCasteller" => "Lier une personne",
    "optionsSwitchCasteller" => "Changer de personne active",

    "tecnicaSendRemindersExpres" => "Express",
    "tecnicaSendRemindersOnline" => "En ligne",
    "tecnicaSendRemindersTags" => "Personnalisé",

    // NO DISPLAY
    "general_menu_home" => "Menu d'ACCUEIL.",
    "general_menu_options" => "Menu d'OPTIONS.",
    "general_menu_languages" => "Menu de LANGUES.",
    "general_menu_events" => "Menu d'ÉVÉNEMENTS.",
    "general_menu_tecnica" => "Menu de la TECHNIQUE.",
    "general_menu_linked_castellers" => "Menu des personnes reliées.",

    // Pinyes
    "mainMenuPinyes" => "Pinyes",
    "pinya" => "Pinyes",
    "rondes" => "Rondes",
    "pinya_en_obres" => "Cette fonctionnalité n'est pas encore implémentée",
    "open_url_pinyes" => "Ouvrez le lien suivant pour voir la pinya",
    "rondes_empty" => "L'événement n'a pas de Rondes publiées.",
    "next_event" => "Prochain événement: ",

    // QUESTIONS

    "general_choose_option" => "Choisissez une des options, s'il vous plaît:",
    "general_use_menu" => "Utilisez les boutons du menu au lieu d'écrire, s'il vous plaît:",
    "general_message_length_is_incorrect" => "Revoyez la longueur du texte, s'il vous plaît, ainsi ce n'est pas possible.",

    /*****************/
    /* CONVERSATIONS */
    /*****************/

    "conversation_welcome" => ":nameUser, bienvenue au robot de FemPinya!",
    "conversation_name_reminder" => "Hola, :nameUser",
    "conversation_inactive_casteller" => "Mmmm... Il semble que la personne castellera :nameCasteller n'ait pas l'accès à Telegram activé.",

    // CONVERSATION UNLINKED

    "conversation_unlinked_link" => "Mmmm... Il semble que ce compte ne soit lié à aucun membre de la colla.",

    "conversation_unlinked_ask_link_code_question" => "As-tu le code pour te connecter?",
    "conversation_unlinked_ask_link_code_answer_no" => "D'accord. Tu dois le demander au responsable de ta colla. Tranquille, j'attends.",

    "conversation_unlinked_ask_insert_link_code_question" => "Écris ou colle le code d'accès personnel, s'il vous plaît:",
    "conversation_unlinked_ask_insert_link_code_answer_unknown_code" => "Oups! Désolé; le code que tu as mis ne semble pas appartenir à un membre de la colla. Peux-tu réessayer, s'il te plaît?",
    "conversation_unlinked_ask_insert_link_code_answer_correct_code" => "D'accord. Ce code correspond à «:nameCasteller» de la colla «:nameColla».",

    "conversation_unlinked_ask_linking_question" => "Veux-tu connecter avec ce compte de Telegram?",
    "conversation_unlinked_ask_linking_answer_no" => "D'accord.",
    "conversation_unlinked_ask_linking_answer_yes" => "D'accord. Je fais le lien...",

    "conversation_unlinked_ask_whatelse_question" => "Veux-tu essayer avec un autre code?",
    "conversation_unlinked_ask_whatelse_answer_no" => "D'accord. Au revoir!",

    "conversation_unlinked_ask_emoji_text" => "Veux-tu interagir avec le bot avec des emojis ou avec du texte?",

    "conversation_unlinked_emoji" => "Emojis",
    "conversation_unlinked_text" => "Texte",

    // CONVERSATION OPTIONS

    "conversation_options_ask_linked_castellers_number_of_casteller" => "{0}Tu n'as aucun lien personnel actif. |{1}Tu as :count lien personnel actif: |[2,*]Tu as :count liens personnels actifs: ",

    "conversation_options_ask_linked_castellers_question" => "{0}Associe-le avec un autre compte en choisissant l'option disponible: |[1,*]Associe-le avec ce compte ou change d'utilisateur en choisissant une des options disponibles: ",

    "conversation_options_ask_link_new_casteller_question" => "Tu as choisi un autre lien personnel avec ce compte. As-tu le code pour cela?",

    "conversation_options_ask_linsert_link_code_answer_same_casteller" => "Ce lien est fait; le code que tu as entré est le tien!",
    "conversation_options_ask_linsert_link_code_answer_already_linked_casteller" => "Ce lien existait déjà; le code que tu as entré est d'une personne que tu avais associée avec ce compte.",

    "conversation_options_ask_whatelse_answer_no" => "D'accord.",

    "conversation_options_ask_linking_answer_linked" => "Le lien est fait!",

    "conversation_options_ask_offer_linked_castellers_offer_question" => "Choisis qui sera l'utilisateur actif à partir de maintenant:",
    "conversation_options_ask_offer_linked_castellers_offer_no_one" => "Aucun",

    "conversation_options_ask_log_out" => "Es-tu sûr de vouloir délier tous tes utilisateurs du bot de Telegram?",

    // CONVERSATION EVENTS

    // EVENT
    "conversation_events_display_event_admit_options_question" => "Admet des réponses?",

    "conversation_events_display_event_admit_companions_question" => "Admet des accompagnants?",

    "conversation_events_display_event_admit_ask_question" => "Admet modification de présence?",

    "conversation_events_display_event_number_of_companions" => "{0}Tu n'as pas d'accompagnant. |{1}Tu as :count accompagnant. |[2,*]Tu as :count accompagnants.",

    "conversation_events_ask_offer_events_no_events" => "Il n'y a pas d'événement programmé.",

    "conversation_events_ask_offer_events_no_unanswered_events" => "Il n'y a aucun événement en attente de réponse.",

    "conversation_events_ask_offer_events_no_today_events" => "Il n'y a pas d'événement aujourd'hui.",

    "conversation_events_ask_offer_events_question" => "De quel événement veux-tu modifier ou consulter la participation?",

    "conversation_events_ask_verify_event" => "De quel événement veux-tu vérifier la présence?",

    "conversation_events_ask_verification_code" => "Écris le code de vérification pour confirmer la présence.",

    "conversation_events_ask_set_attendance_updated" => "La participation a été modifiée.",

    "conversation_events_ask_set_all_attendance_question" => "Es-tu certain de vouloir modifier la participation de tous les événements de la liste?",

    "conversation_events_ask_companions_question" => "Nombre d'accompagnants?",

    "conversation_events_ask_options_question" => "Réponds aux autres questions de participation ",

    "companions" => "Accompagnants",
    "answers" => "Réponses",
    "back" => "Finir",
    "you_have_chosen" => "Ta sélection: ",
    "your_answers" => "Tes réponses: ",
    "your_companions" => "Tes accompagnants: ",

    // CONVERSATION TECNICA

    "reminder_about_event" => "À propos d'un événement",
    "reminder_general" => "Général",

    "calculating_wait" => "Je calcule les données pour lister et envoyer les rappels. Un petit moment...",

    "reminder_sent" => "Rappel envoyé!",

    "reminders" => "Rappels

",
    "custom_reminder" => "Rappel personnalisé",
    "reminders_message" => "Hey, :name, Tu n'as pas encore confirmé ta participation à: ",

    "written_by" => "Écrit par: ",

    "conversation_is_ok_message_for_reminder" => "Le texte du rappel est-il correct?",
    "conversation_is_yes_ok_message_for_reminder" => "Tu as dit OUI.",
    "conversation_is_no_ok_message_for_reminder" => "Tu as dit NON.",
    "conversation_edit_recordatori_per_enviar_minim_max" => "Écris le texte du rappel (mín. 20, max. 4000 caractères)",
    "conversation_edit_recordatori_per_enviar_minim_max_warning" => "{1}Attention, la taille du rappel est de :count caractère.|[0,*]Attention, la taille du rappel est de :count caractères.",
    "conversation_edit_recordatori_tag" => "À quel tag veux-tu envoyer la notification?",

    "conversation_ask_castellers_attendance_event" => "Quel type de présence veux-tu consulter?",

    "conversation_ask_offer_event_attendance" => "De quel événement veux-tu consulter la présence?",
    "conversation_ask_offer_event_reminder" => "De quel événement veux-tu envoyer un rappel?",

    "conversation_display_member_telegram_token" => "Voici votre code Telegram:",
    "conversation_display_url_member_click" => "Cliquez ici pour accéder au site web:",
    "conversation_display_telegram_not_available_for_member" => "Cet utilisateur a l'accès Telegram désactivé.",
    "conversation_display_web_not_available_for_member" => "Cet utilisateur a l'accès Web désactivé.",

    // CONVERSATION SEARCH

    "conversation_edit_name_search_minim_max" => "Écris le pseudonyme que tu cherches.",
    "conversation_search_no_casteller_found" => "Je n'ai trouvé personne. Essaie avec d'autres données.",
    "conversation_search_counter" => "J'ai trouvé :count de :total, il faut affiner la recherche.",
    "conversation_search_fount_person" =>  "J'ai trouvé les résultats suivants dans la colla: ",
    "found" => "Trouvé!",
    /***********/
    /* DRIVERS */
    /***********/

    // WEB

    "driver_web_widget_title" => "Bot FemPinya",
    "driver_web_widget_placeholder" => "Écris un message...",

    /***********/
    /* HELP */
    /***********/

    "help_tecnica_send_reminders_expres" => "Rappel rapide aux membres en activité qui n'ont pas encore confirmé",
    "help_tecnica_send_reminders" => "Rappel personnalisé aux membres en activité qui n'ont pas encore confirmé",
    "help_actuacions" => "Actuations",
    "help_assajos" => "Assajos",
    "help_activitats" => "Activités",
    "help_unanswered" => "Événements en attente de réponse",
    "help_events_attendance_yes" => "Sélectionne OUI à la participation.",
    "help_events_attendance_no" => "Sélectionne NON à la participation.",
    "help_events_attendance_Unknown" => "Sélectionne JE NE SAIS PAS à la participation.",
    "help_events_attendance_AllYes" => "Mets OUI à tout.",
    "help_events_attendance_AllNo" => "Mets NON à tout.",
    "help_events_attendance_AllUnknown" => "Mets JE NE SAIS PAS à tout.",
    "help_link_casteller" => "Lie une personne à ce compte.",
    "help_text_emoji" => "Change l'interaction entre texte ou emojis.",
    "help_logout" => "Délie tous les utilisateurs.",
    "help_select_castellers" => "Change le membre actif.",
    "help_tecnica_search" => "Rechercher des membres de la colla.",

    "help_tecnica_attendance" => "Consulte la présence.",
    "help_tecnica_reminders" => "Envoie des rappels.",
    "help_tecnica_events_reminders" => "Envoi de rappels d'événements.",
    "help_tecnica_general_reminders" => "Envoi de notifications générales.",
    "help_tecnica_send_reminders_expres" => "Rappel rapide.",
    "help_tecnica_send_reminders_tags" => "Rappel aux membres avec étiquettes.",
];
