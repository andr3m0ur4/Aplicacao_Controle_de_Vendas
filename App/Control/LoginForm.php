<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Password;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Session\Session;

class LoginForm extends Page
{
	private $form;		// formulário
	
	public function __construct() 
	{
		parent::__construct();

		// instancia um formulário
		$this->form = new FormWrapper(new Form('form_login'));
		$this->form->setTitle('Login');

		// cria os campos do formulário
		$login = new Entry('login');
		$password = new Password('password');

		$login->placeholder = 'admin';
		$password->placeholder = '123456';
		
		$this->form->addField('Login', $login, 200);
		$this->form->addField('Senha', $password, 200);
		
		// adiciona as ações
		$this->form->addAction('Login', new Action([$this, 'onLogin']));
		
		// adiciona o formulário na página
		parent::add($this->form);
	}

	public function onLogin ( $param ) 
	{
		// obtém os dados
        $data = $this -> form -> getData ( );
        if ( $data -> login == 'admin' AND $data -> password == '123456' ) {
        	Session::setValue ( 'logged', true );
        	echo "<script language='JavaScript'>
        			window.location = 'index.php';
        		</script>";
        }
	}

	public function onLogout ( $param ) 
	{
		Session::setValue ( 'logged', false );
		echo "<script language='JavaScript'>
        			window.location = 'index.php';
        		</script>";
	}
}
