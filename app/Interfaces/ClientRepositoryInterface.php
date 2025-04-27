<?php

// app/Interfaces/ClientRepositoryInterface.php
namespace App\Interfaces;

interface ClientRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 10);
    public function search($query);
}
