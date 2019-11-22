<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Transaction;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Traits\SaveTrait;
use Livro\Traits\EditTrait;
use Livro\Traits\ReloadTrait;
use Livro\Traits\DeleteTrait;

class FabricantesFormList extends Page
{
	private $form;
	private $datagrid;
	private $loaded;
	private $connection;
	private $activeRecord;

	use EditTrait;
	use DeleteTrait;
	use ReloadTrait
	{
		onReload as onReloadTrait;
	}
	use SaveTrait
	{
		onSave as onSaveTrait;
	}
	
	public function __construct ( ) 
	{
		parent::__construct ( );

		$this -> connection = 'livro';		// nome da conexão
		$this -> activeRecord = 'Fabricante';	// nome do Active Record

		// instancia um formulário
		$this -> form = new FormWrapper ( new Form ( 'form_fabricantes' ) );
		$this -> form -> setTitle ( 'Fabricantes' );

		// cria os campos do formulário
		$codigo = new Entry ( 'id' );
		$nome = new Entry ( 'nome' );
		$site = new Entry ( 'site' );

		// define alguns atributos para os campos do formulário
		$codigo -> setEditable ( false );
		
		$this -> form -> addField ( 'Código', $codigo, '30%' );
		$this -> form -> addField ( 'Nome', $nome, '70%' );
		$this -> form -> addField ( 'Site', $site, '70%' );
		
		// adiciona as ações
		$this -> form -> addAction ( 'Salvar', new Action ( array ( $this, 'onSave' ) ) );
		$this -> form -> addAction ( 'Limpar', new Action ( array ( $this, 'onEdit' ) ) );

		// instancia a Datagrid
        $this -> datagrid = new DatagridWrapper ( new Datagrid );
        
        // instancia as colunas da Datagrid
        $codigo = new DatagridColumn ( 'id', 'Código', 'center', '10%' );
        $nome = new DatagridColumn ( 'nome', 'Nome', 'left', '50%' );
        $site = new DatagridColumn ( 'site', 'Site', 'left', '40%' );
        
        // adiciona as colunas à Datagrid
        $this -> datagrid -> addColumn ( $codigo );
        $this -> datagrid -> addColumn ( $nome );
        $this -> datagrid -> addColumn ( $site );

        $this -> datagrid -> addAction ( 'Editar', new Action ( [
            $this, 
            'onEdit'
        ] ), 'id', 'fa fa-edit fa-lg blue' );
        $this -> datagrid -> addAction ( 'Excluir', new Action ( [
            $this, 
            'onDelete'
        ] ), 'id', 'fa fa-trash fa-lg red' );

        // monta a página através de uma tabela
        $box = new VBox;
        $box -> style = 'display:block';
        $box -> add ( $this -> form );
        $box -> add ( $this -> datagrid );

        parent::add ( $box );
	}

	public function onSave ( )
	{
		$this -> onSaveTrait ( );
		$this -> onReload ( );
	}

	public function onReload ( )
	{
		$this -> onReloadTrait ( );
		$this -> loaded = true;
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
