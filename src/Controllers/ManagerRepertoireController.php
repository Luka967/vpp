<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Repertoire;
use Skop\Models\RepertoireModel;
use Skop\Models\ScreeningFeatureModel;
use Skop\Models\TicketModel;

class ManagerRepertoireController extends Controller
{
    public function showRepertoire()
    {
        $repertoire = RepertoireModel::tryGenerate(showNextCycle: true);

        $this->render('manage/repertoire.twig', [
            'repertoireEntryColumns' => Repertoire::$columnTraits,
            'screeningFeatures' => ScreeningFeatureModel::all(),
            'movies' => $repertoire['allMovies'],
            'theaters' => $repertoire['theaters'],
            'dayTimestamps' => $repertoire['dayTimestamps'],
            'dayEntries' => $repertoire['dayEntries'],
            'dayEntryGaps' => $repertoire['dayEntryGaps']
        ]);
    }

    public function showEditEntry()
    {
        $obj = RepertoireModel::withId($this->req->query['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_REPERTOIRE);

        $this->persistentFormData = (array)$obj;
        $this->persistentFormData['features'] = ScreeningFeatureModel::ofRepertoireEntry($obj);

        $this->showRepertoire();
    }

    private function fetchAllFeaturesByName(array $source)
    {
        $res = [];
        foreach ($source as $key => $featureName)
        {
            $fetched = ScreeningFeatureModel::withDescription($featureName);
            if ($fetched == null)
            {
                $this->logger->warning("$this->reqId had unknown feature name in request while inserting/updating", [
                    'name' => $featureName
                ]);
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_GENRE, "Feature idx $key ('$featureName') could not be found");
            }
            $res[] = $fetched;
        }
        return $res;
    }
    public function doInsertEntry()
    {
        $obj = new Repertoire();
        foreach ($obj::$columnTraits as $key => $_)
            $obj->$key = $this->req->data[$key];

        $features = $this->fetchAllFeaturesByName($this->req->data['features']);

        RepertoireModel::tryGenerate($obj, true);
        RepertoireModel::insertOne($obj);
        $obj = RepertoireModel::exact($obj->movie_id, $obj->theater_id, $obj->screening_start);
        ScreeningFeatureModel::setForRepertoireEntry($obj, $features);

        $this->logger->info("$this->reqId inserted new repertoire entry", [
            'object' => $obj,
            'features' => $features
        ]);

        $this->redirect('/manage/repertoire');
    }
    public function doUpdateEntry()
    {
        $obj = RepertoireModel::withId($this->req->data['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_REPERTOIRE);

        $features = $this->fetchAllFeaturesByName($this->req->data['features']);
        ScreeningFeatureModel::setForRepertoireEntry($obj, $features);

        $this->logger->info("$this->reqId updated repertoire entry", [
            'object' => $obj,
            'features' => $features
        ]);

        $this->redirect('/manage/repertoire');
    }

    public function doDeleteEntry()
    {
        $obj = RepertoireModel::withId($this->req->query['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_REPERTOIRE);

        TicketModel::deleteOfRepertoire($obj->id);
        ScreeningFeatureModel::setForRepertoireEntry($obj, []);
        RepertoireModel::deleteOne($obj->id);

        $this->logger->info("$this->reqId deleted repertoire entry", [
            'object' => $obj
        ]);

        $this->redirect('/manage/repertoire');
    }
}
