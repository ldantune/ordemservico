<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoModel extends Model
{

    protected $table            = 'eventos';
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'conta_id',
        'ordem_id',
        'title',
        'start',
        'end',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function recuperaEventos(array $dataGet){
        return $this
                    ->where('start >', $dataGet['start'])
                    ->where('end <', $dataGet['end'])
                    ->findAll();
    }

    public function cadastraEvento(string $coluna, string $titulo, int $id, int $dias){

        $evento = [
            "$coluna" => $id,
            "title" => $titulo,
            "start" => date("Y-m-d", strtotime("+$dias days", time())),
            "end" => date("Y-m-d", strtotime("+$dias days", time())),
        ];

        return $this->insert($evento);
    }
    

    public function atualizaEvento(string $coluna, int $id, int $dias){
        return $this->protect(false)
                    ->where($coluna, $id)
                    ->set('start', date("Y-m-d", strtotime("+$dias days", time())))
                    ->set('end', date("Y-m-d", strtotime("+$dias days", time())))
                    ->update();
    }
    
}
