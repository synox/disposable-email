<?php
namespace DisposableEmail;

class Input {

  private $msgRaw;
  private $parser;
  function __construct($msgRaw) {
    $this->msgRaw = $msgRaw;
    $this->parser = new \eXorus\PhpMimeMailParser\Parser();
    $this->parser->setText($msgRaw);
  }

  public function getBean() {
    $mail              = \R::dispense( 'mail' );
    $mail->username    = $this->getUsername();
    $mail->to          = $this->parser->getHeader('to');
    $mail->from        = $this->parser->getHeader('from');
    $mail->received    = time();
    $mail->date        = $this->parser->getHeader('date');
    $mail->subject     = $this->parser->getHeader('subject');
    $mail->body_text   = $this->parser->getMessageBody('text');
    $mail->body_html   = $this->parser->getMessageBody('html');
    $mail->deliveredTo = $this->getDeliveredTo();
    $mail->raw         = $this->msgRaw;
    return $mail;
  }

  // isolate real username
  public function getUsername() {
    $pattern = '/([^\<]+)@/';
    if( preg_match($pattern, $this->getDeliveredTo(), $matches) ) {
        return $matches[1];
    }
    return "-";
  }

  // extract final recipient
  public function getDeliveredTo() {
    $deliveredTo    =     $this->parser->getHeader('delivered-to');
    if($deliveredTo == NULL) {
      $deliveredTo = $this->parser->getHeader('to');
    }
    return $deliveredTo;
  }
}
