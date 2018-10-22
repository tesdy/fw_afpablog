<?php


use Phinx\Seed\AbstractSeed;

/**
 * Permet de remplir la BDD avec des données Bidons !
 * Class PostSeeder
 */
class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        // SEEDING DE 5 CATEGORIES BIDONS (nom/slug)
        $data = [];
        $faker = \Faker\Factory::create('fr_FR');
        for ($i=0; $i < 5; ++$i) {
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug
            ];
        }
        $this->table('categories')->insert($data)->save();
        // SEEDING DE 5 users BIDONS (nom/pseudo)
        $data = [];
        $faker = \Faker\Factory::create('fr_FR');
        for ($i=0; $i < 5; ++$i) {
            $data[] = [
                'name' => $faker->firstName,
                'pseudo' => $faker->userName,
                'email' => $faker->freeEmail
            ];
        }
        $this->table('users')->insert($data)->save();

        // SEEDING DE 100 ARTICLES (nom/slug/contenu/dateCreation/dateUpdate)
        $data = [];
        for ($i=0; $i < 100; ++$i) {
            $date =$faker->unixTime('now');
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug,
                'category_id' => random_int(1, 5),
                'user_id' => random_int(1, 5),
                'content' => $faker->text(3000),
                'created_at' => date('Y-m-d H:i:s', $date),
                'updated_at' =>  date('Y-m-d H:i:s', $date),
            ];
        }
        $this->table('posts')->insert($data)->save();
    }
}
