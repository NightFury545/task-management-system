<?php

namespace Nightfury\TaskManagementSystem\Models\Enums;

enum Status: string
{
    case COMPLETED = 'Виконано';
    case NOT_COMPLETED = 'Не виконано';
    case IN_PROGRESS = 'В процесі';
}
