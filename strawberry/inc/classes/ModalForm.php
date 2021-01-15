<?php

namespace classes;

use classes\Template;
use classes\PHPMailer;
use classes\CuteParser;

final class ModalForm extends CuteParser
{
    const SUBJECT = 'Заявка с сайта';
    const MESSAGE = '<p>Сообщение с сайта от %s (%s).</p><br />%s';
    const ISWRITE = true; // записывать данные в лог

    private $action;
    private $header;
    private $method;
    private $module = 'requestForm';
    private $schema = ['www.', 'http://', 'https://'];
    private $errors = [];

    public $result;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->action = $_POST['action'] ?? null;
        $this->header = $_SERVER['HTTP_X_REQUESTED_WITH'];
        
        if (!$this->header or strtolower($this->header) != 'xmlhttprequest')
        {
            exit;
        }
    }

    public function run()
    {
        return isset($this->action) ? $this->send(new PHPMailer) : $this->form();
    }

    /**
     * Отравка сообжения на Email
     */

    private function send(PHPMailer $mailer)
    {
        if (!isset($this->action) or $this->action != 'orderform')
        {
            exit;
        }

        //if ($this->method != 'POST')
        //{
        //    exit;
        //}

        foreach ($_POST as $k => $v)
        {
            $$k = trim($v);
        }
    
        if (empty($name))
        {
            $this->errors[] = t('Заполните ваше имя.');
        }
     
        if (empty($phone) or !preg_match('/^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/', $phone))
        {
            $this->errors[] = t('Укажите корректный телефон.');
        }
    
        if (reset($this->errors))
        {
			foreach ($this->errors as $k => $v) {
				printf ('<li>%s.</li>', $v);
			}
	
			header("HTTP/1.1 500 Internal Server Error");
            exit;
        }

        $name    = filter_var($name, FILTER_SANITIZE_STRING);
        $phone   = filter_var($phone, FILTER_SANITIZE_STRING);
        $comment = filter_var($comment, FILTER_SANITIZE_STRING);

        try
        {
            $mailer->From    = 'no-reply@' . str_replace($this->schema, '', $this->Config['http_script_dir']);
            $mailer->Body    = printf (self::MESSAGE, $name, $phone, $comment);
            $mailer->Subject = self::SUBJECT;
            $mailer->CharSet = $this->Config['charset'];
            $mailer->AddAddress($this->Config['admin_mail']); // Добавляем адрес в список получателей
            $mailer->IsHTML(true);

            if ($mailer->Send())
            {
                $mailer->ClearAddresses();
                $mailer = null;
                exit("Ваше сообщение отправленно!");
            }
             
        } catch (Exception $e) {
            $this->logWrite('Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Вызов формы
     */

    private function form($dir = templates_directory.'/Forms/')
    {
        $template = (new Template($dir))->open($this->module, $this->module);
        return $template->compile($this->module, true);
        $template->fullClear();
    }

    /**
     * Запись ошибок
     */

    private function logWrite($message)
    {
        if (self::ISWRITE === false)
        {
            return;
        }

        $output = date('d.m.Y H:i:s') . PHP_EOL . $message . PHP_EOL . '-------------------------' . PHP_EOL;
        file_put_contents(rootpath . '/logs.txt', $output, FILE_APPEND | LOCK_EX);
    }

    //public function __toString ()
    //{
        //return $this->run();
    //}
}
