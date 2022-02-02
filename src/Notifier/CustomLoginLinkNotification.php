<?php
namespace App\Notifier;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;


/**
 * Use this notification to ease sending login link
 * emails/SMS using the Notifier component.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class CustomLoginLinkNotification extends Notification implements EmailNotificationInterface
{
    private $loginLinkDetails;

    public function __construct(LoginLinkDetails $loginLinkDetails, string $subject, array $channels = [])
    {
        parent::__construct($subject, $channels);

        $this->loginLinkDetails = $loginLinkDetails;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        if (!class_exists(NotificationEmail::class)) {
            throw new \LogicException(sprintf('The "%s" method requires "symfony/twig-bridge:>4.4".', __METHOD__));
        }

        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->content($this->getContent() ?: $this->getDefaultContent('bouton suivant'))
            ->action('Se connecter', $this->loginLinkDetails->getUrl())
        ;

        return new EmailMessage($email);
    }

    private function getDefaultContent(string $target): string
    {
        $duration = $this->loginLinkDetails->getExpiresAt()->getTimestamp() - time();
        $durationString = floor($duration / 60).' minute'.($duration > 60 ? 's' : '');
        if (($hours = $duration / 3600) >= 1) {
            $durationString = floor($hours).' hour'.($hours >= 2 ? 's' : '');
        }

        return sprintf('Cliquez sur le %s pour confirmer que vous voulez vous connecter. Ce lien expirera dans %s.', $target, $durationString);

    }

}