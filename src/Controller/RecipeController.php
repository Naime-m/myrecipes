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
     * @Route("/recipes", name="recipes")
     */
    public function recipes(RecipeRepository $recipeRepository)
    {
        $recipes = $recipeRepository->findAll();

        return $this->render(
            'recipe/index.html.twig', [
                'recipes' => $recipes,
            ]
        );
    }

    /**
     * @Route("/recipe/{id}", name="recipe")
     */
    public function show(Recipe $recipe)
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * @Route("/recipe/{id}/edit", name="edit")
     */
    public function edit(ManagerRegistry $doctrine, Request $request, Recipe $recipe)
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('recipes');
        }

        return $this->renderForm(
            'recipe/edit.html.twig', [
                'form' => $form,
            ]);
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
                 ]);
    }

    /**
     * @Route("/recipe/{id}/delete", name="delete")
     */
    public function delete(ManagerRegistry $doctrine, Recipe $recipe)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('recipes');
    }
}
