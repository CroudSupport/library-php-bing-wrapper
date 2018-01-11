<?php
namespace BingWrapper;

use BingWrapper\Reports\AdPerformanceReport;
use BingWrapper\Reports\KeywordPerformanceReport;
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
     * @param Client $client
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param \string[] ...$accountIds
     *
     * @param string $downloadDestination
     * @param array $excludedColumns
     * @return string
     */
    public function downloadKeywordPerformanceReportCsv(
        Client $client,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $accountIds,
        string $downloadDestination,
        array $excludedColumns = null
    ) {
        $columns = $this->getColumns(
            $excludedColumns,
            new KeywordPerformanceReportColumn()
        );

        $reportBuilder = new KeywordPerformanceReport(
            $startDate,
            $endDate,
            ReportAggregation::Daily,
            $accountIds,
            $columns
        );

        return $this->reportDownloaderService
            ->requestAndPollAndDownloadCsv($client, $reportBuilder, $downloadDestination);
    }

    /**
     * @param Client $client
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param \string[] ...$accountIds
     * @param string $downloadDestination
     * @param array $excludedColumns
     *
     * @return string
     */
    public function downloadAdPerformanceReportCsv(
        Client $client,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $accountIds,
        string $downloadDestination,
        array $excludedColumns
    ) {
        $columns = $this->getColumns(
            $excludedColumns,
            new AdPerformanceReportColumn()
        );

        $reportBuilder = new AdPerformanceReport(
            $startDate,
            $endDate,
            ReportAggregation::Daily,
            $accountIds,
            $columns
        );

        return $this->reportDownloaderService
            ->requestAndPollAndDownloadCsv($client, $reportBuilder, $downloadDestination);
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

        return array_values($columns);
    }
}
