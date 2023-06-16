<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use App\Entity\Offer;
use App\Service\OfferService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/offer', name: 'api_offer_')]
class OfferController extends AbstractController
{
    private OfferService $offerService;
    private JsonValidator $validator;
    private SerializerInterface $serializer;

    public function __construct(
        OfferService $offerService,
        JsonValidator $validator,
        SerializerInterface $serializer
    ) {
        $this->offerService = $offerService;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->offerService->getOffers($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'offers' => $response['items'],
                    'totalItems' => $response['totalItems'],

                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addOffer(Request $request): JsonResponse
    {
        $errors = $this->validator->validateRequest('offer-schema.json');

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'Bad request.',
                    'errors' => $errors
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $offerId = $this->offerService->addOffer($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'offerId' => $offerId
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/edit/{offer}', name: 'edit', methods: ['POST', 'PUT', 'PATCH'])]
    public function editOffer(Offer $offer, Request $request): JsonResponse
    {
        if (!$offer) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'Offer not found.'
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $this->offerService->editOffer($offer, $request);

            return new JsonResponse(
                [
                    'status' => 'success'
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{offer}', name: 'detail', methods: ['GET'])]
    public function getOfferDetail(Offer $offer): JsonResponse
    {
        if (!$offer) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'Offer was not found.'
                ],
                Response::HTTP_NOT_FOUND
            );
        } else {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'offer' => $this->serializer->normalize($offer)
                ],
                Response::HTTP_OK
            );
        }
    }

    #[Route('/delete/{offer}', name: 'delete', methods: ['DELETE'])]
    public function deleteOffer(Offer $offer): JsonResponse
    {
        try {
            $this->offerService->deleteOffer($offer);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Offer was deleted.'
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}