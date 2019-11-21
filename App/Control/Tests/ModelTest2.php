<?php 

use Livro\Control\Page;
use Livro\Database\Transaction;

class ModelTest2 extends Page
{
	public function show ( )
	{
		try {
			Transaction::open ( 'livro' );

			// busca pessoa 1
			$p1 = Pessoa::find ( 1 );
			$p1 -> delGrupos ( );					// apaga grupos
			$p1 -> addGrupo ( new Grupo ( 1 ) );	// adiciona grupo
			$p1 -> addGrupo ( new Grupo ( 3 ) );	// adiciona grupo

			$grupos = $p1 -> getGrupos ( );			// carrega grupos

			if ( $grupos ) {
				foreach ( $grupos as $grupo ) {
					print $grupo -> id . ' - ';
					print $grupo -> nome . '<br>';
				}
			}

			Transaction::close ( );
		} catch (Exception $e) {
			echo $e -> getMessage ( );
		}
	}
}
