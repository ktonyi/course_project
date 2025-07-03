<?php

namespace App\Entity;

enum QuestionTypeEnum: string
{
    case STRING = 'string';
    case TEXT = 'text';
    case INTEGER = 'integer';
    case CHECKBOX = 'checkbox';
}