<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Restaurant;
use App\DTOs\Cms\RestaurantUpsertData;
use App\Repositories\Interfaces\IRestaurantRepository;

/**
 * Repository for Restaurant database operations.
 */
class RestaurantRepository extends BaseRepository implements IRestaurantRepository
{
    /**
     * Returns all active restaurants with their image path from MediaAsset.
     *
     * Uses LEFT JOIN so restaurants without an image are still included.
     * The ImagePath column comes from MediaAsset.FilePath.
     *
     * @return Restaurant[]
     */
    public function findAllActive(): array
    {
        return $this->fetchAll(
            'SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.IsActive = 1
            ORDER BY r.RestaurantId ASC',
            [],
            fn(array $row) => Restaurant::fromRow($row),
        );
    }

    /**
     * Returns a single restaurant by ID with its card image path, or null if not found.
     *
     * Detail section images are now stored in RestaurantImage and fetched separately.
     */
    public function findById(int $id): ?Restaurant
    {
        return $this->fetchOne(
            'SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.RestaurantId = :id
            LIMIT 1',
            [':id' => $id],
            fn(array $row) => Restaurant::fromRow($row),
        );
    }

    /**
     * Returns all restaurants (including inactive), optionally filtered by name search.
     * Orders by Name ASC.
     *
     * @return Restaurant[]
     */
    public function findAll(?string $search = null): array
    {
        $sql = 'SELECT r.*, ma.FilePath AS ImagePath FROM Restaurant r
                LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId';
        $params = [];
        if ($search !== null && $search !== '') {
            $sql .= ' WHERE r.Name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY r.Name ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => Restaurant::fromRow($row));
    }

    /** Inserts a new restaurant row from the CMS restaurant form data. */
    public function create(RestaurantUpsertData $data): int
    {
        return $this->executeInsert(
            'INSERT INTO Restaurant
            (Name, AddressLine, City, Stars, CuisineType, DescriptionHtml, ImageAssetId, IsActive,
             Phone, Email, Website, AboutText, ChefName, ChefText, MenuDescription,
             LocationDescription, MapEmbedUrl, MichelinStars, SeatsPerSession, DurationMinutes,
             SpecialRequestsNote, CreatedAtUtc)
            VALUES
            (:name, :address, :city, :stars, :cuisine, :desc, :imageId, :active,
             :phone, :email, :website, :about, :chef, :chefText, :menu,
             :location, :mapUrl, :michelin, :seats, :duration, :special, NOW())',
            [
                ':name' => $data->name, ':address' => $data->addressLine, ':city' => $data->city,
                ':stars' => $data->stars, ':cuisine' => $data->cuisineType, ':desc' => $data->descriptionHtml,
                ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
                ':phone' => $data->phone, ':email' => $data->email, ':website' => $data->website,
                ':about' => $data->aboutText, ':chef' => $data->chefName, ':chefText' => $data->chefText,
                ':menu' => $data->menuDescription, ':location' => $data->locationDescription,
                ':mapUrl' => $data->mapEmbedUrl, ':michelin' => $data->michelinStars,
                ':seats' => $data->seatsPerSession, ':duration' => $data->durationMinutes,
                ':special' => $data->specialRequestsNote,
            ],
        );
    }

    /** Updates an existing restaurant row from the CMS restaurant form data. */
    public function update(int $id, RestaurantUpsertData $data): void
    {
        $this->execute(
            'UPDATE Restaurant SET
            Name=:name, AddressLine=:address, City=:city, Stars=:stars, CuisineType=:cuisine,
            DescriptionHtml=:desc, ImageAssetId=:imageId, IsActive=:active,
            Phone=:phone, Email=:email, Website=:website, AboutText=:about,
            ChefName=:chef, ChefText=:chefText, MenuDescription=:menu,
            LocationDescription=:location, MapEmbedUrl=:mapUrl, MichelinStars=:michelin,
            SeatsPerSession=:seats, DurationMinutes=:duration, SpecialRequestsNote=:special
            WHERE RestaurantId=:id',
            [
                ':id' => $id, ':name' => $data->name, ':address' => $data->addressLine, ':city' => $data->city,
                ':stars' => $data->stars, ':cuisine' => $data->cuisineType, ':desc' => $data->descriptionHtml,
                ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
                ':phone' => $data->phone, ':email' => $data->email, ':website' => $data->website,
                ':about' => $data->aboutText, ':chef' => $data->chefName, ':chefText' => $data->chefText,
                ':menu' => $data->menuDescription, ':location' => $data->locationDescription,
                ':mapUrl' => $data->mapEmbedUrl, ':michelin' => $data->michelinStars,
                ':seats' => $data->seatsPerSession, ':duration' => $data->durationMinutes,
                ':special' => $data->specialRequestsNote,
            ],
        );
    }

    /** Soft-deletes a restaurant by marking it inactive instead of removing the row. */
    public function delete(int $id): void
    {
        $this->execute('UPDATE Restaurant SET IsActive = 0 WHERE RestaurantId = :id', [':id' => $id]);
    }
}
