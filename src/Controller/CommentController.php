<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\ApartmentRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;


    }

    #[Route('/comment', name: 'app_comment', methods: 'GET')]
    public function indexC(CommentRepository $commentRepository): JsonResponse
    {
        $query = $commentRepository->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        $cs = $query->getResult();
        return $this->json($cs);
    }

    #[Route('/AddComment/{idUser}/{idAp}', name: 'add_comment', methods: 'POST')]
    public function createC(Request $request, $idUser, $idAp, UserRepository $userRepository, ApartmentRepository $apartmentRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['id' => $idUser]);
        $apartment = $apartmentRepository->findOneBy(['id' => $idAp]);
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setApartment($apartment);
        $comment->setContent($data['content']);
        $comment->setRating(0);


        $this->manager->persist($comment);
        $this->manager->flush();

        return $this->json($comment, Response::HTTP_CREATED);
    }

    #[Route('/editComment/{idC}', name: 'edit_comment',methods: ['PUT', 'PATCH'])]
    public function editC(Request $request, $idC, CommentRepository $commentRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $comment = $commentRepository->findOneBy(['id' => $idC]);


        $comment->setContent($data['content']);


        $this->manager->persist($comment);
        $this->manager->flush();

        return $this->json($comment);
    }
    #[Route('/deleteC/{id}', name: 'deleteC', methods: 'DELETE')]
    public function deleteC(CommentRepository $commentRepository, int $id): Response
    {
        $comment = $commentRepository->findOneBy(['id' => $id]);

        if (!$comment) {
            return $this->json('No Comment found for id' . $id, 404);
        }

        $this->manager->remove($comment);
        $this->manager->flush();

        return $this->json('Deleted a comment successfully with id ' . $id);
    }
}
