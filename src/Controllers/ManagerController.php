<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Genre;
use Skop\Models\GenreModel;

class ManagerController extends Controller
{
    public function showIndex()
    {
        $this->render('manage/index.twig');
    }

    public function showGenres()
    {
        if ($this->req->hasQueryKey('id'))
        {
            $obj = GenreModel::withId($this->req->query['id']);
            if ($obj == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_GENRE);
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
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_GENRE);
        GenreModel::deleteOne($existing->id);
        $this->redirect('/manage/genres');
    }
}
