<?php

namespace Livro\Control;

use Livro\Widgets\Base\Element;

/**
 * Page controller
 */
class Page extends Element
{
    /**
     * Método construtor
     */
    public function __construct()
    {
        parent::__construct('div');
    }
    
    /**
     * Executa determinado método de acordo com os parâmetros recebidos
     */
    public function show()
    {
        if ($_GET) {
            $class  = $_GET['class'] ?? null;
            $method = $_GET['method'] ?? null;
            
            if ($class) {
                $object = $class == get_class($this) ? $this : new $class;

                if (method_exists($object, $method)) {
                    call_user_func([$object, $method], $_GET);
                }
            }
        }
        
        parent::show();
    }
}
