<?php

namespace Livro\Widgets\Container;

use Livro\Widgets\Base\Element;

/**
 * Empacota elementos em card Bootstrap
 * @author André Moura
 */
class Card extends Element
{
    private $body;
    private $footer;
    
    /**
     * Constrói o card
     */
    public function __construct($card_title = null)
    {
        parent::__construct('div');
        $this->class = 'card';
        
        if ($card_title) {
            $head = new Element('div');
            $head->class = 'card-header';

            $label = new Element('h4');
            $label->add($card_title);
            
            $title = new Element('div');
            $title->class = 'card-title';
            $title->add($label);
            $head->add($title);
            parent::add($head);
        }
        
        $this->body = new Element('div');
        $this->body->class = 'card-body';
        parent::add($this->body);
        
        $this->footer = new Element('div');
        $this->footer->{'class'} = 'card-footer';
        
    }
    
    /**
     * Adiciona conteúdo
     */
    public function add($content)
    {
        $this->body->add($content);
    }
    
    /**
     * Adiciona rodapé
     */
    public function addFooter($footer)
    {
        $this->footer->add($footer);
        parent::add($this->footer);
    }
}
