<?php

namespace Livro\Widgets\Container;

use Livro\Widgets\Base\Element;

/**
 * Caixa horizontal
 * @author André Moura
 */
class HBox extends Element
{
    /**
     * Método construtor
     */
    public function __construct ( )
    {
        parent::__construct ( 'div' );
    }
    
    /**
     * Adiciona um elemento filho
     * @param $child Objeto filho
     */
    public function add ( $child )
    {
        $wrapper = new Element ( 'div' );
        $wrapper -> {'style'} = 'display:inline-block;';
        $wrapper -> add ( $child );
        parent::add ( $wrapper );
        return $wrapper;
    }
}
