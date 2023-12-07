<?php

namespace App\Controller\Public;

use App\Service\OfferService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Uid\Uuid;

/**
 * @OA\Tag(name="Offers")
 */
#[Route('/api/public/offer', name: 'api_public_offer_')]
class OfferController extends AbstractController
{
    private OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    #[Route('/{id}/detail', name: 'detail', methods: ['GET'])]
    public function getOffer(Uuid $id, Request $request): JsonResponse
    {
        try {
            $offer = $this->offerService->getOffer($id);

            if (!$offer) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Invalid request. Please try again later.'
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

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getOfferList(Request $request): JsonResponse
    {
        try {
            $offers = $this->offerService->getOffers($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $offers
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