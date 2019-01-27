<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Reporting\ReadModel\DelegateCatering;
use Doctrine\Common\Collections\Criteria;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class ReportsController extends AppController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function cateringPreferencesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            $data[$record->getPreference()]++;
        }

        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator(\array_keys($data)));
        $iterator->attachIterator(new \ArrayIterator(\array_values($data)));

        $csv = $this->createCsvData($iterator);

        return $this->makeResponse($csv);
    }

    public function cateringAlergiesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));

        $data = [];

        foreach ($records as $record) {
            /** @var DelegateCatering $record */
            if (!empty($record->getAllergies())) {
                $data[] = [
                    'name' => $record->getName(),
                    'allergies' => $record->getAllergies(),
                ];
            }
        }

        $csv = $this->createCsvData($data);

        return $this->makeResponse($csv);
    }

    /**
     * Csv output function inspired by https://github.com/opencfp/opencfp/blob/master/src/Http/Controller/Admin/ExportsController.php
     * @param iterable $data
     * @return string
     */
    private function createCsvData(iterable $data): string
    {
        \ob_start();
        $f = \fopen('php://output', 'w');

        $escape = function (string $record): string {
            return \preg_replace('#^([=+\-@]{1})#','\'\1', $record);
        };

        foreach ($data as $row) {
            \fputcsv($f, \array_map($escape, $row));
        }

        \fclose($f);

        return \ob_get_clean();
    }

    /**
     * @param string $csv
     * @return \Zend\Stdlib\ResponseInterface
     */
    private function makeResponse(string $csv): \Zend\Stdlib\ResponseInterface
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            $response->setContent($csv);
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'text/csv');
            $headers->addHeaderLine('Content-Disposition', 'attachment; filename="export.csv"');
            $headers->addHeaderLine('Accept-Ranges', 'bytes');
            $headers->addHeaderLine('Content-Length', strlen($csv));
        }

        return $response;
    }
}