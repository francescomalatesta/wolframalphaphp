<?php

namespace WolframAlpha;

class Engine {

    private $queryEndpointUrl = 'http://api.wolframalpha.com/v2/query';
    private $validateQueryEndpointUrl = 'http://api.wolframalpha.com/v2/validatequery';

    private $appId;

    function __construct($appId)
    {
        $this->appId = $appId;
    }

    public function process($query, $assumptions = array(), $formats = array('image', 'plaintext'))
    {
        $requestUrl = $this->buildRequestUrl('query', $query, $assumptions, $formats);
        $rawXml = $this->getQueryXml($requestUrl);
        return new QueryResult($rawXml);
    }

    public function validate($query, $assumptions = array())
    {
        $requestUrl = $this->buildRequestUrl('validate', $query, $assumptions, array('image', 'plaintext'));
        $rawXml = $this->getQueryXml($requestUrl);
        return new ValidateQueryResult($rawXml);
    }

    private function buildRequestUrl($type, $query, $assumptions, $format)
    {
        $endpointString = ($type == 'query') ? $this->queryEndpointUrl : $this->validateQueryEndpointUrl;

        $formatString = '';
        if($type == 'query')
        {
            $formatString .= '&format=' . implode(',', $format);
        }

        $assumptionsQueryString = '';
        if(count($assumptions) > 0)
        {
            foreach ($assumptions as &$assumption){
                $assumption = 'assumption=' . $assumption;
            }

            $assumptionsQueryString = '&'. implode('&', $assumptions);
        }

        return $endpointString . '?appid=' . $this->appId . '&input=' . urlencode($query) . $formatString . $assumptionsQueryString;
    }

    private function getQueryXml($requestUrl)
    {
        return file_get_contents($requestUrl);
    }

}