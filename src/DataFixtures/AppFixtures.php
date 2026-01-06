<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Task;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        // $manager->flush();


        $faker = Factory::create('en_US');

        for ($i = 0; $i < 30; $i++) {

            $task = new Task();

            $task->setTitle($faker->sentence(3));
            $task->setDescription($faker->boolean(70) ? $faker->paragraph() : null);

            // status
            $task->setIsDone($faker->boolean(25)); // ~25% zadań done

            // createdAt: losowo z ostatnich 14 dni
            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-14 days', 'now')
            );
            $task->setCreatedAt($createdAt);

            // dueAt: czasem null, czasem w przyszłości
            $dueAt = $faker->boolean(60)
                ? \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+20 days'))
                : null;

            $task->setDueAt($dueAt);

            $manager->persist($task);
        }

        $manager->flush();

    }
}
