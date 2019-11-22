<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Transaction;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Session\Session;

class VendasForm extends Page
{
	private $form;
	private $datagrid;
	private $loaded;
	
	public function __construct ( ) 
	{
		parent::__construct ( );

		new Session;		// instancia nova seção

		// instancia um formulário
		$this -> form = new FormWrapper ( new Form ( 'form_vendas' ) );
		$this -> form -> setTitle ( 'Venda' );

		// cria os campos do formulário
		$codigo = new Entry ( 'id_produto' );
		$quantidade = new Entry ( 'quantidade' );
		
		$this -> form -> addField ( 'Código', $codigo, '50%' );
		$this -> form -> addField ( 'Quantidade', $quantidade, '50%' );
		
		// adiciona as ações
		$this -> form -> addAction ( 'Adicionar', new Action ( array ( $this, 'onAdiciona' ) ) );
		$this -> form -> addAction ( 'Terminar', new Action ( array ( new ConcluiVendaForm, 'onLoad' ) ) );

		// instancia objeto Datagrid
        $this -> datagrid = new DatagridWrapper ( new Datagrid );
        
        // instancia as colunas da Datagrid
        $codigo = new DatagridColumn ( 'id_produto', 'Código', 'center', '20%' );
        $descricao = new DatagridColumn ( 'descricao', 'Descrição', 'left', '40%' );
        $quantidade = new DatagridColumn ( 'quantidade', 'Qtde', 'right', '20%' );
        $preco = new DatagridColumn ( 'preco', 'Preço', 'right', '20%' );

        // define um transformador para a coluna preço
        $preco -> setTransformer ( array ( $this, 'formata_money' ) );
        
        // adiciona as colunas à Datagrid
        $this -> datagrid -> addColumn ( $codigo );
        $this -> datagrid -> addColumn ( $descricao );
        $this -> datagrid -> addColumn ( $quantidade );
        $this -> datagrid -> addColumn ( $preco );
        
        $this -> datagrid -> addAction ( 'Excluir', new Action ( [
            $this, 
            'onDelete'
        ] ), 'id_produto', 'fa fa-trash fa-lg red' );

        // monta a página através de uma caixa
        $box = new VBox;
        $box -> style = 'display:block';
        $box -> add ( $this -> form );
        $box -> add ( $this -> datagrid );

        parent::add ( $box );
	}

	public function onAdiciona ( )
	{
		try {
			// obtém os dados do formulário
			$item = $this -> form -> getData ( );

			Transaction::open ( 'livro' );						// abre transação
			$produto = Produto::find ( $item -> id_produto );	// carrega o produto

			if ( $produto ) {
				// busca mais informações do produto
				$item -> descricao = $produto -> descricao;
				$item -> preco = $produto -> preco_venda;

				$list = Session::getValue ( 'list' );			// lê variável $list da seção
				$list[$item -> id_produto] = $item;				// acrescenta produto na variável

				Session::setValue ( 'list', $list );			// grava variável de volta à seção
			}
			Transaction::close ( 'livro' );						// fecha transação

		} catch ( Exception $e ) {
			new Message ( 'error', $e -> getMessage ( ) );
		}
		
		$this -> onReload ( );		// recarrega a listagem
	}

	public function onDelete ( $param )
	{
		// lê variável $list da seção
		$list = Session::getValue ( 'list' );

		// exclui a posição que armazena o produto de código
		unset ( $list[$param['id_produto']] );

		// grava variável $list de volta à seção
		Session::setValue ( 'list', $list );

		// recarrega a listagem
		$this -> onReload ( );
	}

	public function onReload ( )
	{
		// obtém a variável de seção $list
		$list = Session::getValue ( 'list' );

		// limpa a Datagrid
		$this -> datagrid -> clear ( );

		if ( $list ) {
			foreach ( $list as $item ) {
				$this -> datagrid -> addItem ( $item );	// adiciona cada objeto
			}
		}

		$this -> loaded = true;
	}

	public function formata_money ( $value )
	{
		return number_format ( $value, 2, ',', '.' );
	}

	function show ( )
    {
         // se a listagem ainda não foi carregada
         if ( !$this -> loaded ) {
	        $this -> onReload ( );
         }
         parent::show ( );
    }
}
