<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Doctrine\Common\Collections\Criteria;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class ReportsController extends AppController
{
    public function cateringPreferencesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            $data[$record->getPreference()]++;
        }

        $iterator = new class(count($data)) extends \MultipleIterator implements \Countable {
            private $count;

            public function __construct($count)
            {
                $this->count = $count;
            }

            public function count()
            {
                return $this->count;
            }
        };
        $iterator->attachIterator(new \ArrayIterator(\array_keys($data)));
        $iterator->attachIterator(new \ArrayIterator(\array_values($data)));

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($iterator);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Catering Report', 'report' => $iterator]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;


    }

    public function cateringAlergiesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            if (!empty($record->getAllergies())) {
                $data[] = [
                    'name' => $record->getName(),
                    'allergies' => $record->getAllergies(),
                ];
            }
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Catering allergies Report', 'report' => $data]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
    }

    public function delegatesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));
        $tickets = $this->getTickets();

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            $row = [
                'name' => $record->getName(),
                'company' => $record->getCompany(),
                'type' => $record->getDelegateType(),
                'email' => $record->getContactEmail(),
            ];

            $row['tickets'] = implode('; ', \array_map(function ($ticketId) use ($tickets) {return $tickets[$ticketId]->getDescriptor()->getName();}, $record->getTickets()));
            $data[] = $row;
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Delegates Report', 'report' => $data, 'header' => ['Name', 'Company', 'Delegate type', 'Email', 'Tickets']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
    }

    public function checkedInDelegatesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('checkedIn', true)));
        $tickets = $this->getTickets();

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            $email = $record->getEmail();
            if (empty($email)) {
                $email = $record->getPurchaserEmail();
            }

            $row = [
                'name' => $record->getName(),
                'company' => $record->getCompany(),
                'type' => $record->getDelegateType(),
                'email' => $email,
            ];

            $row['tickets'] = implode('; ', \array_map(function ($ticketId) use ($tickets) {return $tickets[$ticketId]->getDescriptor()->getName();}, $record->getTickets()));
            $data[] = $row;
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Checked in Delegates Report', 'report' => $data, 'header' => ['Name', 'Company', 'Delegate type', 'Email', 'Tickets']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
    }

    public function purchasesAction()
    {
        $records = $this->repository(Purchase::class)->matching(Criteria::create());

        $data = [];

        foreach ($records as $record) {
            /** @var Purchase $record */
            $row = [
                'email' => $record->getEmail(),
                'code' => $record->getDiscountCode(),
                'delegates' => $record->getMaxDelegates(),
                'paid' => $record->isPaid() ? 'Yes' : 'No',
            ];

            $data[] = $row;
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Purchases Report', 'report' => $data, 'header' => ['Email', 'Discount code', 'Delegates', 'Paid']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
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