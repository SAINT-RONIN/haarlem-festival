<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Artist;
use App\Models\ArtistUpsertData;
use App\Repositories\Interfaces\IArtistRepository;
use App\Services\Interfaces\ICmsArtistsService;

class CmsArtistsService implements ICmsArtistsService
{
    public function __construct(
        private readonly IArtistRepository $artistRepository,
    ) {}

    /** @return Artist[] */
    public function getArtists(?string $search): array
    {
        return $this->artistRepository->findAll($search);
    }

    public function findById(int $id): ?Artist
    {
        return $this->artistRepository->findById($id);
    }

    /** @return array<string, string> */
    public function validateForCreate(ArtistUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** @return array<string, string> */
    public function validateForUpdate(int $id, ArtistUpsertData $data): array
    {
        return $this->validate($data);
    }

    public function createArtist(ArtistUpsertData $data): int
    {
        return $this->artistRepository->create($data);
    }

    public function updateArtist(int $id, ArtistUpsertData $data): void
    {
        $this->artistRepository->update($id, $data);
    }

    public function deleteArtist(int $id): void
    {
        $this->artistRepository->delete($id);
    }

    /** @return array<string, string> */
    private function validate(ArtistUpsertData $data): array
    {
        $errors = [];
        if ($data->name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($data->style === '') {
            $errors['style'] = 'Style is required.';
        }
        if ($data->bioHtml === '') {
            $errors['bioHtml'] = 'Bio is required.';
        }
        return $errors;
    }
}
