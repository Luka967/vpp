<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Movie;
use Skop\Models\GenreModel;
use Skop\Models\MovieModel;

class ManagerMoviesController extends Controller
{
    public function showMovies()
    {
        if ($this->req->hasQueryKey('id'))
        {
            $obj = MovieModel::withId($this->req->query['id']);
            if ($obj == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
            $this->persistentFormData = (array)$obj;
            $this->persistentFormData['genres'] = $obj->genres();
        }
        $this->render('manage/movies.twig', [
            'movies' => MovieModel::all(),
            'genres' => GenreModel::all(),
            'movieObjectColumns' => Movie::$columnTraits
        ]);
    }

    private function fetchAllGenresByName(array $source)
    {
        $res = [];
        foreach ($source as $key => $genreName)
        {
            $fetchedGenre = GenreModel::withName($genreName);
            if ($fetchedGenre == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_GENRE, "Genres idx $key ('$genreName') could not be found");
            $res[] = $fetchedGenre;
        }
        return $res;
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
        foreach ($newMovie as $key => $_)
            $newMovie->$key = $this->req->data[$key];

        MovieModel::insertOne($newMovie);
        $newMovie = MovieModel::withTitle($newMovie->title);
        GenreModel::setForMovie($newMovie, $this->fetchAllGenresByName($this->req->data['genres']));

        $this->moveTrailerFile($newMovie->id);
        $this->movePosterFile($newMovie->id);

        $this->redirect('/manage/movies');
    }
    public function doUpdateMovie()
    {
        $existing = MovieModel::withId($this->req->data['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
        foreach ($existing as $key => $_)
            $existing->$key = $this->req->data[$key];

        MovieModel::updateOne($existing);
        GenreModel::setForMovie($existing, $this->fetchAllGenresByName($this->req->data['genres']));

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
        GenreModel::setForMovie($existing, []);
        MovieModel::deleteOne($existing->id);
        $this->redirect('/manage/movies');
    }
}
