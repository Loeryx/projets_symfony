<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Language;
use App\Entity\Movie;
use App\Entity\Playlist;
use App\Entity\PlaylistMedia;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Season;
use App\Entity\Serie;
use App\Entity\Comment;
use App\Entity\SubscriptionHistory;
use App\Enum\CommentStatusEnum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const MAX_USERS = 10;
    public const PLAYLIST_PER_USER = 5;
    public const MAX_SUBSCRIPTIONS = 3;
    public const MAX_MEDIA = 100;
    public const MAX_MEDIA_PER_PLAYLIST = 5;
    public const MAX_SEASONS = 3;
    public const MAX_EPISODES = 15;
    public const MAX_COMMENTS_PER_MEDIA = 10;
    public const MAX_CATEGORY_PER_MEDIA = 3;
    public const MAX_LANGUAGE_PER_MEDIA = 3;
    public const MAX_SUBSCRIPTIONS_HISTORY_PER_USER = 2;

    public function load(ObjectManager $manager): void {
        $users = [];
        $medias = [];
        $playlists = [];
        $categories = [];
        
        for ($i=0; $i < SELF::MAX_USERS; $i++) {
            $user = $this->createUser($i, $manager);
            $users[] = $user;

            for ($k = 0; $k < random_int(1, self::PLAYLIST_PER_USER); $k++) {
                $playlists = $this->createPlaylists($user, $manager, $playlists);
            }
        }

        $manager->flush();
    }

    protected function createUser(int $i, ObjectManager $manager) : User {
        $user = new User();
        $user->setUsername("test_{$i}");
        $user->setPassword("test");
        $user->setEmail("test_{$i}@gmail.com");
        $manager->persist($user);

        return $user;
    }

    protected function createPlaylists(User $user, ObjectManager $manager, array $playlists) : array {
        $playlist = new Playlist();
        $playlist->setName("playlist1");
        $playlist->setCreatedAt(new \DateTimeImmutable());
        $playlist->setUpdatedAt(new \DateTimeImmutable());
        $playlist->setCreator($user);
        $manager->persist($playlist);
        $playlists[] = $playlist;

        return $playlists;
    }

    protected function createMediaAndLinkToPlaylists(ObjectManager $manager, array $playlists): void {
        for ($j = 0; $j < self::MAX_MEDIA; $j++) {
            $media = $this->createMedia($j, $manager);

            for ($l = 0; $l < random_int(1, self::MAX_MEDIA_PER_PLAYLIST); $l++) {
                $this->linkMediaToPlaylist($media, $playlists, $manager);
            }
        }
    }

    protected function createMedia(int $j, ObjectManager $manager) : Media {
        $media = random_int(0, 1) === 0 ? new Movie() : new Serie();

        $media->setTitle("Film {$j}");
        $media->setLongDescription('Longue description');
        $media->setShortDescription('Short description');
        $media->setCoverImage('http://');
        $media->setReleaseDate(new \DateTime(datetime: "+7 days"));
        $manager->persist($media);

        return $media;
    }

    protected function linkMediaToPlaylist(Media $media, array $playlists, ObjectManager $manager): void {
        $playlistMedia = new PlaylistMedia();
        $playlistMedia->setMedia($media);
        $playlistMedia->setAddedAt(new \DateTimeImmutable());
        $playlistMedia->setPlaylist($playlists[array_rand($playlists)]);

        $manager->persist($playlistMedia);
    }

    protected function createCategories(ObjectManager $manager, array &$categories) : void {
        $array = [
            ['type' => 'Action', 'label' => 'Action'],
            ['type' => 'Comédie', 'label' => 'Comédie'],
            ['type' => 'Drame', 'label' => 'Drame'],
            ['type' => 'Horreur', 'label' => 'Horreur'],
            ['type' => 'Science-fiction', 'label' => 'Science-fiction'],
            ['type' => 'Thriller', 'label' => 'Thriller']
        ];

        foreach ($array as $element) {
            $category = new Category();
            $category->setName($element['type']);
            $category->setLabel($element['label']);
            $manager->persist($category);
            $categories[] = $category;
        }
    }

    protected function createLanguages(ObjectManager $manager, array &$languages): void
    {
        $array = [
            ['code' => 'fr', 'nom' => 'Français'],
            ['code' => 'en', 'nom' => 'Anglais'],
            ['code' => 'es', 'nom' => 'Espagnol'],
            ['code' => 'de', 'nom' => 'Allemand'],
            ['code' => 'it', 'nom' => 'Italien'],
        ];

        foreach ($array as $element) {
            $language = new Language();
            $language->setCode($element['code']);
            $language->setName($element['nom']);
            $manager->persist($language);
            $languages[] = $language;
        }
    }

    protected function createSeasons(ObjectManager $manager, Serie $media) : void {
        for ($i=0; $i < random_int(1, self::MAX_SEASONS); $i++) {
            $season = new Season();
            $season->setNumber('Saison '.($i+1));
            $season->setSerie($media);

            $manager->persist($season);
            $this->createEpisode($season, $manager);
        }
    }

    protected function createEpisode(Season $season, ObjectManager $manager) : void {
        for ($i = 0; $i < random_int(1, self::MAX_EPISODES); $i++) {
            $episode = new Episode();
            $episode->setTitle('Episode '. ($i + 1));
            $episode->setDuration(random_int(10, 60));
            $episode->setReleasedAt(new DateTimeImmutable());
            $episode->setSeason($season);

            $manager->persist($episode);
        }
    }

    protected function createComments(ObjectManager $manager, array $medias, array $users) : void {
        foreach($medias as $media) {
            for($i=0; $i < random_int(1, self::MAX_COMMENTS_PER_MEDIA); $i++) {
                $comment = new Comment();
                $comment->setPublisher($users[array_rand($users)]);
                $comment->setContent("Commentaire ".$i);

                $comment->setStatus(random_int(0,1) === 1 ? CommentStatusEnum::VALIDATED : CommentStatusEnum::WAITING);
                $comment->setMedia($media);

                $shouldHaveParent = random_int(0, 5) < 2;
                if($shouldHaveParent) {
                    $parentComment = new Comment();
                    $parentComment->setPublisher($users[array_rand($users)]);
                    $parentComment->setContent("Commentaire parent");
                    $parentComment->setStatus(random_int(0, 1) === 1 ? CommentStatusEnum::VALIDATED : CommentStatusEnum::WAITING);
                    $parentComment->setMedia($media);
                    $comment->setParentComment($parentComment);

                    $manager->persist($parentComment);
                }
                $manager->persist($comment);
            }
        }
    }

    protected function linkMediaToCategories(array $medias, array $categories): void {
        foreach ($medias as $media) {
            for ($i = 0; $i < random_int(1, self::MAX_CATEGORY_PER_MEDIA); $i++) {
                $media->addCategory($categories[array_rand($categories)]);
            }
        }
    }

    protected function linkMediaToLanguages(array $medias, array $languages): void {
        foreach ($medias as $media) {
            for ($i = 0; $i < random_int(1, self::MAX_LANGUAGE_PER_MEDIA); $i++) {
                $media->addLanguage($languages[array_rand($languages)]);
            }
        }
    }

    protected function linkSubscriptionToUsers(array $users, array $subscriptions, ObjectManager $manager): void {
        foreach ($users as $user) {
            $sub = $subscriptions[array_rand($subscriptions)];

            for ($i = 0; $i < random_int(1, self::MAX_SUBSCRIPTIONS_HISTORY_PER_USER); $i++) {
                $history = new SubscriptionHistory();
                $history->setSubscriber($user);
                $history->setSubscription($sub);
                $history->setStartAt(new DateTimeImmutable());
                $history->setEndAt(new DateTimeImmutable());
                $manager->persist($history);
            }
        }
    }

    

}
