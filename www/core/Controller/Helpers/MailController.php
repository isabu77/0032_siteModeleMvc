<?php
namespace Core\Controller\Helpers;
use Core\Controller\Controller;

/**
 *  Classe Text
 * @var string
 * @access public
 * @static
 **/
class MailController extends Controller
{
/**
* envoi d'un mail par swift_mailer
* @return int nb de mails envoyés
*/
    public static function sendMail($emailTo, $sujet, $msg, $cci = true, $from = ""): int
    {
        $mailTo = $emailTo;
        if (!is_array($emailTo)) {
            $mailTo = [$emailTo];
        }
        // Crée le Transport -- mailcatcher ne fonctionne que sous unix et docker en mode développement
        // ne fonctionne pas sous windows/wamp
        // ENV_MAILCATCHER déclarée dabs config.php selon le serveur
        if (\App\App::getInstance()->getEnv('ENV_MAILCATCHER')) {
            $transport = new \Swift_SmtpTransport(getenv('CONTAINER_NAME').'.mailcatcher', 1025);
            $sender = ["mail@test.fr" => "adminDev"];
        } else {
            // 'smtp.gmail.com' peut être dans une variable d'environnement
            // pour laisser le choix du fournisseur
            $transport = new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
            $transport->setUsername(\App\App::getInstance()->getEnv('GMAIL_MAIL'));
            $transport->setPassword(\App\App::getInstance()->getEnv('GMAIL_PASSWORD'));
            $sender = [\App\App::getInstance()->getEnv('GMAIL_MAIL')
                    => \App\App::getInstance()->getEnv('GMAIL_PSEUDO')];
        }

        // Crée le Mailer utilisant le Transport
        $mailer = new \Swift_Mailer($transport);
        
        // Crée le message en HTML et texte
        $message = new \Swift_Message($sujet);
        $message->setFrom($sender);
        if ($cci) {
            $message->setBcc($mailTo);
        } else {
            $message->setTo($mailTo);
        }
    
        if (is_array($msg) && array_key_exists('text', $msg) && array_key_exists('html', $msg)) {
            $message->setBody($msg['html'], 'text/html');
            $message->addPart($msg['text'], 'text/plain');
        } elseif (is_array($msg) && array_key_exists('html', $msg)) {
            $message->setBody($msg["html"], 'text/html');
            $message->addPart($msg["html"], 'text/plain');
        } elseif (is_array($msg) && array_key_exists("text", $msg)) {
            $message->setBody($msg["text"], 'text/plain');
        } elseif (is_array($msg)) {
            die('erreur une clé n\'est pas bonne');
        } else {
            $message->setBody($msg, 'text/plain');
        }
        if (!empty($from)) {
            // ajouter un Header
            $headers = $message->getHeaders();
            // "From: $from\nReply-to: $from\n"
            $headers->addMailboxHeader('From', [$from]);
            $headers->addMailboxHeader('Reply-to', [$from]);
        }
        // envoie le message
        return($mailer->send($message));
    }
}
