<?php

/*
 * @package RS-MINI
 * @copyright (c) 2015 Alexey Glumov aka Rio-Shaman (support@rio-shaman.ru)
 * @license GNU General Public License version 2; see LICENSE.txt
 *
 */

defined('rootpath') or die;

class Request
{
    /*
     * хранит значение полученное методами 
     * getHttpPost(), 
     * getHttpGet(), 
     * getHttpReq(), 
     * getHttpFiles(), 
     * getHttpCookie()
     *
     * @var    - string
     * @access - private
     */
    
    private $data = NULL;
    /*
     * получаем данные из глобавльного массива $_SESSION
     * 
     * @access - public
     * @return - this
     * 
     * @param string name - ключ элемента массива
     */
     
    public function Session($name, $v = NULL)
    {
        $this->data = $_SESSION[$name] ?? $v;
        return $this;
    }
    
    /*
     * получаем данные из глобавльного массива $_COOKIE
     * 
     * @access - public
     * @return - сам себя
     * 
     * @param string name - ключ элемента массива
     */
     
    public function Cookie($name, $v = NULL)
    {
        $this->data = $_COOKIE[$name] ?? $v;
        return $this;
    }
    
    /*
     * получаем данные из глобавльного массива $_POST
     * 
     * @access - public
     * @return - сам себя
     * 
     * @param string name - ключ элемента массива
     */
     
    public function Post($name, $v = NULL)
    {
        $this->data = $_POST[$name] ?? $v;
        return $this;
    }
    
    /*
     * получаем данные из глобавльного массива $_GET
     * 
     * @access - public
     * @return - this
     * 
     * @param string name - ключ элемента массива
     */
    
    public function Get($name, $v = NULL)
    {
        $this->data = $_GET[$name] ?? $v;
        return $this;
    }
    
    /*
     * получаем данные из глобавльного массива $_REQUEST
     * 
     * @access - public
     * @return - this
     * 
     * @param string name - ключ элемента массива
     */
    
    public function Req($name, $v = NULL)
    {
        $this->data = $_REQUEST[$name] ?? $v;
        return $this;
    }
    
    /*
     * получаем данные из глобавльного массива $_FILES
     * 
     * @access - public
     * @return - this
     * 
     * @param string name - ключ элемента массива
     */
    
    public function File($name, $v = NULL)
    {
        $this->data = $_FILES[$name] ?? $v;
        return $this;
    }
    
    /*
     * получаем данные из глобавльного массива $_SERVER. 
     * В основном метод написан для получение HTTP_HOST и REQUEST_URI ключей
     * 
     * @access - public
     * @return - this
     * 
     * @param string name - ключ элемента массива
     */
    
    public function Server($name, $v = NULL)
    {
        $this->data = $_SERVER[$name] ?? $v;
        return $this;
    }

    /*
     * обрабатывает данные в свойстве $this->data как стороку
     * 
     * @access - public
     * @return - обработанные данные или null если ничего не найдено
     * 
     */
    
    public function toString()
    {
        return $this->data ? filter_var($this->data, FILTER_SANITIZE_STRING): NULL;
    }

    /*
     * обрабатывает данные в свойстве $this->data как целое число
     * 
     * @access - public
     * @return - обработанные данные или null если ничего не найдено
     * 
     */
    
    public function toInteger()
    {
        return $this->data? filter_var($this->data, FILTER_SANITIZE_NUMBER_INT): NULL;
    }
    
    /*
     * обрабатывает данные в свойстве $this->data как число с плавующей точкой
     * 
     * @access - public
     * @return - обработанные данные или null если ничего не найдено
     * 
     */
    
    public function toFloat()
    {
        return (float) $this->data;
    }

    public function toUri()
    {
        return  $this->data? filter_var($this->data, FILTER_SANITIZE_URL): NULL;
    }
}
