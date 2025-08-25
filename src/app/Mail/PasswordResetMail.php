<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $subjectLine;
    public $content;

    /**
     * Constructeur simplifié
     */
    public function __construct(User $user, string $url, string $subjectLine = null, string $content = null)
    {
        $this->user = $user;
        $this->url = $url;
        $this->subjectLine = $subjectLine ?? 'Réinitialisation de votre mot de passe';
        $this->content = $content ?? $this->generateDefaultContent($user, $url);
    }

    /**
     * Génère le contenu par défaut
     */
    private function generateDefaultContent(User $user, string $url): string
    {
        return "Bonjour {$user->first_name} {$user->last_name},\n\n"
             . "Vous avez demandé la réinitialisation de votre mot de passe.\n\n"
             . "Cliquez sur le lien suivant pour créer un nouveau mot de passe :\n"
             . "{$url}\n\n"
             . "Ce lien expirera dans 24 heures.\n\n"
             . "Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email.";
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.PasswordResetMail')
                    ->with([
                        'user' => $this->user,
                        'url' => $this->url,
                        'content' => $this->content,
                    ]);
    }
}