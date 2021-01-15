<?php

class Request
{
    private $string; // переменная хранящая данные GET и POST
    private $result = [];

    // при создании объекта запроса мы пропускаем все данные
    // через фильтр-функцию для очистки параметров от нежелательных данных
    public function __construct () {
        $this->string = $this->cleanInput($_REQUEST);
    }

    // магическая функция, которая позволяет обращатья к GET и POST переменным по имени.
    public function __get($name)
    {
        if ( isset($this->string[$name]) ) {
            return $this->string[$name];
        }
    }
   
    // очистка данных от опасных символов
    private function cleanInput($data)
    {
        if (is_array($data)) {
            
            foreach ($data as $k => $v) {
                $result[$k] = $this->cleanInput($v);
            }
            return $result;
        }
        //filter_var($data, FILTER_SANITIZE_STRING);
        return trim(htmlspecialchars($data, ENT_QUOTES));
    }

    // возвращаем содержимое хранилища
    public function getRequestEntries()
    {
        return $this->string;
    }
}

//$request = new Request(); // создаем объект класса Request
// а здесь обращаемся к значениям, заполненных пользователем
//echo sprintf("Имя: %s, Телефон: %s", $request->name, $request->mail);
