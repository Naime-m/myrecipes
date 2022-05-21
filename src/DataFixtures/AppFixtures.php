<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test@local.com');
        $password = $this->hasher->hashPassword($user1, 'pass');
        $user1->setPassword($password);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('test2@local.com');
        $password = $this->hasher->hashPassword($user2, 'pass');
        $user2->setPassword($password);
        $manager->persist($user2);

        for ($i = 1; $i < 5; ++$i) {
            $category = new Category();
            $category->setName('Catégorie'.$i);
            $manager->persist($category);
        }

        for ($i = 1; $i < 30; ++$i) {
            $recipe1 = new Recipe();
            $recipe1->setAuthor($user1);
            $recipe1->setTitle('Titre'.$i);
            $recipe1->setIngredients('Ingrédient mystère');
            $recipe1->setCategory($category);
            $recipe1->setContent('Une recette exquise et très simple à faire');
            $recipe1->setDate(new DateTime());
            $manager->persist($recipe1);
        }

        for ($i = 1; $i < 30; ++$i) {
            $recipe2 = new Recipe();
            $recipe2->setAuthor($user2);
            $recipe2->setCategory($category);
            $recipe2->setContent('Une recette exquise et très simple à faire');
            $recipe2->setTitle('Titre'.$i);
            $recipe2->setIngredients('Ingrédient mystère');
            $recipe2->setDate(new DateTime());
            $manager->persist($recipe2);
        }

        $manager->flush();
    }
}
