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
        $query = Movie::latest();

        if ($search) {
            $query->where('judul', 'like', "%$search%")
                ->orWhere('sinopsis', 'like', "%$search%");
        }

        return $query->paginate(6)->appends(['search' => $search]);
    }

    public function findById($id)
    {
        return Movie::findOrFail($id);
    }

    public function store($request)
    {
        $fileName = Str::uuid() . '.jpg';

        $request->file('foto_sampul')->move(public_path('images'), $fileName);

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

        if ($request->hasFile('foto_sampul')) {
            $fileName = Str::uuid() . '.' . $request->file('foto_sampul')->getClientOriginalExtension();

            $request->file('foto_sampul')->move(public_path('images'), $fileName);

            if (File::exists(public_path('images/' . $movie->foto_sampul))) {
                File::delete(public_path('images/' . $movie->foto_sampul));
            }

            $movie->update(array_merge($request->all(), ['foto_sampul' => $fileName]));
        } else {
            $movie->update($request->all());
        }

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
}