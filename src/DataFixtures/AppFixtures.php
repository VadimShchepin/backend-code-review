<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\MessageStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;
use function Psl\Iter\random;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $statusValues = MessageStatusEnum::cases();
        
        foreach (range(1, 10) as $i) {
            $randomStatus = $statusValues[array_rand($statusValues)];
            $message = new Message($faker->sentence,$randomStatus);
            $manager->persist($message);
        }

        $manager->flush();
    }
}
