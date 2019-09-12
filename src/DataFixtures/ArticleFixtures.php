<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');

        // creer 3 categories fakees
        for($i = 1 ; $i <= 3 ; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

            // creer entre 4 et 6 article par categorie
            for ($j = 1; $j <= mt_rand(4, 6) ; $j ++) {
                $article = new Article();

                $content = join($faker->paragraphs(mt_rand(3, 5)), '<br>');

                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl() )
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
    
                $manager->persist($article);

                // Ajout de 3 à 10 commentaires par article
                for($k = 1; $k <= mt_rand(3, 10); $k ++) {
                    $comment = new Comment();

                    $content = join($faker->paragraphs(mt_rand(1, 3)), '<br>');

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt()); // période de diff entre deux dates
                    $days = $interval->days; // extraction des jours
                    $minimum = '-' . $days . ' days'; // exemple -30 days   

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);

                    $manager->persist($comment);
            }

            }          

        }       

        $manager->flush();
    }
}
