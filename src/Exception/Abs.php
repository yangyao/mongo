<?php
namespace Yangyao\Mongo\Exception;
use Exception;
abstract class Abs extends Exception
{
    protected $data;

    /**
     * 构造函数
     *
     * @param string $message 错误消息
     * @param int $code 错误代码
     */
    function __construct($message, $code = 0, $data = null)
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    /**
     * 输出异常的详细信息和调用堆栈
     *
     * @code php
     * QException::dump($ex);
     * @endcode
     */
    static function dump(Exception $ex)
    {
        $out = "exception '" . get_class($ex) . "'";
        if ($ex->getMessage() != '')
        {
            $out .= " with message '" . $ex->getMessage() . "'";
        }

        $out .= ' in ' . $ex->getFile() . ':' . $ex->getLine() . "\n\n";
        $out .= $ex->getTraceAsString();

        if (ini_get('html_errors'))
        {
            echo nl2br(htmlspecialchars($out));
        }
        else
        {
            echo $out;
        }
    }

    public function getData(){
        return $this->data;
    }
}