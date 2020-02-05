<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NasaApiService
{

    const ROVERS = ['curiosity', 'opportunity', 'spirit'];
    const CAMERAS_ABBREVIATION = ['FHAZ', 'RHAZ'];
    const API_KEY = 'lPNp7MQXqig9RdaRCCW6a2XrJX1KkZqm3yBkiY3l';

    /**
     * @param string $rover
     * @param string $camera
     * @param \DateTimeInterface $date
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getDataFromApi(string $rover, string $camera, \DateTimeInterface $date)
    {
        $url = 'https://api.nasa.gov/mars-photos/api/v1/rovers/'
            . $rover . '/photos?api_key=' . self::API_KEY
            . '&camera=' . $camera . '&earth_date=' . $date->format('Y-m-d');

        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            $url
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Api internal error');
        }

        return $response;
    }
}