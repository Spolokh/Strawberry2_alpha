<?php

/*
 * Класс для генерации постраничной навигации
 */

namespace classes;

use classes\Blitz;

class Pagination extends Blitz
{
    const SKIP = 'skip';
    /**
     * 
     * @var Ссылок навигации на страницу
     * 
     */
    protected $max = 10;

    /**
     * 
     * @var Ключ для GET, в который пишется номер страницы
     * 
     */
    protected $index = 'skip';

    /**
     * 
     * @var Общее количество записей
     * 
     */
    protected $total;

    /**
     * 
     * @var Записей на страницу
     * 
     */
    protected $limit;
    protected $pages;

     /**
     * 
     * @var Текущая страница
     * 
     */
    protected $skip;
    protected $body;
    protected $data = [];

    /**
     * Запуск необходимых данных для навигации
     * @param integer $total - общее количество записей
     * @param integer $limit - количество записей на страницу
     * 
     * @return
     */
    public function __construct($total, $limit, $skip = null)
    {
        # Устанавливаем общее количество записей
        $this->total = $total;

        # Устанавливаем количество записей на страницу
        $this->limit = $limit;

        # Устанавливаем url шаблона
        $this->pages = themes_directory.'/pages.tpl';

        # Устанавливаем url текущей страницы
        $this->skip = $_GET['skip'] ?? $skip;

        # Устанавливаем количество страниц
        $this->amount = $this->amount();

        # Устанавливаем номер текущей страницы
        $this->setPage($skip);
    }

    /**
     *  Для вывода ссылок
     * 
     * @return HTML-код со ссылками навигации
     */
    public function get()
    {
        $limits = $this->limits();

        # Генерируем ссылки
        if ($this->skip > 1) { //$this->generateHtml(1, '&lt;')
            $this->data['pages'][0] = [
                'link'   => cute_get_link([self::SKIP => 1], self::SKIP),
                'page'   => '',
                'active' => 'icon-chevron-left'
            ];
        }
        
        for ($i = $limits[0]; $i <= $limits[1]; $i++) {
            
            # Если текущая это текущая страница, ссылки нет и добавляется класс active
            if ($i == $this->skip)
            {
                $this->data['pages'][$i] = [
                    'page'  => $i, 
                    'link'  => '',
                    'active'=> 'active'
                ];
            } else {
                # Иначе генерируем ссылку
                $this->data['pages'][$i] = [
                    'page'  => $i, 
                    'link'  => cute_get_link([self::SKIP => $i], self::SKIP),
                    'active'=> ''
                ];
            }
        }

        # Если ссылки создались
        if (!empty($this->data)) { //$this->generateHtml($this->amount, '&gt;')]
            if ($this->skip < $this->amount)
            {
                $this->data['pages'][$this->amount] = [
                    'page' => '',
                    'link' => cute_get_link([self::SKIP => $this->amount], self::SKIP),
                    'active' => 'icon-chevron-right'
                ];
            }
        }

        $this->file($this->pages);
        $this->set ($this->data);
        $this->display();
    }

    /**
     * Для генерации HTML-кода ссылки
     * @param integer $page - номер страницы
     * 
     * @return
     */
    private function generateHtml($page, $text = null)
    {
        $text = $text ?? $page;
        return '<a href="'.cute_get_link([self::SKIP => $page], self::SKIP).'">'.$text.'</a>';
    }

    /**
     *  Для получения, откуда стартовать
     * 
     * @return массив с началом и концом отсчёта
     */
    private function limits() : array
    {
        # Вычисляем ссылки слева (чтобы активная ссылка была посередине)
        $left = $this->skip - round($this->max / 2);

        # Вычисляем начало отсчёта
        $start = $left > 0 ? $left : 1;

        # Если впереди есть как минимум $this->max страниц
        if ($start + $this->max <= $this->amount)
        # Назначаем конец цикла вперёд на $this->max страниц или просто на минимум
            $end = $start > 1 ? $start + $this->max : $this->max;
        else {
            # Конец - общее количество страниц
            $end = $this->amount;

            # Начало - минус $this->max от конца
            $start = $this->amount - $this->max > 0 ? $this->amount - $this->max : 1;
        }

        # Возвращаем
        return [$start, $end];
    }

    /**
     * Для установки текущей страницы
     * 
     * @return
     */
    private function setPage($skip)
    { 
        $this->skip = ($this->skip > 0) ? (($this->skip > $this->amount) ? $this->amount : $this->skip) : 1;
        # Если текущая страница боле нуля
        #if ($this->skip > 0) {    # Если текунщая страница меньше общего количества страниц
        #    if ($this->skip > $this->amount)  # Устанавливаем страницу на последнюю
        #        $this->skip = $this->amount;
        #} else  # Устанавливаем страницу на первую
        #    $this->skip = 1;
    }

    /**
     * Для получеия общего числа страниц
     * 
     * @return число страниц
     */
    private function amount()
    {
        return round($this->total / $this->limit); # Делим и возвращаем
    }

    public function __toString ()
    {
    }
} 
