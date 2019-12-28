<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
// use Cocur\Slugify\Slugify;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for($i = 1; $i <= 30; $i++) {
            $product = new Product();
            //$slugify = new Slugify();

            $name= $faker->sentence();
            // $slug=$slugify->slugify($name);
            $photo = $faker->imageUrl(300,300);
            $introduction = $faker->paragraph(2);
            $description = '<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>';

            $product->setDescription($description)
                    ->setIntroduction($introduction)
                    ->setName($name)
                    //->setSlug($slug)
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