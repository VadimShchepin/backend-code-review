<?php

namespace App\Entity;

enum MessageStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case READ = 'read';
    case FAILED = 'failed';
}