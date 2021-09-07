<?php

use Livro\Control\Page;
use Livro\Widgets\Dialog\Message;
use Livro\Database\Transaction;
use Livro\Widgets\Container\Panel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class PessoasReport extends Page
{
	
	public function __construct ( ) 
	{
		parent::__construct ( );

		$loader = new FilesystemLoader('App/Resources');
		$twig = new Environment($loader);
		$template = $twig->loadTemplate('pessoas_report.html');

		// vetor de parâmetros para o template
		$replaces = array ( );
		
		try {
			// inicia transação com o banco 'livro'
			Transaction::open ( 'livro' );

			$replaces['pessoas'] = ViewSaldoPessoa::all ( );

			// finaliza a transação
			Transaction::close ( );

		} catch ( Exception $e ) {
			new Message ( 'error', $e -> getMessage ( ) );
			Transaction::rollback ( );
		}

		$content = $template -> render ( $replaces );

		// cria um painél para conter o formulário
		$panel = new Panel ( 'Pessoas' );
		$panel -> add ( $content );
		
		parent::add ( $panel );
		
	}
}
