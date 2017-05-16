<?php
namespace BingDeCrapperWrapper\Reports;

use DateTimeInterface;
use Microsoft\BingAds\V11\Reporting\AccountReportScope;
use Microsoft\BingAds\V11\Reporting\Date;
use Microsoft\BingAds\V11\Reporting\ReportFormat;
use Microsoft\BingAds\V11\Reporting\ReportRequest;
use Microsoft\BingAds\V11\Reporting\ReportTime;
use ReflectionObject;
use RuntimeException;

abstract class ReportRequestBuilder implements ReportRequestBuilderContract
{
    /**
     * @param ReportRequest $report
     * @return ReportRequest
     */
    protected function setBaseFields(ReportRequest $report)
    {
        $report->Format = ReportFormat::Csv;
        $report->ReportName = uniqid('my_report_');
        $report->ReturnOnlyCompleteData = false;
        $report->ExcludeReportFooter = true;
        $report->ExcludeReportHeader = true;

        return $report;
    }

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param ReportRequest $report
     *
     * @return ReportRequest
     */
    protected function setDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate, ReportRequest $report)
    {
        if ( ! $this->checkClassObjectHasProperty($report, 'Time')) {
            throw new RuntimeException('$report does not support date range');
        }

        $report->Time = new ReportTime();

        $report->Time->CustomDateRangeStart = new Date();
        $report->Time->CustomDateRangeStart->Day = $startDate->format('d');
        $report->Time->CustomDateRangeStart->Month = $startDate->format('m');
        $report->Time->CustomDateRangeStart->Year = $startDate->format('Y');

        $report->Time->CustomDateRangeEnd = new Date();
        $report->Time->CustomDateRangeEnd->Day = $endDate->format('d');
        $report->Time->CustomDateRangeEnd->Month = $endDate->format('m');
        $report->Time->CustomDateRangeEnd->Year = $endDate->format('Y');

        return $report;
    }

    /**
     * @param array $columns
     * @param $report
     *
     * @return ReportRequest
     */
    protected function setReportColumns(array $columns, ReportRequest $report)
    {
        if ( ! $this->checkClassObjectHasProperty($report, 'Columns')) {
            throw new RuntimeException('$report does not support the setting of Columns');
        }

        $report->Columns = $columns;

        return $report;
    }

    /**
     * @param array $accountIds
     * @param $report
     *
     * @return ReportRequest
     */
    protected function setAccountIds(array $accountIds, ReportRequest $report)
    {
        if ( ! $this->checkClassObjectHasProperty($report, 'Scope')) {
            throw new RuntimeException('$report does not support the setting of AccountIds');
        }

        $report->Scope = new AccountReportScope();
        $report->Scope->AccountIds = $accountIds;

        return $report;
    }

    /**
     * @param string $aggregation
     * @param $report
     *
     * @return ReportRequest
     */
    protected function setAggregation(string $aggregation, ReportRequest $report)
    {
        if ( ! $this->checkClassObjectHasProperty($report, 'Aggregation')) {
            throw new RuntimeException('$report does not support the setting of a Aggregation');
        }

        $report->Aggregation = $aggregation;

        return $report;
    }

    protected function checkClassObjectHasProperty($object, string $property)
    {
        $reflectionObject = new ReflectionObject($object);

        return $reflectionObject->hasProperty($property);
    }

    /**
     * @return ReportRequest
     */
    abstract public function getReport(): ReportRequest;
}