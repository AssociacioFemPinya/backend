<?php

return [

    /***********/
    /* GENERAL */
    /***********/

    // MENUS

    "general_menu_help" => "This is the HELP menu.",
    "general_menu_info" => "General information: ",
    "general_menu_help_nom" => "Name: ",
    "general_menu_help_colla" => "Group: ",

    "mainMenuHome" => "Home",
    "mainMenuEvents" => "Events",
    "mainMenuOptions" => "Options",
    "mainMenuHelp" => "?",
    "mainMenuTecnica" => "Technique",

    "eventsActuacio" => "Performances",
    "eventsAssaig" => "Rehearsals",
    "eventsActivitat" => "Activities",
    "eventsUnanswered" => "Unanswered",
    "eventsVerifyAttendance" => "Verify attendance",

    "eventsAttendanceAllOk" => "Attending all",
    "eventsAttendanceAllNok" => "Not attending all",
    "eventsAttendanceAllUnknown" => "Unknown for all",

    "eventsAttendanceOk" => "Attending",
    "eventsAttendanceNok" => "Not attending",
    "eventsAttendanceUnknown" => "Unknown",

    "tecnicaAttendance" => "Attendance",
    "tecnicaReminders" => "Reminders",
    "tecnicaSearch" => "Search",

    "optionsLinkingCastellers" => "Link or change person",
    "optionsLanguage" => "Language",
    "optionsSwitchTextEmojis" => "Text or Emojis",
    "optionsLogOut" => "Unlink person",
    "optionsURLMember" => "Web link",
    "optionsURLMemberClick" => "Click on the following link to access the website:",
    "optionsForbiddenURLMemberClick" => "Forbidden web access. Please request access to the site administrator.",

    "pinyesPinya" => "Pinya",
    "pinyesRondes" => "Rondes",

    "optionsLinkCasteller" => "Link person",
    "optionsSwitchCasteller" => "Switch active person",

    "tecnicaSendRemindersExpres" => "Express",
    "tecnicaSendRemindersOnline" => "Online",
    "tecnicaSendRemindersTags" => "Custom",

    // NO DISPLAY
    "general_menu_home" => "This is the HOME menu.",
    "general_menu_options" => "This is the OPTIONS menu.",
    "general_menu_languages" => "This is the LANGUAGES menu.",
    "general_menu_events" => "This is the EVENTS menu.",
    "general_menu_tecnica" => "This is the TECHNIQUE menu.",
    "general_menu_linked_castellers" => "This is the menu for linked people.",

    // Pinyes
    "mainMenuPinyes" => "Pinyes",
    "pinya" => "Pinyes",
    "rondes" => "Rondes",
    "pinya_en_obres" => "This feature is not yet implemented",
    "open_url_pinyes" => "Open the following link to see the pinya",
    "rondes_empty" => "The event has no published Rondes.",
    "next_event" => "Next Event: ",


    // QUESTIONS

    "general_choose_option" => "Choose one of the options, please:",
    "general_use_menu" => "Use the menu buttons instead of typing, please:",
    "general_message_length_is_incorrect" => "Check the length of the text, please.",

    /*****************/
    /* CONVERSATIONS */
    /*****************/

    "conversation_welcome" => "Hello, :nameUser, I'm the FemPinya bot and I welcome you!",
    "conversation_name_reminder" => "Hello, :nameUser",
    "conversation_inactive_casteller" => "Hmm... It seems that the casteller person :nameCasteller does not have Telegram access activated.",

    // CONVERSATION UNLINKED

    "conversation_unlinked_link" => "Hmm... It seems that this account is not yet linked to any member of the group.",

    "conversation_unlinked_ask_link_code_question" => "Do you have the code to make the link?",
    "conversation_unlinked_ask_link_code_answer_no" => "Alright. You have to request it from the responsible staff of your group. I'll wait, don't worry.",

    "conversation_unlinked_ask_insert_link_code_question" => "Write or paste the personal link code, please:",
    "conversation_unlinked_ask_insert_link_code_answer_unknown_code" => "Oops! I'm sorry; the code you entered does not seem to belong to any member of the group. You can try again, please?",
    "conversation_unlinked_ask_insert_link_code_answer_correct_code" => "Alright. The code you entered corresponds to «:nameCasteller» of the group «:nameColla».",

    "conversation_unlinked_ask_linking_question" => "Do you want to link this Telegram account?",
    "conversation_unlinked_ask_linking_answer_no" => "Understood.",
    "conversation_unlinked_ask_linking_answer_yes" => "Understood. I'm linking it...",

    "conversation_unlinked_ask_whatelse_question" => "Do you want to try with another code?",
    "conversation_unlinked_ask_whatelse_answer_no" => "Understood. Goodbye!",

    "conversation_unlinked_ask_emoji_text" => "Do you want to interact with the bot using emojis or text?",

    "conversation_unlinked_emoji" => "Emojis",
    "conversation_unlinked_text" => "Text",


    // CONVERSATION OPTIONS

    "conversation_options_ask_linked_castellers_number_of_casteller" => "{0}You don't have any active personal link. |{1}You have :count active personal link: |[2,*]You have :count active personal links: ",

    "conversation_options_ask_linked_castellers_question" => "{0}Link it to this account by choosing the available option: |[1,*]Link it to this account or change user by choosing one of the available options: ",

    "conversation_options_ask_link_new_casteller_question" => "You have chosen to make another personal link with this account. Do you have the code to do it?",

    "conversation_options_ask_linsert_link_code_answer_same_casteller" => "This link was already made; the code you entered is yours!",
    "conversation_options_ask_linsert_link_code_answer_already_linked_casteller" => "This link was already made; the code you entered belongs to someone you had already linked to this account.",

    "conversation_options_ask_whatelse_answer_no" => "Understood.",

    "conversation_options_ask_linking_answer_linked" => "The link is already made!",

    "conversation_options_ask_offer_linked_castellers_offer_question" => "Choose who you want to be the active user from now on:",
    "conversation_options_ask_offer_linked_castellers_offer_no_one" => "None",

    "conversation_options_ask_log_out" => "Are you sure you want to unlink all your users from the Telegram bot",

    // CONVERSATION EVENTS

    // EVENT
    "conversation_events_display_event_admit_options_question" => "Admit responses?",

    "conversation_events_display_event_admit_companions_question" => "Admit companions?",

    "conversation_events_display_event_admit_ask_question" => "Admit attendance modification?",

    "conversation_events_display_event_number_of_companions" => "{0}You have no companions. |{1}You have :count companion. |[2,*]You have :count companions.",

    "conversation_events_ask_offer_events_no_events" => "There are no scheduled events.",

    "conversation_events_ask_offer_events_no_unanswered_events" => "There are no events pending response.",

    "conversation_events_ask_offer_events_no_today_events" => "There are no events today.",

    "conversation_events_ask_offer_events_question" => "Which event do you want to modify or check your attendance for?",

    "conversation_events_ask_verify_event" => "Which event do you want to check your attendance for?",

    "conversation_events_ask_verification_code" => "Write the verification code to confirm your attendance.",

    "conversation_events_ask_set_attendance_updated" => "The attendance has been updated.",

    "conversation_events_ask_set_all_attendance_question" => "Are you sure you want to modify the attendance for all listed events?",

    "conversation_events_ask_companions_question" => "Number of companions?",

    "conversation_events_ask_options_question" => "Complete other attendance questions ",

    "companions" => "Companions",
    "answers" => "Answers",
    "back" => "Finish",
    "you_have_chosen" => "Your selection: ",
    "your_answers" => "Your answers: ",
    "your_companions" => "Your companions: ",

    // CONVERSATION TECNICA

    "reminder_about_event" => "About an event",
    "reminder_general" => "General",

    "calculating_wait" => "I'm calculating the data to list and sending the reminders. Just a moment...",

    "reminder_sent" => "Reminder sent!",

    "reminders" => "Reminders",
    "custom_reminder" => "Custom reminder",
    "reminders_message" => "Hey, :name, you haven't confirmed your attendance to: ",

    "written_by" => "Written by: ",

    "conversation_is_ok_message_for_reminder" => "Is the reminder text correct?",
    "conversation_is_yes_ok_message_for_reminder" => "You said YES.",
    "conversation_is_no_ok_message_for_reminder" => "You said NO.",
    "conversation_edit_recordatori_per_enviar_minim_max" => "Write the reminder text (min. 20, max. 4000 characters)",
    "conversation_edit_recordatori_per_enviar_minim_max_warning" => "{1}Warning, the size of the reminder is :count character.|[0,*]Warning, the size of the reminder is :count characters.",
    "conversation_edit_recordatori_tag" => "Which tag do you want to send the notification to?",


    "conversation_ask_castellers_attendance_event" => "What type of attendance do you want to check?",

    "conversation_ask_offer_event_attendance" => "Which event do you want to check the attendance for?",
    "conversation_ask_offer_event_reminder" => "Which event do you want to send a reminder for?",

    "conversation_display_member_telegram_token" => "This is your telegram code:",
    "conversation_display_url_member_click" => "Click on the following link to access your website:",
    "conversation_display_telegram_not_available_for_member" => "Telegram access not available for this user.",
    "conversation_display_web_not_available_for_member" => "Web access not available for this user.",

    // CONVERSATION SEARCH

    "conversation_edit_name_search_minim_max" => "Write the alias you want to search for.",
    "conversation_search_no_casteller_found" => "I didn't find anyone. Try with different data.",
    "conversation_search_counter" => "I have reached record :count of :total, you need to refine the search term.",
    "conversation_search_fount_person" =>  "I found the following results in the group: ",
    "found" => "Eureka!",
    /***********/
    /* DRIVERS */
    /***********/

    // WEB

    "driver_web_widget_title" => "FemPinya Bot",
    "driver_web_widget_placeholder" => "Write a message...",

    /***********/
    /* HELP */
    /***********/

    "help_tecnica_send_reminders_expres" => "Quick reminder for active people who haven't confirmed yet",
    "help_tecnica_send_reminders" => "Custom reminder for active people who haven't confirmed yet",
    "help_actuacions" => "Performances",
    "help_assajos" => "Rehearsals",
    "help_activitats" => "Activities",
    "help_unanswered" => "Events pending response",
    "help_events_attendance_yes" => "Set attendance to YES.",
    "help_events_attendance_no" => "Set attendance to NO.",
    "help_events_attendance_Unknown" => "Set attendance to UNKNOWN.",
    "help_events_attendance_AllYes" => "Set all to YES.",
    "help_events_attendance_AllNo" => "Set all to NO.",
    "help_events_attendance_AllUnknown" => "Set all to UNKNOWN.",
    "help_link_casteller" => "Make a personal link to this account.",
    "help_text_emoji" => "Switch interaction between text or emojis.",
    "help_logout" => "Unlink all users.",
    "help_select_castellers" => "Switch active person.",
    "help_tecnica_search" => "Search group members.",

    "help_tecnica_attendance" => "Check attendance.",
    "help_tecnica_reminders" => "Sending reminders.",
    "help_tecnica_events_reminders" => "Sending event reminders.",
    "help_tecnica_general_reminders" => "Sending general notifications.",
    "help_tecnica_send_reminders_expres" => "Quick reminder.",
    "help_tecnica_send_reminders_tags" => "Reminder to members with tags.",

];

