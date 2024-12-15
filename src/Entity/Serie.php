<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie extends Media
{
    /**
     * @var Collection<int, Season>
     */
    #[ORM\OneToMany(targetEntity: Season::class, mappedBy: 'serie')]
    private Collection $seasons;

    public function __construct()
    {
        parent::__construct();
        $this->seasons = new ArrayCollection();
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season)
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
            $season->setSerie($this);
        }
        return $this;
    }

    public function removeSeason(Season $season)
    {
        if ($this->seasons->removeElement($season)) {
            if ($season->getSerie() === $this) {
                $season->setSerie(null);
            }
        }
        return $this;
    }
}
