<?php
namespace Core\Controller;

class MailsController extends Controller
{
    /**
     * @var \Swift_Mailer
     */
    private $mailService;

    private $message;

    public function __construct(string $object = null)
    {
        if (getenv('ENV_DEV')) {
            //dev
            $transport = new \Swift_SmtpTransport(
                getenv('CONTAINER_NAME').'.mailcatcher',
                1025
            );
            $sender = ["mail@test.fr"=>"adminDev"];
        } else {
            //prod
            $transport = new \Swift_SmtpTransport(
                'smtp.gmail.com',
                587,
                'tls'
            );
            $transport->setUsername(getenv('GMAIL_MAIL'));
            $transport->setPassword(getenv('GMAIL_PASSWORD'));
            $sender = [getenv('GMAIL_MAIL') => getenv('GMAIL_PSEUDO')];
        }
        $this->mailService = new \Swift_Mailer($transport);
        $message = new \Swift_Message($object);
        $message->setFrom($sender);

        $headers = $message->getHeaders();
        $headers->addMailBoxHeader('From', $sender);
        $headers->addMailBoxHeader('Reply-to', $sender);
        $this->message = $message;
    }

    public function object(string $object): self
    {
        $this->message->setSubject($object);
        return $this;
    }

    public function to(string $mail): self
    {
        $this->message->setTo($mail);
        return $this;
    }

    public function send(): void
    {
        $this->mailService->send($this->message);
    }

    public function message(string $template, array $datas = null): self
    {
        $this->message->setBody(
            $this->render("email/".$template.".html", $datas),
            "text/html"
        );
        $this->message->addPart(
            $this->render("email/".$template.".txt", $datas),
            "text/plain"
        );
        return $this;
    }
}
