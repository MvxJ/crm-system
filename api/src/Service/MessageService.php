<?php

namespace App\Service;

use App\Entity\Message;
use App\Helper\MessageHelper;
use App\Repository\CustomerRepository;
use App\Repository\MessageRepository;
use App\Repository\ServiceRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class MessageService
{
    private MessageRepository $messageRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;
    private ServiceRequestRepository $serviceRequestRepository;
    private MessageHelper $messageHelper;

    public function __construct(
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        ServiceRequestRepository $serviceRequestRepository,
        MessageHelper $messageHelper
    ) {
        $this->messageRepository = $messageRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->serviceRequestRepository = $serviceRequestRepository;
        $this->messageHelper = $messageHelper;
    }

    public function createMessage(Request $request): ?array
    {
        $message = new Message();
        $content = json_decode($request->getContent(), true);
        $message = $this->objectCreator($message, $content);

        if (!$message) {
            return null;
        }

        $message->setCreatedDate(new \DateTime());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $this->sendMessage($message);

        return $this->createMessageArray($message);
    }

    public function getMessages(Request $request): array
    {
        $messagesArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $type = $request->get('type', 'all');
        $customer = $request->get('customerId', 'all');
        $maxResults = $this->messageRepository->countMessages($customer, $type);
        $messages = $this->messageRepository->getMessagesWithPagination(
            $page,
            $itemsPerPage,
            $order,
            $orderBy,
            $customer,
            $type
        );

        /** @var Message $message */
        foreach ($messages as $message) {
            $messagesArray[] = $this->createMessageArray($message);
        }

        return [
            'maxResults' => $maxResults,
            'messages' => $messagesArray
        ];
    }

    public function getCustomerMessages(string $userEmail, Request $request): array
    {
        $messagesArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $maxResults = $this->messageRepository->countCustomerMessages($userEmail);
        $messages = $this->messageRepository->getCustomerMessages($page, $itemsPerPage, $order, $orderBy, $userEmail);

        /** @var Message $message */
        foreach ($messages as $message) {
            $messagesArray[] = $this->createMessageArray($message);
        }

        return [
            'maxResults' => $maxResults,
            'messages' => $messagesArray
        ];
    }

    private function sendMessage(Message $message): void
    {
        $this->messageHelper->sendMessageToCustomer($message);
    }

    private function objectCreator(Message $message, array $content): ?Message
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Message::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setCustomer') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $message->setCustomer($customer);
                } elseif ($setterMethod == 'setServiceRequest') {
                    $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $fieldValue]);

                    if (!$serviceRequest) {
                        return null;
                    }

                    $message->setServiceRequest($serviceRequest);
                } elseif (method_exists($message, $setterMethod)) {
                    $message->$setterMethod($fieldValue);
                }
            }
        }

        return $message;
    }

    private function createMessageArray(Message $message): array
    {
        $messageArray = [
            'id' => $message->getId(),
            'customer' => [
                'id' => $message->getCustomer()->getId(),
                'email' => $message->getCustomer()->getEmail(),
                'name' => $message->getCustomer()->getFirstName(),
                'surname' => $message->getCustomer()->getLastName()
            ],
            'subject' => $message->getSubject(),
            'message' => $message->getMessage(),
            'type' => $message->getType(),
            'createdAt' => $message->getCreatedDate(),
            'phone' => $message->getPhoneNumber(),
            'email' => $message->getEmail(),
        ];

        if ($message->getServiceRequest()) {
            $messageArray['serviceRequest'] = [
                'id' => $message->getServiceRequest()->getId(),
                'contractNumber' => $message->getServiceRequest()->getContract()->getNumber()
            ];
        }

        return $messageArray;
    }
}