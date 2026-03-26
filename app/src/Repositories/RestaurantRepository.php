<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Restaurant;
use App\Models\RestaurantUpsertData;
use App\Repositories\Interfaces\IRestaurantRepository;
use PDO;

/**
 * Repository for Restaurant database operations.
 */
class RestaurantRepository implements IRestaurantRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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
        $stmt = $this->pdo->prepare('
            SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.IsActive = 1
            ORDER BY r.RestaurantId ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Restaurant::class, 'fromRow'], $rows);
    }

    /**
     * Returns a single restaurant by ID with its card image path, or null if not found.
     *
     * Detail section images are now stored in RestaurantImage and fetched separately.
     */
    public function findById(int $id): ?Restaurant
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, ma.FilePath AS ImagePath
            FROM Restaurant r
            LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
            WHERE r.RestaurantId = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? Restaurant::fromRow($row) : null;
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([Restaurant::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(RestaurantUpsertData $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Restaurant
            (Name, AddressLine, City, Stars, CuisineType, DescriptionHtml, ImageAssetId, IsActive,
             Phone, Email, Website, AboutText, ChefName, ChefText, MenuDescription,
             LocationDescription, MapEmbedUrl, MichelinStars, SeatsPerSession, DurationMinutes,
             SpecialRequestsNote, CreatedAtUtc)
            VALUES
            (:name, :address, :city, :stars, :cuisine, :desc, :imageId, :active,
             :phone, :email, :website, :about, :chef, :chefText, :menu,
             :location, :mapUrl, :michelin, :seats, :duration, :special, NOW())'
        );
        $stmt->execute([
            ':name' => $data->name, ':address' => $data->addressLine, ':city' => $data->city,
            ':stars' => $data->stars, ':cuisine' => $data->cuisineType, ':desc' => $data->descriptionHtml,
            ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
            ':phone' => $data->phone, ':email' => $data->email, ':website' => $data->website,
            ':about' => $data->aboutText, ':chef' => $data->chefName, ':chefText' => $data->chefText,
            ':menu' => $data->menuDescription, ':location' => $data->locationDescription,
            ':mapUrl' => $data->mapEmbedUrl, ':michelin' => $data->michelinStars,
            ':seats' => $data->seatsPerSession, ':duration' => $data->durationMinutes,
            ':special' => $data->specialRequestsNote,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, RestaurantUpsertData $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Restaurant SET
            Name=:name, AddressLine=:address, City=:city, Stars=:stars, CuisineType=:cuisine,
            DescriptionHtml=:desc, ImageAssetId=:imageId, IsActive=:active,
            Phone=:phone, Email=:email, Website=:website, AboutText=:about,
            ChefName=:chef, ChefText=:chefText, MenuDescription=:menu,
            LocationDescription=:location, MapEmbedUrl=:mapUrl, MichelinStars=:michelin,
            SeatsPerSession=:seats, DurationMinutes=:duration, SpecialRequestsNote=:special
            WHERE RestaurantId=:id'
        );
        $stmt->execute([
            ':id' => $id, ':name' => $data->name, ':address' => $data->addressLine, ':city' => $data->city,
            ':stars' => $data->stars, ':cuisine' => $data->cuisineType, ':desc' => $data->descriptionHtml,
            ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
            ':phone' => $data->phone, ':email' => $data->email, ':website' => $data->website,
            ':about' => $data->aboutText, ':chef' => $data->chefName, ':chefText' => $data->chefText,
            ':menu' => $data->menuDescription, ':location' => $data->locationDescription,
            ':mapUrl' => $data->mapEmbedUrl, ':michelin' => $data->michelinStars,
            ':seats' => $data->seatsPerSession, ':duration' => $data->durationMinutes,
            ':special' => $data->specialRequestsNote,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE Restaurant SET IsActive = 0 WHERE RestaurantId = :id');
        $stmt->execute([':id' => $id]);
    }
}
