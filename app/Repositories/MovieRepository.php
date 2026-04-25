<?php

namespace App\Repositories;

use App\Models\Movie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Interfaces\MovieRepositoryInterface;

class MovieRepository implements MovieRepositoryInterface
{
    public function getAll($search)
    {
        $movieQuery = Movie::latest();

        if ($search) {
            $movieQuery->where(function ($query) use ($search) {
                $query->where('judul', 'like', "%$search%")
                      ->orWhere('sinopsis', 'like', "%$search%");
            });
        }

        return $movieQuery->paginate(6)->appends(['search' => $search]);
    }

    public function findById($id)
    {
        return Movie::findOrFail($id);
    }

    public function store($request)
    {
        $fileName = null;

        if ($request->hasFile('foto_sampul')) {
            $fileName = $this->uploadImage($request->file('foto_sampul'));
        }

        return Movie::create([
            'id' => $request->id,
            'judul' => $request->judul,
            'category_id' => $request->category_id,
            'sinopsis' => $request->sinopsis,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
            'foto_sampul' => $fileName,
        ]);
    }

    public function update($request, $id)
    {
        $movie = Movie::findOrFail($id);

        $data = [
            'judul' => $request->judul,
            'category_id' => $request->category_id,
            'sinopsis' => $request->sinopsis,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
        ];

        if ($request->hasFile('foto_sampul')) {
            $fileName = $this->uploadImage($request->file('foto_sampul'));

            if (File::exists(public_path('images/' . $movie->foto_sampul))) {
                File::delete(public_path('images/' . $movie->foto_sampul));
            }

            $data['foto_sampul'] = $fileName;
        }

        $movie->update($data);

        return $movie;
    }

    public function delete($id)
    {
        $movie = Movie::findOrFail($id);

        if (File::exists(public_path('images/' . $movie->foto_sampul))) {
            File::delete(public_path('images/' . $movie->foto_sampul));
        }

        return $movie->delete();
    }

    private function uploadImage($file)
    {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $fileName);

        return $fileName;
    }
}