<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Dialog\Question;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Traits\ReloadTrait;
use Livro\Traits\DeleteTrait;

class ProdutosList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    private $filters;

    use DeleteTrait;
    use ReloadTrait
    {
        onReload as onReloadTrait;
    }

    public function __construct ( )
    {
        parent::__construct ( );

        $this -> connection = 'livro';      // nome da conexão
        $this -> activeRecord = 'Produto';  // nome do Active Record

        // instancia um formulário de buscas
        $this -> form = new FormWrapper ( new Form ( 'form_busca_produtos' ) );
        $this -> form -> setTitle ( 'Produtos' );

        // cria os campos do formulário
        $descricao = new Entry ( 'descricao' );
        $this -> form -> addField ( 'Descrição', $descricao, '100%' );
        $this -> form -> addAction ( 'Buscar', new Action ( array ( $this, 'onReload' ) ) );
        $this -> form -> addAction ( 'Cadastrar', new Action ( array ( new ProdutosForm, 'onEdit' ) ) );

        // instancia objeto Datagrid
        $this -> datagrid = new DatagridWrapper ( new Datagrid );
        
        // instancia as colunas da Datagrid
        $codigo = new DatagridColumn ( 'id', 'Código', 'center', '10%' );
        $descricao = new DatagridColumn ( 'descricao', 'Descrição', 'left', '30%' );
        $fabrica = new DatagridColumn ( 'nome_fabricante', 'Fabricante', 'left', '30%' );
        $estoque = new DatagridColumn ( 'estoque', 'Estoque', 'right', '15%' );
        $preco = new DatagridColumn ( 'preco_venda', 'Venda', 'right', '15%' );
        
        // adiciona as colunas à Datagrid
        $this -> datagrid -> addColumn ( $codigo );
        $this -> datagrid -> addColumn ( $descricao );
        $this -> datagrid -> addColumn ( $fabrica );
        $this -> datagrid -> addColumn ( $estoque );
        $this -> datagrid -> addColumn ( $preco );

        $this -> datagrid -> addAction ( 'Editar', new Action ( [
            new ProdutosForm, 
            'onEdit'
        ] ), 'id', 'fa fa-edit fa-lg blue' );
        $this -> datagrid -> addAction ( 'Excluir', new Action ( [
            $this, 
            'onDelete'
        ] ), 'id', 'fa fa-trash fa-lg red' );

        // monta a página através de uma caixa
        $box = new VBox;
        $box -> style = 'display:block';
        $box -> add ( $this -> form );
        $box -> add ( $this -> datagrid );

        parent::add ( $box );
    }
    
    function onReload ( )
    {
        // obtém os dados do formulário de buscas
        $dados = $this -> form -> getData ( );

        // verifica se o usuario preencheu o formulário
        if ( $dados -> descricao ) {
            // filtra pela descrição do produto
            $this -> filters[] = [
                'descricao',
                'like',
                "%{$dados -> descricao}%",
                'AND'
            ];
        }

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
