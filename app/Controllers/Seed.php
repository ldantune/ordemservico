<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Throwable;

class Seed extends Controller
{
    public function index()
    {
        $seeder = \Config\Database::seeder();
       

        try {
            $seeder->call('HospedarSeeder');

            echo 'Dados iniciados criados com sucesso!';
        } catch (Throwable $e) {
          echo $e->getMessage();
            // Do something with the error here...
        }
    }
}