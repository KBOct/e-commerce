<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
// use Cocur\Slugify\Slugify;
use App\Entity\Image;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Doctrine\ORM\EntityManagerInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    /**
     * @author BAZILE-OCTUVON Kenny <kennybazileoctuvon@gmail.com>
     * 
     * Permet de charger les fausses données (fixtures) utilisateurs
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // Utilisateurs
        $users=[];
        $genders=['male','female'];



        for($i = 1; $i <= 10; $i++) {
            $user = new User();
            $gender = $faker->randomElement($genders);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99).'.jpg';
    
            $picture = $picture. ($gender == 'male' ?'men/' : 'women/'). $pictureId;

            $password = $this->encoder->encodePassword($user, 'password');

            $delAddress = $faker->address;
            $bilAddress = $faker->address;

            $user->setFirstName($faker->firstname($gender))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                //  ->setLogin($faker->userName)
                 ->setPassword($password)
                 ->setPicture($picture)
                 ->setDeliveryAddress($delAddress)
                 ->setBillingAddress(mt_rand(0,1) == 1 ? $delAddress : $bilAddress);

            $manager->persist($user);
            $users[]=$user;
        }



        // Produits
        for($i = 1; $i <= 30; $i++) {
            $product = new Product();
            $slugify = new Slugify();

            $name= $faker->sentence();
            $slug=$slugify->slugify($name);
            $photo = $faker->imageUrl(300,300);
            $introduction = $faker->paragraph(2);
            $description = '<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>';

            $product->setDescription($description)
                    ->setIntroduction($introduction)
                    ->setName($name)
                    ->setSlug($slug)
                    ->setIsDeleted(false)
                    ->setQuantity(mt_rand(1,100))
                    ->setPrice(mt_rand(2.0,99.0))                
                    ->setPhoto($photo);

            for($j = 1; $j <= mt_rand(2,5); $j++){
                $image  = new Image();
                $image  ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setProduct($product);
                
                $manager->persist($image);
            }
            
            $manager->persist($product);
        }


        $manager->flush();
    }
}
