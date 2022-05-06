<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeController extends AbstractController
{
    /**
     * @Route("/recipes", name="recipe")
     */
    public function home(RecipeRepository $recipeRepository)
    {
        $recipes = $recipeRepository->findAll();

        return $this->render(
            'recipe/index.html.twig', [
                'recipes' => $recipes,
            ]
        );
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(ManagerRegistry $doctrine, Request $request, UserInterface $user = null)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        $date = new DateTime();

        if ($form->isSubmitted() &&
                $form->isValid()) {
            $recipe->setAuthor($user);
            $recipe->setDate($date);
            $entityManager = $doctrine->getManager();

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'La recette a été bien été ajoutée.');

            return $this->redirectToRoute('recipes');
        }

        return $this->renderForm(
                'recipe/new.html.twig', [
                'form' => $form,
                'user' => $user, ]);
    }
}
