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
use Symfony\Component\Uid\Uuid;

#[Route('/api/offer', name: 'api_offer_')]
class OfferController extends AbstractController
{
    private OfferService $offerService;
    private JsonValidator $validator;

    public function __construct(OfferService $offerService, JsonValidator $validator) {
        $this->offerService = $offerService;
        $this->validator = $validator;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->offerService->getOffers($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $response
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
            $offer = $this->offerService->addOffer($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'offer' => $offer
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

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function editOffer(Uuid $id, Request $request): JsonResponse
    {
        try {
            $offer = $this->offerService->editOffer($id, $request);

            if (!$offer) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Invalid request. Please try again later.'
                    ]
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'offer' => $offer
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

    #[Route('/{id}/detail', name: 'detail', methods: ['GET'])]
    public function getOfferDetail(Uuid $id): JsonResponse
    {
        try {
            $offer = $this->offerService->getOffer($id);

            if (!$offer) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Offer was not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'offer' => $offer
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ]
            );
        }
    }

    #[Route('/{offerId}/device/{deviceId}/remove', name: '', methods: ['DELETE'])]
    public function deleteOfferDevice(Uuid $offerId, Uuid $deviceId, Request $request): JsonResponse
    {
        try {
            $offer = $this->offerService->removeOfferDevice($offerId, $deviceId);

            if (!$offer) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request. Please try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'offer' => $offer
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

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteOffer(Uuid $id): JsonResponse
    {
        try {
            $status = $this->offerService->deleteOffer($id);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

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