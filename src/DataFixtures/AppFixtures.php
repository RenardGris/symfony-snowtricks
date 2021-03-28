<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        $this->loadUser($faker,$manager);
        $this->loadFigures($manager);
        $this->loadComments($manager);


        $manager->flush();

    }

    public function loadUser($faker, $manager){

        for ($i = 0; $i <= 5; $i++){

            $user = New User();
            $encoded = password_hash('demo', PASSWORD_DEFAULT);

            $user->setUsername($faker->firstName)
                ->setEmail($faker->email)
                ->setAvatar($faker->randomElement(['avatar-1.jpg','avatar-2.jpg','avatar-3.jpg','avatar-4.jpg']))
                ->setPassword($encoded)
                ->setValidate(1)
                ->setNewToken();

            $manager->persist($user);

        }

        $manager->flush();

    }

    public function loadFigures(ObjectManager $manager){
        $figures = [
            [
                'name' => "Mute",
                'description' => " saisie de la carre frontside de la planche entre les deux pieds avec la main avant",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "mute.jpg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/CflYbNXZU3Q",
                        'favorite' => false,
                    ]
                ]

            ],
            [
                'name' => "Melancholie",
                'description' => " saisie de la carre backside de la planche, entre les deux pieds, avec la main avant ;",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "melancholy.jpg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/KEdFwJ4SWq4",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "Indy",
                'description' => "saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "indy.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/iKkhKekZNQ8",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "stalefish ",
                'description' => "saisie de la carre backside de la planche entre les deux pieds avec la main arrière",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "stalefish.jpg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/f9FjhCt_w2U",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "Truck driver",
                'description' => "saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)",
                'type' => "Grab",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "truck-driver.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/6tgjY8baFT0",
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
                        'type' => "photo",
                        'link' => "japan-air.png",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/CzDjM7h_Fwo",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "180",
                'description' => "un 180 désigne un demi-tour, soit 180 degrés d'angle ",
                'type' => "Rotation",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/6tgjY8baFT0",
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
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/6tgjY8baFT0",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "Front flip",
                'description' => "Un  frontflip est une rotation verticale vers l'avant.
                Il est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation. Les flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip...), mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées.
                Néanmoins, en dépit de la difficulté technique relative d'une telle figure, le danger de retomber sur la tête ou la nuque est réel et conduit certaines stations de ski à interdire de telles figures dans ses snowparks.",
                'type' => "Flips",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "frontflip.jpg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/9_zC7CdvYu4",
                        'favorite' => false,
                    ]
                ]
            ],
            [
                'name' => "Back flip",
                'description' => "Un  frontflip est une rotation verticale vers l'arrière.
                Il est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation. Les flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip...), mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées.
                Néanmoins, en dépit de la difficulté technique relative d'une telle figure, le danger de retomber sur la tête ou la nuque est réel et conduit certaines stations de ski à interdire de telles figures dans ses snowparks.",
                'type' => "Flips",
                'medias' => [
                    [
                        'type' => "photo",
                        'link' => "backflip.jpg",
                        'favorite' => true,
                    ],
                    [
                        'type' => "photo",
                        'link' => "default.jpeg",
                        'favorite' => false,
                    ],
                    [
                        'type' => "video",
                        'link' => "https://youtube.com/embed/5bpzng08nzk",
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
                ->setSlug($figure->getName())
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

    public function loadComments(ObjectManager $manager){
        $figures = $manager->getRepository(Figure::class)->findAll();
        $authors = $manager->getRepository(User::class)->findAll();

        foreach ($figures as $figure) {

            $nbComments = random_int(5,20);
            for ($i=0;$i<$nbComments;$i++){

                $comment = new Comment();

                $author = $authors[random_int(1,5)];

                $comment->setAuthor($author)
                    ->setFigure($figure)
                    ->setContent('Commentaire de test')
                    ->setCreatedAt(new \DateTime());
                $manager->persist($comment);
            }

        }

        $manager->flush();
    }




}
