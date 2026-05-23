<?php

declare(strict_types=1);

namespace App\Enums;

enum ChatbotIntent: string
{
    case Emergency = 'emergency';
    case MedicalAdvice = 'medical_advice';
    case RequestHandoff = 'request_handoff';
    case Faq = 'faq';
    case Schedule = 'schedule';
    case General = 'general';
    case Blocked = 'blocked';

    /**
     * Apakah intent ini boleh diteruskan ke LLM?
     */
    public function allowsLlm(): bool
    {
        return match ($this) {
            self::Emergency, self::MedicalAdvice, self::RequestHandoff, self::Blocked => false,
            default => true,
        };
    }

    public function requiresHandoff(): bool
    {
        return in_array($this, [self::Emergency, self::MedicalAdvice, self::RequestHandoff], true);
    }
}
