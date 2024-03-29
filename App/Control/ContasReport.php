<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Date;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Wrapper\FormWrapper;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ContasReport extends Page
{
	private $form;		// formulário de entrada

	public function __construct ( ) 
	{
		parent::__construct ( );
		
		// instancia um formulário
		$this -> form = new FormWrapper ( new Form ( 'form_relat_contas' ) );
		$this -> form -> setTitle ( 'Relatório de contas' );

		// cria os campos do formulário
		$data_ini = new Date ( 'data_ini' );
		$data_fim = new Date ( 'data_fim' );
		
		$this -> form -> addField ( 'Vencimento inicial', $data_ini, '50%' );
		$this -> form -> addField ( 'Vencimento final', $data_fim, '50%' );
		
		// adiciona as ações
		$this -> form -> addAction ( 'Gerar', new Action ( array ( $this, 'onGera' ) ) );
		$this -> form -> addAction ( 'PDF', new Action ( array ( $this, 'onGeraPDF' ) ) );

        parent::add ( $this -> form );
	}

	public function onGera ( )
	{
		$loader = new FilesystemLoader('App/Resources');
		$twig = new Environment($loader);
		$template = $twig->loadTemplate('contas_report.html');

		// obtém os dados do formulário
		$dados = $this -> form -> getData ( );

		// joga os dados de volta ao formulário
		$this -> form -> setData ( $dados );

		// lê os campos do formulário, converte para o padrão americano
		$data_ini = $dados -> data_ini;
		$data_fim = $dados -> data_fim;

		// vetor de parâmetros para o template
		$replaces = array ( );
		$replaces['data_ini'] = $dados -> data_ini;
		$replaces['data_fim'] = $dados -> data_fim;

		try {
			// inicia transação com o banco livro
			Transaction::open ( 'livro' );

			// instancia um repositório da classe Conta
			$repositorio = new Repository ( 'Conta' );

			// cria um critério de seleção por intervalo de datas
			$criterio = new Criteria;
			$criterio -> setProperty ( 'order', 'dt_vencimento' );

			if ( $dados -> data_ini ) {
				$criterio -> add ( 'dt_vencimento', '>=', $data_ini );
			}
			if ( $dados -> data_fim ) {
				$criterio -> add ( 'dt_vencimento', '<=', $data_fim );
			}

			// lê todas as contas que satisfazem ao critério
			$contas = $repositorio -> load ( $criterio );
			if ( $contas ) {
				foreach ( $contas as $conta ) {
					$conta_array = $conta -> toArray ( );
					$conta_array['nome_cliente'] = $conta -> cliente -> nome;
					$replaces['contas'][] = $conta_array;
				}
			}

			// finaliza a transação
			Transaction::close ( );

		} catch ( Exception $e ) {
			new Message ( 'error', $e -> getMessage ( ) );
			Transaction::rollback ( );
		}

		$content = $template -> render ( $replaces );
		$title = 'Contas';
		$title .= ( !empty ( $dados -> data_ini ) ) ? ' de ' . $dados -> data_ini : '';
		$title .= ( !empty ( $dados -> data_fim ) ) ? ' até ' . $dados -> data_fim : '';

		// cria um painél para conter o formulário
		$panel = new Panel ( $title );
		$panel -> add ( $content );
		
		parent::add ( $panel );
		return $content;
	}

	public function onGeraPDF ( $param )
	{
		$html = $this -> onGera ( $param );	// gera o relário em HTML primeiro

		$options = new Options ( );
		$options -> set ( 'dpi', '128' );

		// Dompdf converte o HTML para PDF
		$dompdf = new Dompdf ( $options );
		$dompdf -> loadHtml ( $html );
		$dompdf -> setPaper ( 'A4', 'portrait' );
		$dompdf -> render ( );

		// escreve o arquivo e abre em tela
		$filename = 'tmp/contas.pdf';
		if ( is_writable ( 'tmp' ) ) {
			file_put_contents($filename, $dompdf -> output ( ) );
			echo "<script>window.open('{$filename}');</script>";
		} else {
			new Message ( 'error', 'Permissão negada em: ' . $filename );
		}
	}
}
