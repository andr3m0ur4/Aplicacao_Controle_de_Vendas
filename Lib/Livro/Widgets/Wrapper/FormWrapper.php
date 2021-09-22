<?php

namespace Livro\Widgets\Wrapper;

use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Button;
use Livro\Widgets\Base\Element;
use Livro\Widgets\Container\Card;
use Livro\Widgets\Container\Panel;

/**
 * Decora formulários no formato Bootstrap
 */
class FormWrapper
{
    private $decorated;
    
    /**
     * Constrói o decorator
     */
    public function __construct(Form $form)
    {
        $this->decorated = $form;
    }
    
    /**
     * Redireciona chamadas para o objeto decorado
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->decorated, $method], $parameters);
    }
    
    /**
     * Exibe o formulário
     */
    public function show()
    {
        $element = new Element('form');
        $element->class = "form-horizontal";
        $element->enctype = "multipart/form-data";
        $element->method = 'post';    // método de transferência
        $element->name = $this->decorated->getName();
        $element->width = '100%';

        foreach ($this->decorated->getFields() as $field) {
            $group = new Element('div');
            $group->class = 'form-group';

            $label = new Element('label');
            $label->class = 'col-sm-2 control-label';
            $label->add($field->getLabel());

            $col = new Element('div');
            $col->class = 'col-sm-10';
            $col->add($field);
            $field->class = 'form-control';

            $group->add($label);
            $group->add($col);
            $element->add($group);
        }

        $group = new Element('div');

        $i = 0;

        foreach ($this->decorated->getActions() as $label => $action) {
            $name = strtolower(str_replace( ' ', '_', $label));

            $button = new Button($name);
            $button->setFormName($this->decorated->getName());
            $button->setAction($action, $label);
            $button->class = 'btn ' . (($i == 0) ? 'btn-success' : 'btn-secondary');

            $group->add($button);
            $i++;
        }

        $card = new Panel($this->decorated->getTitle());
        $card->add($element);
        $card->addFooter($group);
        $card->show();
    }
}
