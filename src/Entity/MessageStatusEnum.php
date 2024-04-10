<?php

namespace App\Entity;

enum MessageStatusEnum: string
{
    case SENT = 'sent';
    case READ = 'read';
    case FAILED = 'failed';
}