<?php

namespace Livro\Traits;

use Livro\Control\Action;
use Livro\Database\Transaction;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Dialog\Question;

use Exception;

trait DeleteTrait
{
    /**
     * Pergunta sobre a exclusão de registro
     */
    function onDelete ( $param )
    {
        $id = $param['id'];                                     // obtém o parâmetro id
        $action1 = new Action ( array ( $this, 'delete' ) );    // cria ação
        $action1 -> setParameter ( 'id', $id );
        
        new Question ( 'Deseja realmente excluir o registro?', $action1 );
    }

    /**
     * Exclui um registro
     */
    function delete ( $param )
    {
        try {
            $id = $param['id'];                         // obtém a chave do registro
            Transaction::open ( $this -> connection );  // inicia transação com o BD
            
            $class = $this -> activeRecord;             // classe Active Record
            
            $object = $class::find ( $id );             // instancia objeto
            $object -> delete ( );                      // deleta objeto do banco de dados
            Transaction::close ( );                     // finaliza a transação
            $this -> onReload ( );                      // recarrega a datagrid
            new Message ( 'info', "Registro excluído com sucesso" );
        } catch ( Exception $e ) {
            new Message ( 'error', $e -> getMessage ( ) );
        }
    }
}
