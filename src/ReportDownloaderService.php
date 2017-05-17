<?php
namespace BingDeCrapperWrapper;

use BingDeCrapperWrapper\Reports\ReportRequestBuilder;
use Exception;
use League\Csv\Reader;
use Microsoft\BingAds\V11\Reporting\PollGenerateReportRequest;
use Microsoft\BingAds\V11\Reporting\ReportRequest;
use Microsoft\BingAds\V11\Reporting\ReportRequestStatusType;
use Microsoft\BingAds\V11\Reporting\SubmitGenerateReportRequest;
use SoapVar;
use ZipArchive;

class ReportDownloaderService
{
    const WAIT_TIME = 30;
    const RETRY_ATTEMPTS = 10;

    /**
     * @param Client $client
     * @param ReportRequestBuilder $reportRequestBuilder
     * @return string
     */
    public function getReportCsvString(
        Client $client,
        ReportRequestBuilder $reportRequestBuilder
    ) {
        $reportRequestId = $this->makeReportRequest($client, $reportRequestBuilder->getReport());

        $reportUrl = $this->poll($client, $reportRequestId);

        return $this->downloadReport($reportUrl);
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
            (new \ReflectionObject($reportRequest))->getName(),
            $client->getClient()->GetNamespace()
        );

        $request = new SubmitGenerateReportRequest();
        $request->ReportRequest = $encodedReport;

        $response = $client->getClient()->GetService()->SubmitGenerateReport($request);

        return $response->ReportRequestId;
    }

    /**
     * @param $url
     * @return string
     * @throws Exception
     */
    public function downloadReport($url)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'bing_report');
        copy($url, $tmpFile);

        $zipArchive = new ZipArchive();

        if($zipArchive->open($tmpFile) !== true) {
            throw new Exception('Cannot open zip');
        }

        $reportContents = $zipArchive->getFromIndex(0);

        $zipArchive->close();
        unlink($tmpFile);

        return $reportContents;
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