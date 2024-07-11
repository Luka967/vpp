<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Genre;
use Skop\Models\Domain\ScreeningFeature;
use Skop\Models\Domain\TheaterSeatType;
use Skop\Models\GenreModel;
use Skop\Models\ScreeningFeatureModel;
use Skop\Models\TheaterSeatTypeModel;

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
        if (GenreModel::withName($this->req->data['name']) != null)
            throw new ErrorPageException(SKOP_ERROR_CONFLICTING_GENRE);

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

    public function showScreeningFeatures()
    {
        if ($this->req->hasQueryKey('id'))
        {
            $obj = ScreeningFeatureModel::withId($this->req->query['id']);
            if ($obj == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_SCREENINGFEATURE);

            $this->persistentFormData = (array)$obj;
        }
        $this->render('manage/screeningFeatures.twig', [
            'screeningFeatures' => ScreeningFeatureModel::all(),
            'screeningFeatureColumns' => ScreeningFeature::$columnTraits
        ]);
    }
    public function doInsertScreeningFeature()
    {
        if (ScreeningFeatureModel::withDescription($this->req->data['description']) != null)
            throw new ErrorPageException(SKOP_ERROR_CONFLICTING_SCREENINGFEATURE);

        $obj = new ScreeningFeature();
        foreach ($this->req->data as $key => $value)
            $obj->$key = $value;

        ScreeningFeatureModel::insertOne($obj);

        $this->redirect('/manage/repertoire/features');
    }
    public function doDeleteScreeningFeature()
    {
        $existing = ScreeningFeatureModel::withId($this->req->query['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_SCREENINGFEATURE);

        ScreeningFeatureModel::deleteOne($existing->id);

        $this->redirect('/manage/repertoire/features');
    }

    public function showTheaterSeatTypes()
    {
        if ($this->req->hasQueryKey('id'))
        {
            $obj = TheaterSeatTypeModel::withId($this->req->query['id']);
            if ($obj == null)
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_SEATTYPE);

            $this->persistentFormData = (array)$obj;
        }
        $this->render('manage/theaterSeatTypes.twig', [
            'theaterSeatTypes' => TheaterSeatTypeModel::all(),
            'theaterSeatTypeColumns' => TheaterSeatType::$columnTraits
        ]);
    }
    public function doInsertTheaterSeatType()
    {
        if (TheaterSeatTypeModel::withName($this->req->data['description']) != null)
            throw new ErrorPageException(SKOP_ERROR_CONFLICTING_SEATTYPE);

        $obj = new TheaterSeatType();
        foreach ($this->req->data as $key => $value)
            $obj->$key = $value;

        TheaterSeatTypeModel::insertOne($obj);

        $this->redirect('/manage/theaters/seats');
    }
    public function doUpdateTheaterSeatType()
    {
        $existing = TheaterSeatTypeModel::withId($this->req->query['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_SEATTYPE);

        foreach ($this->req->data as $key => $value)
            $existing->$key = $value;

        TheaterSeatTypeModel::updateOne($existing);

        $this->redirect('/manage/theaters/seats');
    }
    public function doDeleteTheaterSeatType()
    {
        $existing = TheaterSeatTypeModel::withId($this->req->query['id']);
        if ($existing == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_SEATTYPE);

        TheaterSeatTypeModel::deleteOne($existing->id);

        $this->redirect('/manage/theaters/seats');
    }
}
