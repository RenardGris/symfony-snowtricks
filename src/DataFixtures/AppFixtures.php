<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        $this->loadUser($faker,$manager);
        $this->loadFigures($manager);



        $manager->flush();

    }

    public function loadUser($faker, $manager){

        for ($i = 0; $i <= 5; $i++){

            $user = New User();

            $user->setUsername($faker->firstName)
                ->setEmail($faker->email)
                ->setAvatar($faker->imageUrl())
                ->setPassword($faker->password())
                ->setValidate(1)
                ->setToken(md5($user->getEmail()));

            $manager->persist($user);

        }

        $manager->flush();

    }

    public function loadFigures(ObjectManager $manager){
        $figures = [
            [
                'name' => "mute",
                'description' => " saisie de la carre frontside de la planche entre les deux pieds avec la main avant",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/2/2b/Shakedown_2008_Figure_3.jpg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Shakedown_2008_Figure_1a.jpg/1200px-Shakedown_2008_Figure_1a.jpg",
                        'favorite' => true,
                    ]
                ]

            ],
            [
                'name' => "melancholie ",
                'description' => " saisie de la carre backside de la planche, entre les deux pieds, avec la main avant ;",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/2/2b/Shakedown_2008_Figure_3.jpg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Shakedown_2008_Figure_1a.jpg/1200px-Shakedown_2008_Figure_1a.jpg",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => " japan air",
                'description' => "saisie de l'avant de la planche, avec la main avant, du côté de la carre frontside.",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/2/2b/Shakedown_2008_Figure_3.jpg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Shakedown_2008_Figure_1a.jpg/1200px-Shakedown_2008_Figure_1a.jpg",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "180",
                'description' => "un 180 désigne un demi-tour, soit 180 degrés d'angle ",
                'type' => "rotations",
                'medias' => [
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/2/2b/Shakedown_2008_Figure_3.jpg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Shakedown_2008_Figure_1a.jpg/1200px-Shakedown_2008_Figure_1a.jpg",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "360",
                'description' => "trois six pour un tour complet",
                'type' => "Rotation",
                'medias' => [
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/2/2b/Shakedown_2008_Figure_3.jpg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "img",
                        'link' => "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Shakedown_2008_Figure_1a.jpg/1200px-Shakedown_2008_Figure_1a.jpg",
                        'favorite' => false,
                    ]
                ]
            ],
        ];


        foreach ($figures as $fig) {

            $figure = new Figure();

            $authors = $manager->getRepository(User::class)->findAll();

            $author = $authors[random_int(1,5)];

            $figure->setName($fig['name'])
                ->setType($fig['type'])
                ->setDescription($fig['description'])
                ->setCreatedAt(new \DateTime())
                ->setAuthor($author);

            $manager->persist($figure);

            foreach ($fig['medias'] as $figMedia){
                $media = new Media();
                $media->setType($figMedia['type'])
                    ->setAddedAt(new \DateTime())
                    ->setFavorite($figMedia['favorite'])
                    ->setLink($figMedia['link'])
                    ->setFigure($figure);
                $manager->persist($media);
            }

        }

        $manager->flush();
    }



}
