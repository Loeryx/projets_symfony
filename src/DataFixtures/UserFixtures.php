<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\Playlist;
use App\Entity\PlaylistMedia;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Serie;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const MAX_USERS = 10;
    public const PLAYLIST_PER_USER = 5;
    public const MAX_SUBSCRIPTIONS = 3;
    public const MAX_MEDIA = 100;
    public const MAX_MEDIA_PER_PLAYLIST = 5;

    public function load(ObjectManager $manager): void
    {
        $users = [];
        $medias = [];
        $playlists = [];
        
        for ($i=0; $i < SELF::MAX_USERS; $i++) {
            $user = $this->createUser($i, $manager);
            $users[] = $user;

            for ($k = 0; $k < random_int(1, self::PLAYLIST_PER_USER); $k++) {
                $playlists = $this->createPlaylists($user, $manager, $playlists);
            }
        }

        $manager->flush();
    }

    protected function createUser(int $i, ObjectManager $manager) : User
    {
        $user = new User();
        $user->setUsername("test_{$i}");
        $user->setPassword("test");
        $user->setEmail("test_{$i}@gmail.com");
        $manager->persist($user);

        return $user;
    }

    protected function createPlaylists(User $user, ObjectManager $manager, array $playlists) : array
    {
        $playlist = new Playlist();
        $playlist->setName("playlist1");
        $playlist->setCreatedAt(new \DateTimeImmutable());
        $playlist->setUpdatedAt(new \DateTimeImmutable());
        $playlist->setCreator($user);
        $manager->persist($playlist);
        $playlists[] = $playlist;

        return $playlists;
    }

    protected function createMediaAndLinkToPlaylists(ObjectManager $manager, array $playlists): void
    {
        for ($j = 0; $j < self::MAX_MEDIA; $j++) {
            $media = $this->createMedia($j, $manager);

            for ($l = 0; $l < random_int(1, self::MAX_MEDIA_PER_PLAYLIST); $l++) {
                $this->linkMediaToPlaylist($media, $playlists, $manager);
            }
        }
    }

    protected function createMedia(int $j, ObjectManager $manager) : Media 
    {
        $media = random_int(0, 1) === 0 ? new Movie() : new Serie();

        $media->setTitle("Film {$j}");
        $media->setLongDescription('Longue description');
        $media->setShortDescription('Short description');
        $media->setCoverImage('http://');
        $media->setReleaseDate(new \DateTime(datetime: "+7 days"));
        $manager->persist($media);

        return $media;
    }

    protected function linkMediaToPlaylist(Media $media, array $playlists, ObjectManager $manager): void
    {
        $playlistMedia = new PlaylistMedia();
        $playlistMedia->setMedia($media);
        $playlistMedia->setAddedAt(new \DateTimeImmutable());
        $playlistMedia->setPlaylist($playlists[array_rand($playlists)]);

        $manager->persist($playlistMedia);
    }
}
