<?php

class WebPageTestResponseHandler
{
    private $database;
    private $data;

    public function __construct()
    {
        $this->database = new Database(Config::MYSQL_HOST, Config::MYSQL_DATABASE, Config::MYSQL_USERNAME,
            Config::MYSQL_PASSWORD);
    }

    public function handle($response)
    {
        $this->data = $response;
        $this->saveDataToDb($this->data);
    }

    public function saveDataToDb($data)
    {
        $userId = '';
        $dataArray = $this->database->executeQuery("SELECT id FROM " . DatabaseTable::USER);
        if (array_key_exists(0, $dataArray) && array_key_exists('id', $dataArray[0]))
        {
            $userId = $dataArray[0]['id'];
        }
        $siteUrl = $this->getTestedUrl($data);
        $this->database->executeQuery("INSERT INTO " . DatabaseTable::USER_URLS . "(user_id, site_url) VALUES (?, ?)",
            [$userId, $siteUrl]);
        $testInfoArray = $this->generateTestDataArray($data);
        $wptTestId = $this->getArrayKey($testInfoArray, 'id');
        $location = $this->getArrayKey($testInfoArray, 'location');
        $fromPlace = $this->getArrayKey($testInfoArray, 'from');
        $completedTime = $this->getArrayKey($testInfoArray, 'completed');
        $this->database->executeQuery("INSERT INTO " . DatabaseTable::TEST_INFO .
            "(user_id, test_url, test_id, location, from_place, completed_time)
                                      VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $siteUrl, $wptTestId, $location, $fromPlace, $completedTime]);
        $testId = $this->database->executeQuery("SELECT id FROM " . DatabaseTable::TEST_INFO . " WHERE id=1");
        $jsonData = json_encode($data);
        $this->database->executeQuery("INSERT INTO " . DatabaseTable::RAW_DATA . "(test_id, json_data)
                                      VALUES (?, ?)", [1, $jsonData]);
        $testResultArray = $this->generateResultDataArray($data['average']['firstView']);
        $loadTime = $this->getArrayKey($testResultArray, 'loadTime');
        $ttfb = $this->getArrayKey($testResultArray, 'TTFB');
        $bytesOut = $this->getArrayKey($testResultArray, 'bytesOut');
        $bytesOutDoc = $this->getArrayKey($testResultArray, 'bytesOutDoc');
        $bytesIn = $this->getArrayKey($testResultArray, 'bytesIn');
        $bytesInDoc = $this->getArrayKey($testResultArray, 'bytesInDoc');
        $connections = $this->getArrayKey($testResultArray, 'connections');
        $requests = $this->getArrayKey($testResultArray, 'requests');
        $requestsDoc = $this->getArrayKey($testResultArray, 'requestsDoc');
        $responses200 = $this->getArrayKey($testResultArray, 'responses_200');
        $responses404 = $this->getArrayKey($testResultArray, 'responses_404');
        $renderTime = $this->getArrayKey($testResultArray, 'render');
        $fullyLoaded = $this->getArrayKey($testResultArray, 'fullyLoaded');
        $docTime = $this->getArrayKey($testResultArray, 'docTime');
        $imageTotal = $this->getArrayKey($testResultArray, 'image_total');
        $domElements = $this->getArrayKey($testResultArray, 'domElements');
        $titleTime = $this->getArrayKey($testResultArray, 'titleTime');
        $loadEventStart = $this->getArrayKey($testResultArray, 'loadEventStart');
        $loadEventEnd = $this->getArrayKey($testResultArray, 'loadEventEnd');
        $domContentLoadedEventStart = $this->getArrayKey($testResultArray, 'domContentLoadedEventStart');
        $domContentLoadedEventEnd = $this->getArrayKey($testResultArray, 'domContentLoadedEventEnd');
        $firstPaint = $this->getArrayKey($testResultArray, 'firstPaint');
        $domInteractive = $this->getArrayKey($testResultArray, 'domInteractive');
        $domLoading = $this->getArrayKey($testResultArray, 'domLoading');
        $visualComplete = $this->getArrayKey($testResultArray, 'visualComplete');
        $speedIndex = $this->getArrayKey($testResultArray, 'SpeedIndex');
        $this->database->executeQuery("INSERT INTO " . DatabaseTable::AVERAGE_RESULT .
            "(test_id, type_view, load_time, ttfb, bytes_out, bytes_out_doc, bytes_in,
                                        bytes_in_doc, connections, requests, requests_doc, responses_200,
                                        responses_404, render_time, fully_loaded, doc_time, image_total,
                                        dom_elements, title_time, load_event_start, load_event_end,
                                        dom_content_loaded_event_start, dom_content_loaded_event_end, first_paint,
                                        dom_interactive, dom_loading, visual_complete, speed_index)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                                        ?, ?, ?, ?)", [
            1,
            1,
            $loadTime,
            $ttfb,
            $bytesOut,
            $bytesOutDoc,
            $bytesIn,
            $bytesInDoc,
            $connections,
            $requests,
            $requestsDoc,
            $responses200,
            $responses404,
            $renderTime,
            $fullyLoaded,
            $docTime,
            $imageTotal,
            $domElements,
            $titleTime,
            $loadEventStart,
            $loadEventEnd,
            $domContentLoadedEventStart,
            $domContentLoadedEventEnd,
            $firstPaint,
            $domInteractive,
            $domLoading,
            $visualComplete,
            $speedIndex
        ]);
    }

    private function getArrayKey($array, $key)
    {
        if (array_key_exists($key, $array))
        {
            $key = $array[$key];
        }
        else
        {
            $key = '';
        }

        return $key;
    }

    private function getTestedUrl($data)
    {
        $url = '';
        if (array_key_exists('url', $data))
        {
            $url = $data['url'];
        }

        return $url;
    }

    private function generateTestDataArray($data)
    {
        $keys = ['id', 'location', 'from', 'completed'];
        $dataArray = [];
        foreach ($keys as $key)
        {
            if (array_key_exists($key, $data))
            {
                $dataArray[$key] = $data[$key];
            }
        }

        return $dataArray;
    }

    private function generateResultDataArray($data)
    {
        $keys = [
            'loadTime',
            'TTFB',
            'bytesOut',
            'bytesOutDoc',
            'bytesIn',
            'bytesInDoc',
            'connections',
            'requests',
            'requestsDoc',
            'responses_200',
            'responses_404',
            'render',
            'fullyLoaded',
            'docTime',
            'image_total',
            'domElements',
            'titleTime',
            'loadEventStart',
            'loadEventEnd',
            'domContentLoadedEventStart',
            'domContentLoadedEventEnd',
            'firstPaint',
            'domInteractive',
            'domLoading',
            'visualComplete',
            'SpeedIndex'
        ];
        $dataArray = [];
        foreach ($keys as $key)
        {
            if (array_key_exists($key, $data))
            {
                $dataArray[$key] = $data[$key];
            }
        }

        return $dataArray;
    }
}