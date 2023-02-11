<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Ordem extends Entity
{
    protected $dates   = [
        'criado_em', 
        'atualizado_em', 
        'deletado_em'
    ];

    public function exibeSituacao()
    {

        if ($this->deletado_em != null) {

            if(url_is('relatorios*')){
                return '<span class="text-white">Excluída</span>';
            }

            $icone = '<span class="text-white">Excluído</span> <i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("ordens/desfazerexclusao/$this->codigo", $icone, ['class' => 'btn btn-outline-success btn-sm']);

            return $situacao;
        }else{

            if($this->situacao === 'aberta'){

                return '<span class="text-warning"><i class="fa fa-unlock"></i>&nbsp;'. ucfirst(($this->situacao)).'</span>';
            }

            if($this->situacao === 'encerrada'){

                return '<span class="text-white"><i class="fa fa-lock"></i>&nbsp;'. ucfirst(($this->situacao)).'</span>';
            }

            if($this->situacao === 'aguardando'){

                return '<span class="text-warning"><i class="fa fa-clock-o"></i>&nbsp;'. ucfirst(($this->situacao)).'</span>';
            }

            if($this->situacao === 'nao_pago'){

                return '<span class="text-warning"><i class="fa fa-clock-o"></i>&nbsp;Não pago</span>';
            }

            if($this->situacao === 'cancelada'){

                return '<span class="text-danger"><i class="fa fa-ban"></i>&nbsp;'. ucfirst(($this->situacao)).'</span>';
            }
        }
    }

    public function defineDataVencimentoEvento(string $expire_at) : int {

        $dataAtualCovertida = $this->mutateDate(date('Y-m-d'));

        $dataCalculo = ($expire_at ? $expire_at : $this->data_vencimento);

        return $dataAtualCovertida->difference($dataCalculo)->getDays();
    }

    public function isImage(string $evidencia): bool{

        $info = new \SplFileInfo($evidencia);

        return ($info->getExtension() != 'pdf' ? true : false);
    }

    public function formataTextoHistorico(){
        $textoHistorico = '<ul>';

        foreach($this->historico as $evento){
            $textoHistorico .= '<li>Evento: ' .$evento['message']. '<br>Data: ' . date('d/m/y H:i:s', strtotime($evento['created_at'])) . '</li>';
        }
        
        $textoHistorico .= '</ul>';

        return $textoHistorico;
    }
}
