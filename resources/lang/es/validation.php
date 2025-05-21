<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    "accepted"        => ":attribute tiene que aceptarse.",
    "active_url"      => ":attribute no es un URL válido.",
    "after"           => ":attribute tiene que ser una fecha posterior a :date.",
    "after_or_equal"  => ":attribute tiene que ser una fecha posterior o igual a :date.",
    "alpha"           => ":attribute solo puede contener letras.",
    "alpha_dash"      => ":attribute solo puede contener letras, números y guiones.",
    "alpha_num"       => ":attribute solo puede contener letras y números.",
    "array"           => ":attribute tiene que ser una matriz.",
    "before"          => ":attribute tiene que ser una fecha anterior a :date.",
    "before_or_equal" => ":attribute tiene que ser una fecha anterior o igual a :date.",
    "between"         => [
        "numeric" => ":attribute tiene que estar entre :min y :max.",
        "file"    => ":attribute tiene que pesar entre :min y :max kB.",
        "string"  => ":attribute tiene que tener entre :min - :max carácteres.",
        "array"   => ":attribute tiene que tener entre :min - :max ítems.",
    ],
    "boolean"        => "El campo :attribute tiene que ser verdadero o falso",
    "confirmed"      => "La confirmación de :attribute no coincide.",
    "date"           => ":attribute no es una fecha vàlida.",
    "date_equals"    => "El :attribute tiene que ser una fecha igual a :date.",
    "date_format"    => "El campo :attribute no concuerda con el formato :format.",
    "different"      => ":attribute y :other tienen que ser diferentes.",
    "digits"         => ":attribute tiene que tener :digits dígitos.",
    "digits_between" => ":attribute tiene que tener entre :min y :max dígitos.",
    "dimensions"     => "Las dimensiones de la imagen :attribute no son válidas.",
    "distinct"       => "El campo :attribute tiene un valor duplicado.",
    "email"          => ":attribute no es un correo electrònico válido.",
    "ends_with"      => "El campo :attribute tiene que terminar con alguno de los siguientes valores: :values.",
    "exists"         => ":attribute no es correcto.",
    "file"           => "El campo :attribute tiene que ser un fichero.",
    "filled"         => "El campo :attribute es obligatorio.",
    "gt"             => [
        "numeric" => "El :attribute tiene que ser superior a :value.",
        "file"    => "El :attribute tiene que ser superior a :value kB.",
        "string"  => "El :attribute tiene que superar los :value caracteres.",
        "array"   => "El :attribute tiene que tener más de :value ítems.",
    ],
    "gte" => [
        "numeric" => "El :attribute tiene que ser igual o superior a :value.",
        "file"    => "El :attribute tiene que ser igual o superior a :value kB.",
        "string"  => "El :attribute tiene que ser igual o superior a :value caracteres.",
        "array"   => "El :attribute tiene que tener :value ítems o más.",
    ],
    "image"    => ":attribute tiene que ser una imagen.",
    "in"       => ":attribute no es correcto",
    "in_array" => "El campo :attribute no existe dentro de :other.",
    "integer"  => ":attribute tiene que ser un número entero.",
    "ip"       => ":attribute tiene que ser una dirección IP válida.",
    "ipv4"     => ":attribute tiene que ser una dirección IPv4 válida.",
    "ipv6"     => ":attribute tiene que ser una dirección IPv6 válida.",
    "json"     => "El campo :attribute tiene que ser una cadena JSON válida.",
    "lt"       => [
        "numeric" => "El :attribute tiene que ser inferior a :value.",
        "file"    => "El :attribute tiene que ser inferior a :value kB.",
        "string"  => "El :attribute no tiene que superar los :value caracteres.",
        "array"   => "El :attribute tiene que tener menos de :value ítems.",
    ],
    "lte" => [
        "numeric" => "El :attribute tiene que ser igual o inferior a :value.",
        "file"    => "El :attribute tiene que ser igual o inferior a :value kB.",
        "string"  => "El :attribute tiene que ser igual o inferior a :value caracteres.",
        "array"   => "El :attribute no tiene que tener más de :value ítems.",
    ],
    "max" => [
        "numeric" => ":attribute no puede ser más grande que :max.",
        "file"    => ":attribute no puede ser más grande que :max kB.",
        "string"  => ":attribute no puede ser más grande que :max caràcters.",
        "array"   => ":attribute no puede tener más de :max ítems.",
    ],
    "mimes"     => ":attribute tiene que ser un fichero con format: :values.",
    "mimetypes" => ":attribute tiene que ser un fichero con format: :values.",
    "min"       => [
        "numeric" => "El tamaño de :attribute tiene que ser como mínimo :min.",
        "file"    => "El tamany de :attribute tiene que ser como mínimo de :min kB.",
        "string"  => ":attribute tiene que contener como mínimo :min caracteres.",
        "array"   => ":attribute tiene que tener como mínimo :min ítems.",
    ],
    "not_in"               => ":attribute no es correcto.",
    "not_regex"            => "El formato de :attribute no es correcto.",
    "numeric"              => ":attribute tiene que ser numérico.",
    "present"              => "El camp :attribute tiene que existir.",
    "regex"                => "El formato de :attribute no es correcto.",
    "required"             => "El campo :attribute es obligatorio.",
    "required_if"          => "El campo :attribute es obligatorio cuando :other es :value.",
    "required_unless"      => "El campo :attribute es obligatorio a no ser que :other esté a :values.",
    "required_with"        => "El campo :attribute es obligatorio cuando hay :values.",
    "required_with_all"    => "El campo :attribute es obligatorio cuando hay :values.",
    "required_without"     => "El campo :attribute es obligatorio cuando no hay :values.",
    "required_without_all" => "El campo :attribute es obligatorio cuando no hay ningún valor de los siguientes: :values.",
    "same"                 => ":attribute y :other han de coincidir.",
    "size"                 => [
        "numeric" => "El tamaño de :attribute tiene que ser :size.",
        "file"    => "El tamaño de :attribute tiene que ser :size kB.",
        "string"  => ":attribute tiene que contener :size caracteres.",
        "array"   => ":attribute tiene que contener :size ítems.",
    ],
    "starts_with" => "El :attribute tiene que empezar por uno de los valores siguientes: :values",
    "string"      => "El campo :attribute tiene que ser una cadena.",
    "timezone"    => "El campo :attribute tiene que ser una zona válida.",
    "unique"      => ":attribute ya està registrado y no se puede repetir.",
    "uploaded"    => ":attribute ha fallado al subir.",
    "url"         => ":attribute no es una dirección web válida.",
    "uuid"        => "El :attribute tiene que ser un indentificador único universal (UUID) válido.",

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

    "custom" => [
        "attribute-name" => [
            "rule-name" => "custom-message",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    "attributes" => [
        "name"                  => "nombre",
        "username"              => "usuario",
        "email"                 => "correo electrónico",
        "first_name"            => "nombre",
        "last_name"             => "apellido",
        "password"              => "contraseña",
        "password_confirmation" => "confirmación de la contraseña",
        "city"                  => "ciudad",
        "country"               => "país",
        "address"               => "dirección",
        "phone"                 => "teléfono",
        "mobile"                => "móvil",
        "age"                   => "edad",
        "sex"                   => "sexo",
        "gender"                => "género",
        "year"                  => "año",
        "month"                 => "mes",
        "day"                   => "día",
        "hour"                  => "hora",
        "minute"                => "minuto",
        "second"                => "segundo",
        "title"                 => "título",
        "body"                  => "contenido",
        "description"           => "descripción",
        "excerpt"               => "resumen",
        "date"                  => "fecha",
        "time"                  => "hora",
        "subject"               => "tema",
        "message"               => "mensaje",
    ],
];
