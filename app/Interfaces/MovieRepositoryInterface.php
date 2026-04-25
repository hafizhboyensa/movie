<?php

namespace App\Interfaces;

interface MovieRepositoryInterface
{
    public function getAll($search);
    public function findById($id);
    public function store($request);
    public function update($request, $id);
    public function delete($id);
}