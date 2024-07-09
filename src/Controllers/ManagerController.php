<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Genre;
use Skop\Models\Domain\Movie;
use Skop\Models\GenreModel;
use Skop\Models\MovieModel;

class ManagerController extends Controller
{
    public function showIndex()
    {
        $this->render('manage/index.twig');
    }

    public function showMovies()
    {
        if ($this->req->hasQueryKey('id'))
        {
            $obj = MovieModel::withId($this->req->query['id']);
            if ($obj == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
            $this->persistentFormData = (array)$obj;
        }
        $this->render('manage/movies.twig', [
            'movies' => MovieModel::all(),
            'movieObjectColumns' => Movie::$columnTraits
        ]);
    }
    private function moveTrailerFile(int $id)
    {
        $status = move_uploaded_file(
            $this->req->files['trailer_file']['tmp_name'],
            SKOP_PUBLIC_PATH_FILESYSTEM . 'img/movie-trailer/' . $id . '.mp4'
        );
        if (!$status)
            throw new ErrorPageException(0, 'moveTrailerFile failed');
    }
    private function movePosterFile(int $id)
    {
        $status = move_uploaded_file(
            $this->req->files['poster_file']['tmp_name'],
            SKOP_PUBLIC_PATH_FILESYSTEM . 'img/movie-poster/' . $id . '.jpg'
        );
        if (!$status)
            throw new ErrorPageException(0, 'movePosterFile failed');
    }
    public function doInsertMovie()
    {
        $newMovie = new Movie();
        foreach ($this->req->data as $key => $value)
            $newMovie->$key = $value;

        MovieModel::insertOne($newMovie);
        $newMovie = MovieModel::withTitle($newMovie->title);
        $this->moveTrailerFile($newMovie->id);
        $this->movePosterFile($newMovie->id);

        $this->redirect('/manage/movies');
    }
    public function doUpdateMovie()
    {
        $existing = MovieModel::withId($this->req->data['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
        foreach ($this->req->data as $key => $value)
            $existing->$key = $value;
        MovieModel::updateOne($existing);

        if ($this->req->hasFileUploaded('trailer_file'))
            $this->moveTrailerFile($existing->id);
        if ($this->req->hasFileUploaded('poster_file'))
            $this->movePosterFile($existing->id);

        $this->redirect('/manage/movies');
    }
    public function doDeleteMovie()
    {
        $existing = MovieModel::withId($this->req->query['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
        MovieModel::deleteOne($existing->id);
        $this->redirect('/manage/movies');
    }

    public function showGenres()
    {
        if ($this->req->hasQueryKey('id'))
        {
            $obj = GenreModel::withId($this->req->query['id']);
            if ($obj == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
            $this->persistentFormData = (array)$obj;
        }
        $this->render('manage/genres.twig', [
            'genres' => GenreModel::all(),
            'genreObjectColumns' => Genre::$columnTraits
        ]);
    }
    public function doInsertGenre()
    {
        $newGenre = new Genre();
        foreach ($this->req->data as $key => $value)
            $newGenre->$key = $value;
        GenreModel::insertOne($newGenre);
        $this->redirect('/manage/genres');
    }
    public function doDeleteGenre()
    {
        $existing = GenreModel::withId($this->req->query['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
        GenreModel::deleteOne($existing->id);
        $this->redirect('/manage/genres');
    }
}
