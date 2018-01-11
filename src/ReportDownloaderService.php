<?php
namespace BingWrapper;

use BingWrapper\Reports\ReportRequestBuilder;
use Carbon\Carbon;
use Exception;
use Microsoft\BingAds\V11\Reporting\PollGenerateReportRequest;
use Microsoft\BingAds\V11\Reporting\ReportRequest;
use Microsoft\BingAds\V11\Reporting\ReportRequestStatusType;
use Microsoft\BingAds\V11\Reporting\SubmitGenerateReportRequest;
use ReflectionObject;
use SoapVar;
use ZipArchive;

class ReportDownloaderService
{
    const WAIT_TIME = 30;
    const RETRY_ATTEMPTS = 10;

    /**
     * @param Client $client
     * @param ReportRequestBuilder $reportRequestBuilder
     * @param string $downloadDestination
     * @return string
     */
    public function requestAndPollAndDownloadCsv(
        Client $client,
        ReportRequestBuilder $reportRequestBuilder,
        string $downloadDestination
    ) {
        $reportRequestId = $this->makeReportRequest($client, $reportRequestBuilder->getReport());

        $reportUrl = $this->poll($client, $reportRequestId);

        return $this->downloadReport($reportUrl, $downloadDestination, $reportRequestId);
    }

    /**
     * @param $client
     * @param ReportRequest $reportRequest
     *
     * @return string
     */
    public function makeReportRequest(Client $client, ReportRequest $reportRequest) {
        $encodedReport = new SoapVar(
            $reportRequest,
            SOAP_ENC_OBJECT,
            (new ReflectionObject($reportRequest))->getShortName(),
            $client->getClient()->GetNamespace()
        );

        $request = new SubmitGenerateReportRequest();
        $request->ReportRequest = $encodedReport;

        $response = $client->getClient()->GetService()->SubmitGenerateReport($request);

        return $response->ReportRequestId;
    }

    /**
     * @param $url
     * @param $downloadDestination
     * @return string
     * @throws Exception
     */
    public function downloadReport($url, $downloadDestination, $reportRequestId)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'bing_report');

        copy($url, $tmpFile);

        $zipArchive = new ZipArchive();

        $wasOpened = $zipArchive->open($tmpFile);

        if ($wasOpened !== true) {
            throw new Exception('Cannot open zip');
        }

        $wasExtracted = $zipArchive->extractTo($downloadDestination);

        if ($wasExtracted !== true) {
            throw new Exception('Cannot extract zip');
        }

        $zipArchive->close();
        unlink($tmpFile);

        $csvPath = $downloadDestination . '/' . Carbon::now()->format('Y-m-d') . '.csv';

        rename(
            $downloadDestination . '/' . $reportRequestId . '.csv',
            $csvPath
        );

        return $csvPath;
    }

    /**
     * @param $reportRequestId
     * @param Client $client
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function poll(Client $client, $reportRequestId)
    {
        $status = null;

        $request = new PollGenerateReportRequest();
        $request->ReportRequestId = $reportRequestId;

        for ($i = 0; $i < self::RETRY_ATTEMPTS; $i++) {

            sleep(self::WAIT_TIME);

            $report = $client->getClient()
                ->GetService()
                ->PollGenerateReport($request)
                ->ReportRequestStatus;

            $status = $report->Status;

            if ($status === ReportRequestStatusType::Error) {
                throw new Exception('Report download error');
            }

            if ($status === ReportRequestStatusType::Success) {
                if (!$report->ReportDownloadUrl) {
                    throw new Exception('Report Is Empty');
                }

                return $report->ReportDownloadUrl;
            }
        }

        throw new Exception('Request has Timed Out');
    }
}
