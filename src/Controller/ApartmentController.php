<?php

namespace App\Controller;

use App\Entity\Apartment;

use App\Repository\ApartmentRepository;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

class ApartmentController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;


    }

    #[Route('/apartment', name: 'app_apartment', methods: 'GET')]
    public function sortApByDate(ApartmentRepository $apartmentRepository): JsonResponse
    {
        $query = $apartmentRepository->createQueryBuilder('a')
            ->Where('a.confirmed = true')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery();

        $apartments = $query->getResult();
        return $this->json($apartments);
    }

    #[Route('/apartmentPriceDesc', name: 'priceDesc_apartment', methods: 'GET')]
    public function sortApByPriceDesc(ApartmentRepository $apartmentRepository): JsonResponse
    {
        $query = $apartmentRepository->createQueryBuilder('a')
            ->Where('a.confirmed = true')
            ->orderBy('a.price', 'DESC')
            ->getQuery();

        $apartments = $query->getResult();
        return $this->json($apartments);
    }

    #[Route('/apartmentPriceASC', name: 'priceAsc_apartment', methods: 'GET')]
    public function sortApByPriceASC(ApartmentRepository $apartmentRepository): JsonResponse
    {
        $query = $apartmentRepository->createQueryBuilder('a')
            ->Where('a.confirmed = true')
            ->orderBy('a.price', 'ASC')
            ->getQuery();

        $apartments = $query->getResult();
        return $this->json($apartments);
    }

    #[Route('/apartment/state/{state}', name: 'state_apartment', methods: 'GET')]
    public function sortApByState(ApartmentRepository $apartmentRepository, $state): JsonResponse
    {
        $query = $apartmentRepository->createQueryBuilder('a')
            ->where('a.state = :state')
            ->andWhere('a.confirmed = true')
            ->setParameter('state', $state)
            ->getQuery();

        $apartments = $query->getResult();
        return $this->json($apartments);
    }

    #[Route('/apartment/country/{country}', name: 'country_apartment', methods: 'GET')]
    public function sortApByCountry(ApartmentRepository $apartmentRepository, $country): JsonResponse
    {
        $query = $apartmentRepository->createQueryBuilder('a')
            ->where('a.country = :country')
            ->andWhere('a.confirmed = true')
            ->setParameter('country', $country)
            ->getQuery();

        $apartments = $query->getResult();
        return $this->json($apartments);
    }

    #[Route('/apartmentByUser/{idUser}', name: 'user_apartments')]
    public function indexApByUser(UserRepository $userRepository, $idUser, ApartmentRepository $apartmentRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => $idUser]);
        $query = $apartmentRepository->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.confirmed = true')
            ->setParameter('user', $user)
            ->getQuery();

        $apartments = $query->getResult();
        if (!$apartments) {
            return $this->json(['message' => 'Apartment not found'], 404);
        }
        return $this->json($apartments);
    }

    #[Route('/findApById/{id}', name: 'id_apartment')]
    public function findApById(ApartmentRepository $apartmentRepository, $id): JsonResponse
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $id]);
        if (!$apartment) {
            return $this->json(['message' => 'Apartment not found'], 404);
        }
        return $this->json($apartment);
    }


    #[Route('/AddApartment/{idUser}', name: 'add_apartment', methods: 'POST')]
    public function createAp(Request $request, $idUser, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['id' => $idUser]);
        $apartment = new Apartment();

        $apartment->setCity($data['city']);
        $apartment->setState($data['state']);
        $apartment->setAddress($data['address']);
        $apartment->setConfirmed(false);
        $apartment->setCountry($data['country']);
        $apartment->setDescription($data['description']);
        $apartment->setPrice($data['price']);
        $apartment->setBedrooms($data['bedrooms']);
        $apartment->setAvailability(true);
        $apartment->setZipcode($data['zipcode']);
        $apartment->setAvailableFrom(DateTime::createFromFormat('Y-m-d', $data['availableFrom']));
        $currentDate = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

//        $currentDate = new DateTime();
        $apartment->setCreatedAt($currentDate);

        $apartment->setAvailableTo(DateTime::createFromFormat('Y-m-d', $data['availableTo']));
        $apartment->setUser($user);
        $this->manager->persist($apartment);
        $this->manager->flush();

        return $this->json($apartment, Response::HTTP_CREATED);
    }

    #[Route('/updateApart/{id}', name: 'apart_update', methods: ['PUT', 'PATCH'])]
    public function updateAp($id, ApartmentRepository $apartmentRepository, Request $request): JsonResponse
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $id]);
        if (!$apartment) {
            return $this->json(['message' => 'Apartment not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $apartment->setAddress($data['address']);
        $apartment->setCity($data['city']);
        $apartment->setState($data['state']);
        $apartment->setZipcode($data['zipcode']);
        $apartment->setCountry($data['country']);
        $apartment->setDescription($data['description']);
        $apartment->setPrice($data['price']);
        $apartment->setBedrooms($data['bedrooms']);

        $apartment->setAvailableFrom(DateTime::createFromFormat('Y-m-d', $data['availableFrom']));

        $apartment->setAvailableTo(DateTime::createFromFormat('Y-m-d', $data['availableTo']));

        $this->manager->persist($apartment);
        $this->manager->flush();
        return $this->json($apartment);

    }

    #[Route('/deleteApart/{id}', name: 'delete_apart', methods: 'DELETE')]
    public function deleteAp(ApartmentRepository $apartmentRepository, int $id): Response
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $id]);

        if (!$apartment) {
            return $this->json('No Apartment found for id' . $id, 404);
        }

        $this->manager->remove($apartment);
        $this->manager->flush();

        return $this->json('Deleted a apartment successfully with id ' . $id);
    }

    #[Route('/admin/confirmation/{idAp}', name: 'confirmation_apart')]
    public function validAp(ApartmentRepository $apartmentRepository, $idAp): JsonResponse
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $idAp]);
        $apartment->setConfirmed(true);
        $this->manager->persist($apartment);
        $this->manager->flush();
        return $this->json($apartment);
    }

}