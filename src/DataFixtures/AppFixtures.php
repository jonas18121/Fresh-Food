<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Unity;
use App\Entity\Emplacement;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// faire cette commande pour les envoyers en base de donnée
// php bin/console doctrine:fixtures:load

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $userAdmin = new User();
        $userAdmin->setPseudo('admin')
            ->setEmail('admin@gmail.com')
            ->setPassword($this->passwordEncoder->encodePassword($userAdmin, 'adminadmin'))
            ->setRoles(["ROLE_ADMIN"])
            ->setCreatedAt($faker->dateTimeBetween('- 2 months'))
        ;
        $manager->persist($userAdmin);

        $now = new \DateTime();
        $maxDays = new \DateTime('15 days');
        $days = $now->diff($userAdmin->getCreatedAt())->days;
        $minimun = '-' . $days . ' days';

        //-------- C A T E G O R Y -------//
        $category1 = new Category();
        $category1->setName('produits laitiers')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('fruits de mer')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('fruit et légumes')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category3);

        $category4 = new Category();
        $category4->setName('aliments transformé')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category4);

        $category5 = new Category();
        $category5->setName('viandes')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category5);

        $category6 = new Category();
        $category6->setName('féculents')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category6);

        $category = new Category();
        $category->setName('conserve')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($category); 

        //-------- U N I T Y -------//
        $unity1 = new Unity();
        $unity1->setName('A la pièce')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($unity1);

        $unity2 = new Unity();
        $unity2->setName('kg')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($unity2);

        $unity3 = new Unity();
        $unity3->setName('litre')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($unity3);

        $unity4 = new Unity();
        $unity4->setName('boite')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($unity4);

        $unity = new Unity();
        $unity->setName('sachet')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($unity);

        //-------- E M P L A C E M E N T -------//
        $emplacement1 = new Emplacement();
        $emplacement1->setName('placard')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($emplacement1);

        $emplacement2 = new Emplacement();
        $emplacement2->setName('armoir')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($emplacement2);

        $emplacement3 = new Emplacement();
        $emplacement3->setName('frigo')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($emplacement3);

        $emplacement4 = new Emplacement();
        $emplacement4->setName('congélateur')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($emplacement4);

        $emplacement = new Emplacement();
        $emplacement->setName('panier')
            ->setCreatedAt($faker->dateTimeBetween($minimun))
            ->setAuthor($userAdmin)
        ;
        $manager->persist($emplacement);

        //-------- P R O D U C T    A D M I N ----------//
        $daysAdmin = $now->diff($userAdmin->getCreatedAt())->days;
        $minimunAdmin = '-' . $daysAdmin . ' days';

        $adminDays = $faker->dateTimeBetween($minimunAdmin);

        $daysCategory = $adminDays->diff($category->getCreatedAt())->days;
        $minimunCategory = '-' . $daysCategory . ' days';

        $categoryDays = $faker->dateTimeBetween($minimunCategory);

        $daysUnity = $categoryDays->diff($unity->getCreatedAt())->days;
        $minimunUnity = '-' . $daysUnity . ' days';

        $unityDays = $faker->dateTimeBetween($minimunUnity);

        $daysEmplacement = $unityDays->diff($emplacement->getCreatedAt())->days;
        $minimunEmplacement = '-' . $daysEmplacement . ' days';

        $emplacementDays = $faker->dateTimeBetween($minimunEmplacement);

        for($i = 1; $i <= 10; $i++){

            $product = new Product();
            $product->setName($faker->word())
                ->setCreatedAt($emplacementDays)
                ->setAuthor($userAdmin)
                ->setClassifiedIn($category)
                ->setPlaceIn($emplacement)
                ->setUnits($unity)
                ->setQuantity($faker->randomDigit)
                ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
            ;

            $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
            $minimunProduct = '-' . $daysProduct . ' days';

            $productDays = $faker->dateTimeBetween($minimunProduct);
            //$expirationDays = $faker->dateTimeBetween($minimunProduct);

            $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
            $minimunExpiration = '-' . $daysExpiration . ' days';

            $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);

            $product->setExpirationDate($expirationDays);

            $manager->persist($product);
        }

        //-------- P R O D U C T    A D M I N   2 ----------//
        for($a = 1; $a <= 10; $a++){

            $product = new Product();
            $product->setName($faker->word())
                ->setCreatedAt($emplacementDays)
                ->setAuthor($userAdmin)
                ->setClassifiedIn($category2)
                ->setPlaceIn($emplacement2)
                ->setUnits($unity2)
                ->setQuantity($faker->randomDigit)
                ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
            ;

            $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
            $minimunProduct = '-' . $daysProduct . ' days';

            $productDays = $faker->dateTimeBetween($minimunProduct);
            //$expirationDays = $faker->dateTimeBetween($minimunProduct);

            $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
            $minimunExpiration = '-' . $daysExpiration . ' days';

            $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);

            $product->setExpirationDate($expirationDays);

            $manager->persist($product);
        }

        //-------- P R O D U C T    A D M I N   3 ----------//
        for($a = 1; $a <= 10; $a++){

            $product = new Product();
            $product->setName($faker->word())
                ->setCreatedAt($emplacementDays)
                ->setAuthor($userAdmin)
                ->setClassifiedIn($category3)
                ->setPlaceIn($emplacement3)
                ->setUnits($unity3)
                ->setQuantity($faker->randomDigit)
                ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
            ;

            $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
            $minimunProduct = '-' . $daysProduct . ' days';

            $productDays = $faker->dateTimeBetween($minimunProduct);
            //$expirationDays = $faker->dateTimeBetween($minimunProduct);

            $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
            $minimunExpiration = '-' . $daysExpiration . ' days';

            $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);

            $product->setExpirationDate($expirationDays);

            $manager->persist($product);
        }


        //-------- U S E R -------//
        for($e = 1; $e <= 10; $e++){
            $user = new User();
            $user->setPseudo($faker->firstName())
                ->setEmail($faker->email)
                ->setPassword($faker->password())
                ->setRoles(["ROLE_USER"])
                ->setCreatedAt($faker->dateTimeBetween($minimun))
            ;
            $manager->persist($user);

            //-------- P R O D U C T    U S E R   1 -------//
            for($p = 1; $p <= 10; $p++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category)
                    ->setPlaceIn($emplacement)
                    ->setUnits($unity)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   2 -------//
            for($q = 1; $q <= 10; $q++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category2)
                    ->setPlaceIn($emplacement2)
                    ->setUnits($unity2)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   3 -------//
            for($r = 1; $r <= 10; $r++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category3)
                    ->setPlaceIn($emplacement3)
                    ->setUnits($unity3)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   4 -------//
            for($s = 1; $s <= 10; $s++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category2)
                    ->setPlaceIn($emplacement1)
                    ->setUnits($unity3)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   5 -------//
            for($u = 1; $u <= 10; $u++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category3)
                    ->setPlaceIn($emplacement2)
                    ->setUnits($unity1)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   6 -------//
            for($v = 1; $v <= 10; $v++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category1)
                    ->setPlaceIn($emplacement3)
                    ->setUnits($unity3)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   7 -------//
            for($w = 1; $w <= 10; $w++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category1)
                    ->setPlaceIn($emplacement3)
                    ->setUnits($unity1)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }

            //-------- P R O D U C T    U S E R   8 -------//
            for($x = 1; $x <= 10; $x++){

                $product = new Product();
                $product->setName($faker->word())
                    ->setCreatedAt($emplacementDays)
                    ->setAuthor($user)
                    ->setClassifiedIn($category1)
                    ->setPlaceIn($emplacement3)
                    ->setUnits($unity2)
                    ->setQuantity($faker->randomDigit)
                    ->setPurchaseDate($faker->dateTimeBetween($emplacementDays))
                ;
    
                $daysProduct = $unityDays->diff($product->getCreatedAt())->days;
                $minimunProduct = '-' . $daysProduct . ' days';
    
                $productDays = $faker->dateTimeBetween($minimunProduct);
                //$expirationDays = $faker->dateTimeBetween($minimunProduct);
    
                $daysExpiration = $productDays->diff($product->getPurchaseDate())->days;
                $minimunExpiration = '-' . $daysExpiration . ' days';
    
                $expirationDays = $faker->dateTimeBetween($minimunExpiration, $maxDays);
    
                $product->setExpirationDate($expirationDays);
    
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
