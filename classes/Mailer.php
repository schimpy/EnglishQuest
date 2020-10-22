<?php

class Mailer
{

    // ADD MORE SECURITY as PHP MAILER
    // COMLPIANCE WITH GMAIL

    protected $to = array();
    protected $headers = array();
    protected $subject;
    protected $message;
    protected $attachment = array();
    protected $parameters;
    protected $wrap = 78;
    protected $uid;

    public static function make() {
        return new Mailer;
    }

    public function __construct($transport = 'mail') {
        $this->reset();
    }

    public function reset() {
        $this->to = array();
        $this->headers = array();
        $this->subject = null;
        $this->message = null;
        $this->attachment = array();
        $this->parameters = null;
        $this->wrap = 78;
        $this->uid;
        return $this;
    }

    public function setTo($email, $name = null) {
        $this->to[] = $this->formatHeader((string) $email, (string) $name);
        return $this;
    }

    public function getTo() {
        return $this->to;
    }

    public function setFrom($email, $name = null) {
        $this->addMailHeader('From', (string) $email, (string) $name);
        return $this;
    }

    public function setCc(array $pairs) {
        return $this->addMailHeaders('Cc', $pairs);
    }

    public function setBcc(array $pairs) {
        return $this->addMailHeaders('Bcc', $pairs);
    }

    public function setReplyTo($email, $name = null) {
        return $this->addMailHeader('Reply-To', $email, $name);
    }

    public function setHTML() {
        return $this->addGenericHeader('Content-Type', 'text/html; charset="utf-8"');
    }

    public function setSubject($subject) {
        $this->subject = $this->encodeUTF8($this->sanitizeOther((string) $subject));
        return $this;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function setMessage($message) {
        $this->message = str_replace("\n.", "\n..", (string) $message);
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

    public function addAttachment($path, $filename = null, $data = null) {
        $filename = empty($filename) ? basename($path) : $filename;
        $filename = $this->encodeUTF8($this->sanitizeOther((string) $filename));
        $data = empty($data) ? $this->getAttachmentData($path) : $data;
        $this->attachment[] = array(
            'path' => $path,
            'file' => $filename,
            'data' => chunk_split(base64_encode($data))
        );
        return $this;
    }

    public function getAttachmentData($path) {
        $filesize = filesize($path);
        $handle = fopen($path, "r");
        $attachment = fread($handle, $filesize);
        fclose($handle);
        return $attachment;
    }

    public function hasAttachment() {
        return !empty($this->attachment);
    }

    public function assembleAttachmentHeaders() {
        $head = array();
        $head[] = "MIME-Version: 1.0";
        $head[] = "Content-Type: multipart/mixed; boundary=\"{$this->uid}\"";
        return join(PHP_EOL, $head);
    }

    public function assembleAttachmentBody() {
        $body = array();
        $body[] = "This is a multi-part message in MIME format.";
        $body[] = "--{$this->uid}";
        $body[] = "Content-Type: text/html; charset=\"utf-8\"";
        $body[] = "Content-Transfer-Encoding: quoted-printable";
        $body[] = "";
        $body[] = quoted_printable_encode($this->message);
        $body[] = "";
        $body[] = "--{$this->uid}";
        foreach ($this->attachments as $attachment) {
            $body[] = $this->getAttachmentMimeTemplate($attachment);
        }
        return implode(PHP_EOL, $body) . '--';
    }

    public function getAttachmentMIMETemplate($attachment) {
        $file = $attachment['file'];
        $data = $attachment['data'];
        $head = array();
        $head[] = "Content-Type: application/octet-stream; name=\"{$file}\"";
        $head[] = "Content-Transfer-Encoding: base64";
        $head[] = "Content-Disposition: attachment; filename=\"{$file}\"";
        $head[] = "";
        $head[] = $data;
        $head[] = "";
        $head[] = "--{$this->uid}";
        return implode(PHP_EOL, $head);
    }

    public function formatHeader($email, $name = null) {
        $email = $this->sanitizeEmail((string) $email);
        if(empty($name)) {
            return $email;
        }
        $name = $this->encodeUTF8($this->sanitizeName((string) $name));
        return sprintf('"%s" <%s>', $name, $email);
    }

    public function addMailHeader($header, $email, $name = null) {
        $address = $this->formatHeader((string) $email, (string) $name);
        $this->headers[] = sprintf('%s: %s', (string) $header, $address);
        return $this;
    }

    public function addMailHeaders($header, array $pairs) {
        if(count($pairs) === 0) {
            throw new InvalidArgumentException('You must pass at least one argument');
        }
        $addresses = array();
        foreach($pairs as $pair) {
            $name = is_numeric($name) ? null : $name;
            $addresses[] = $this->formatHeader($email, $name);
        }
        $this->addGenericHeader($header, implode(',', $addresses));
        return $this;
    }

    public function addGenericHeader($header, $value) {
        $this->headers[] = sprintf('%s: %s', (string) $header, (string) $value);
        return $this;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function sanitizeEmail($email) {
        $rule = array(
            "\r" => '',
            "\n" => '',
            "\t" => '',
            '"' => '',
            '<' => '',
            '>' => '',
            ',' => ''
        );
        $email = strtr($email, $rule);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $email;
    }

    public function sanitizeName($name) {
        $rule = array(
            "\r" => '',
            "\n" => '',
            "\t" => '',
            '"' => '',
            '<' => '',
            '>' => ''
        );
        $filtered = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        return trim(strtr($filtered, $rule));
    }

    public function sanitizeOther($other) {
        return filter_var($other, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    }

    public function encodeUTF8($value) {
        $value = trim($value);
        if(preg_match('/(\s)/', $value)) {
            return $this->encodeUTF8Words($value);
        }
        return $this->encodeUTF8Word($value);
    }

    public function encodeUTF8Word($value) {
        return sprintf('=?UTF-8?B?%s?=', base64_encode($value));
    }

    public function encodeUTF8Words($value) {
        $words = explode(' ', $value);
        $encoded = array();
        foreach($words as $word) {
            $encoded[] = $this->encodeUTF8Word($word);
        }
        return join($this->encodeUTF8Word(' '), $encoded);
    }

    public function setParameters($param) {
        $this->parameters = (string) $param;
        return $this;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function setWrap($wrap = 78) {
        $wrap = (int) $wrap;
        if($wrap < 1) {
            $wrap = 78;
        }
        $this->wrap = $wrap;
        return $this;
    }

    public function getWrap() {
        return $this->wrap;
    }

    public function getWrapMessage() {
        return wordwrap($this->message, $this->wrap);
    }

    public function getUniqueID() {
        return md5(uniqid(time()));
    }

    public function send() {
        $to = $this->getToForSend();
        $headers = $this->getHeadersForSend();

        if(empty($to)) {
            throw new RuntimeException('No adressee has been set.');
        }

        if($this->hasAttachment()) {
            $message = $this->assembleAttachmentBody();
            $headers .= PHP_EOL . $this->assembleAttachmentHeaders;
        } else {
            $message = $this->getWrapMessage();
        }

        return mail($to, $this->subject, $message, $headers);

    }

    public function getToForSend() {
        if(empty($this->to)) {
            return '';
        }
        return join(', ', $this->to);
    }

    public function getHeadersForSend() {
        if(empty($this->headers)) {
            return '';
        }
        return join(PHP_EOL, $this->headers);
    }
}