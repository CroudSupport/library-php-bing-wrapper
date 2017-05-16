<?php
namespace BingDeCrapperWrapper;

use BingDeCrapperWrapper\Reports\AdPerformanceReport;
use BingDeCrapperWrapper\Reports\KeywordPerformanceReport;
use DateTimeInterface;
use Microsoft\BingAds\Auth\ServiceClientType;
use Microsoft\BingAds\V11\Reporting\AdPerformanceReportColumn;
use Microsoft\BingAds\V11\Reporting\KeywordPerformanceReportColumn;
use Microsoft\BingAds\V11\Reporting\ReportAggregation;
use ReflectionObject;
use RuntimeException;

class ReportHelper
{
    /**
     * @var ReportDownloaderService
     */
    private $reportDownloaderService;

    /**
     * @param ReportDownloaderService $reportDownloaderService
     */
    public function __construct(ReportDownloaderService $reportDownloaderService)
    {
        $this->reportDownloaderService = $reportDownloaderService;
    }

    /**
     * @param string $userName
     * @param string $password
     * @param string $developerToken
     * @return Client
     */
    public function getReportingClient(string $userName, string $password, string $developerToken)
    {
        return new Client(
            $userName,
            $password,
            $developerToken,
            ServiceClientType::ReportingVersion11
        );
    }

    /**
     * @param Client $client
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param array $excludedColumns
     * @param \string[] ...$accountIds
     *
     * @return string
     */
    public function getKeywordPerformanceReportCsvString(
        Client $client,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $accountIds,
        array $excludedColumns = null
    ) {
        $columns = $this->getColumns(
            $excludedColumns,
            new KeywordPerformanceReportColumn()
        );

        $reportBuilder = new KeywordPerformanceReport(
            $startDate,
            $endDate,
            ReportAggregation::Summary,
            $accountIds,
            $columns
        );

        return $this->reportDownloaderService->getReportCsvString($client, $reportBuilder);
    }

    /**
     * @param Client $client
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param array $excludedColumns
     * @param \string[] ...$accountIds
     *
     * @return string
     */
    public function getAdPerformanceReportCsvString(
        Client $client,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $accountIds,
        array $excludedColumns
    ) {
        $columns = $this->getColumns(
            $excludedColumns,
            new AdPerformanceReportColumn()
        );

        $reportBuilder = new AdPerformanceReport(
            $startDate,
            $endDate,
            ReportAggregation::Summary,
            $accountIds,
            $columns
        );

        return $this->reportDownloaderService->getReportCsvString($client, $reportBuilder);
    }

    /**
     * @param array $excludedColumns
     * @param $object
     *
     * @return array
     */
    private function getColumns(array $excludedColumns = null, $object)
    {
        $reflectionObject = new ReflectionObject($object);

        $columns = $reflectionObject->getConstants();

        if ($excludedColumns) {
            foreach ($excludedColumns as $excludedColumn) {
                if (($key = array_search($excludedColumn, $columns)) !== false) {
                    unset($columns[$key]);
                } else {
                    throw new RuntimeException(sprintf(
                        'Unable to exclude "%s" as it was not found in %s',
                        $excludedColumn,
                        $reflectionObject->getName()
                    ));
                }
            }
        }

        return $columns;
    }
}