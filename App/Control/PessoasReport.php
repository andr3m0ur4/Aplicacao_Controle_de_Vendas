<?php

use Livro\Control\Page;
use Livro\Widgets\Dialog\Message;
use Livro\Database\Transaction;
use Livro\Widgets\Container\Panel;

class PessoasReport extends Page
{
	
	public function __construct ( ) 
	{
		parent::__construct ( );

		$loader = new Twig_loader_FileSystem ( 'App/Resources' );
		$twig = new Twig_Environment ( $loader );
		$template = $twig -> loadTemplate ( 'pessoas_report.html' );

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
