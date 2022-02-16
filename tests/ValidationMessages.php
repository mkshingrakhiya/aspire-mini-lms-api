<?php

function validationBetweenNumeric(string $attribute, int $min, int $max)
{
    return "The {$attribute} must be between {$min} and {$max}.";
}

function validationConfirmed(string $attribute)
{
    return "The {$attribute} confirmation does not match.";
}

function validationInteger(string $attribute)
{
    return "The {$attribute} must be an integer.";
}

function validationEnum(string $attribute)
{
    return "The selected {$attribute} is invalid.";
}

function validationMinString(string $attribute, int $min)
{
    return "The {$attribute} must be at least {$min} characters.";
}

function validationMaxString(string $attribute, int $max)
{
    return "The {$attribute} must not be greater than {$max} characters.";
}

function validationNumeric(string $attribute)
{
    return "The {$attribute} must be a number.";
}

function validationGreaterNumeric(string $attribute, int $value)
{
    return "The {$attribute} must be greater than {$value}.";
}

function validationRequired(string $attribute)
{
    return "The {$attribute} field is required.";
}

function validationEmail(string $attribute)
{
    return "The {$attribute} must be a valid email address.";
}

function validationString(string $attribute)
{
    return "The {$attribute} must be a string.";
}

function validationUnique(string $attribute)
{
    return "The ${attribute} has already been taken.";
}
