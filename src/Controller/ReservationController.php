<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ApartmentRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;


    }

    #[Route('/admin/reservation', name: 'app_reservation')]
    public function index(ReservationRepository $reservationRepository): JsonResponse //by date missing
    {
        $reservations = $reservationRepository->findAll();
        return $this->json($reservations);
    }
    #[Route('/ResByApa/{idAp}', name: 'apa_res')]
    public function getResByAp(ApartmentRepository $apartmentRepository, $idAp): JsonResponse
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $idAp]);

        return $this->json($apartment->getReservations());
    }
//
    #[Route('/ResByUser/{idUser}', name: 'user_res')]
    public function indexResByUser(UserRepository $userRepository, $idUser): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => $idUser]);

        return $this->json($user->getReservations());
    }

    #[Route('/findResById/{id}', name: 'id_res')]
    public function findResById(ReservationRepository $reservationRepository, $id): JsonResponse
    {
        $reservation =  $reservationRepository->findOneBy(['id' => $id]);
        if (!$reservation) {
            return $this->json(['message' => 'Reservation not found'], 404);
        }
        return $this->json($reservation);
    }





    #[Route('/admin/validation/{idRes}', name: 'valid_reservation')]
    public function valid($idRes,ReservationRepository $reservationRepository,ApartmentRepository $apartmentRepository): JsonResponse
    {   $reservation = $reservationRepository->findOneBy(['id' => $idRes]);
        $apartment = $apartmentRepository->findOneBy(['id' => $reservation->getApartment()->getId()]);

        $reservation->setValidate(true);
        $apartment->setAvailability(false);
        $apartment->setAvailableFrom($reservation->getCheckoutDate()->modify('+1 day'));
        $apartment->setAvailableTo($reservation->getCheckoutDate()->modify('+30 day'));
        $this->manager->persist($reservation);
        $this->manager->persist($apartment);
        $this->manager->flush();
        return $this->json($reservation);
    }

    #[Route('/AddReservation/{idUser}/{idAp}', name: 'add_reservation', methods:'POST')]
    public function createRes(Request $request, $idUser, $idAp, ApartmentRepository $apartmentRepository, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['id' => $idUser]);
        $apartment = $apartmentRepository->findOneBy(['id' => $idAp]);
        $reservation = new Reservation();

        $reservation->setUser($user);
        $reservation->setApartment($apartment);

        $reservation->setCheckinDate(DateTime::createFromFormat('Y-m-d',$data['checkinDate']));
        $reservation->setCheckoutDate(DateTime::createFromFormat('Y-m-d',$data['checkoutDate']));
        $reservation->setTotalPrice($data['totalPrice']);
        $reservation->setValidate(false);
        $this->manager->persist($reservation);
        $this->manager->flush();

        return $this->json($reservation, Response::HTTP_CREATED);
    }
    #[Route('/updateRes/{id}', name: 'Res_update', methods: ['PUT', 'PATCH'])]
    public function updateRes($id, ReservationRepository $reservationRepository,Request $request): JsonResponse
    {
        $reservation = $reservationRepository->findOneBy(['id' => $id]);
        if (!$reservation) {
            return $this->json(['message' => 'Reservation not found'], 404);
        }
        elseif ($reservation->isValidate()){
            return $this->json(['message' => "Reservation Validate you can't update it"]);
        }

        $data = json_decode($request->getContent(), true);
        $reservation->setCheckinDate(DateTime::createFromFormat('Y-m-d',$data['checkinDate']));
        $reservation->setCheckoutDate(DateTime::createFromFormat('Y-m-d',$data['checkoutDate']));
        $reservation->setTotalPrice($data['totalPrice']);
        return $this->json( $reservation);

    }
    #[Route('/admin/deleteRes/{id}', name: 'delete_res', methods: 'DELETE')]
    public function deleteRes(ReservationRepository $reservationRepository, int $id): Response
    {
        $reservation = $reservationRepository->findOneBy(['id' => $id]);

        if (!$reservation) {
            return $this->json('No Reservation found for id' . $id, 404);
        }

        $this->manager->remove($reservation);
        $this->manager->flush();

        return $this->json('Deleted a Reservation successfully with id ' . $id);
    }
    #[Route('/cancelRes/{id}', name: 'cancel_res', methods: 'DELETE')]
    public function cancelRes(ReservationRepository $reservationRepository, int $id): Response
    {
        $reservation = $reservationRepository->findOneBy(['id' => $id]);

        if (!$reservation) {
            return $this->json('No Reservation found for id' . $id, 404);
        }
        elseif ($reservation->isValidate()){
            return $this->json(['message' => "Reservation Validate you can't cancel it" ]);
        }

        $this->manager->remove($reservation);
        $this->manager->flush();

        return $this->json('Deleted a Reservation successfully with id ' . $id);
    }
}
