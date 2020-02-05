<?php

namespace App\Controller;

use App\Entity\NasaImage;
use App\Service\NasaApiService;
use Doctrine\Common\Annotations\AnnotationReader;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class NasaApiController
 * @package App\Controller
 *
 * @Route("/api",name="api_")
 */
class NasaApiController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/get_photos", name="get_photos")
     *
     * @param Request $request
     * @param NasaApiService $apiService
     * @return JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getPhotos(Request $request, NasaApiService $apiService)
    {
        $rover = $request->get('rover');
        $date = $request->get('date');
        $camera = $request->get('camera');

        if ($date) {
            $date = \DateTime::createFromFormat('Y-m-d', $date);
        }

        $this->checkParams($date, $rover, $camera);

        $imageRepository = $this->getDoctrine()->getRepository(NasaImage::class);
        $images = $imageRepository->findByFields($rover, $camera, $date);

        $serializer = $this->getJsonSerializer();

        $response = $serializer->serialize($images, 'json', ['groups' => 'info']);

        //todo fix workaround
        return new JsonResponse(json_decode($response));
    }

    /**
     * @Rest\Get("/get_photos_details/{photo}", name="get_photos")
     *
     * @param Request $request
     * @param NasaApiService $apiService
     * @return JsonResponse
     */
    public function getPhotosDetails(Request $request, NasaApiService $apiService, $photo)
    {

        $imageRepository = $this->getDoctrine()->getRepository(NasaImage::class);
        $image = $imageRepository->find((int)$photo);

        if (!$image) {
            $image = $imageRepository->findAll();
        }

        $serializer = $this->getJsonSerializer();

        $response = $serializer->serialize($image, 'json', ['groups' => 'details']);

        //todo fix workaround
        return new JsonResponse(json_decode($response));
    }

    /**
     * @param \DateTime $date
     * @param $rover
     * @param $camera
     */
    public function checkParams($date, $rover, $camera): void
    {
        $errors = [];
        if ($date === false) {
            $errors[] = 'Please provide valida date, format: YYYY-MM-DD';
        }

        if ($rover && !in_array($rover, NasaApiService::ROVERS)) {

            $errors[] = 'Please provide valid rover, available: '
                . implode(',', NasaApiService::ROVERS);
        }
        if ($camera && !in_array($camera, NasaApiService::CAMERAS_ABBREVIATION)) {
                $errors[] = 'Please provide valid camera, available: '
                . implode(',', NasaApiService::CAMERAS_ABBREVIATION);
        }

        if (count($errors) > 0) {
            throw new BadRequestHttpException(
                implode("\n", $errors)
            );
        }
    }

    /**
     * @return Serializer
     */
    public function getJsonSerializer()
    {

        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d') : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'earthDate' => $dateCallback,
            ],
        ];


        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(new AnnotationReader())
        );
        $normalizer = new GetSetMethodNormalizer(
            $classMetadataFactory,
            new MetadataAwareNameConverter($classMetadataFactory),
            null,
            null,
            null,
            $defaultContext
        );

        $normalizers = [$normalizer];

        return new Serializer($normalizers, ['json' => new JsonEncoder()]);
    }
}