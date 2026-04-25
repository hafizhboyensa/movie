<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Interfaces\MovieRepositoryInterface;

class MovieService
{
    protected $movieRepo;

    public function __construct(MovieRepositoryInterface $movieRepo)
    {
        $this->movieRepo = $movieRepo;
    }

    public function getAllMovies($search = null)
    {
        return $this->movieRepo->getAll($search);
    }

    public function getMovieById($id)
    {
        return $this->movieRepo->findById($id);
    }

    public function createMovie($request)
    {
        return $this->movieRepo->store($request);
    }

    public function updateMovie($request, $id)
    {
        return $this->movieRepo->update($request, $id);
    }

    public function deleteMovie($id)
    {
        return $this->movieRepo->delete($id);
    }
}