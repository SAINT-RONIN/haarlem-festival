<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CheckoutController;
use App\Controllers\CmsAuthController;
use App\Controllers\CmsDashboardController;
use App\Controllers\CmsEventsController;
use App\Controllers\CmsMediaController;
use App\Controllers\CmsOrdersController;
use App\Controllers\CmsUsersController;
use App\Controllers\HistoryController;
use App\Controllers\HomeController;
use App\Controllers\JazzController;
use App\Controllers\ProgramController;
use App\Controllers\RestaurantController;
use App\Controllers\StorytellingController;
use App\Http\Requests\StripeWebhookRequestFactory;
use App\Infrastructure\CheckoutRuntimeConfig;
use App\Infrastructure\Database;
use App\Infrastructure\EmailService;
use App\Infrastructure\StripeService;
use App\Repositories\CmsContentRepository;
use App\Repositories\CmsOrdersRepository;
use App\Repositories\CmsRepository;
use App\Repositories\CmsUsersRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\EventTypeRepository;
use App\Repositories\MediaAssetRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\ArtistAlbumRepository;
use App\Repositories\ArtistGalleryImageRepository;
use App\Repositories\ArtistHighlightRepository;
use App\Repositories\ArtistLineupMemberRepository;
use App\Repositories\ArtistTrackRepository;
use App\Repositories\CuisineTypeRepository;
use App\Repositories\EventGalleryImageRepository;
use App\Repositories\EventHighlightRepository;
use App\Repositories\PageGalleryImageRepository;
use App\Repositories\RestaurantImageRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\StripeWebhookEventRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\PriceTierRepository;
use App\Repositories\ScheduleDayConfigRepository;
use App\Repositories\VenueRepository;
use App\Services\CmsDashboardService;
use App\Services\CmsEventsService;
use App\Services\CmsEditService;
use App\Services\CmsOrdersService;
use App\Services\CmsUsersService;
use App\Services\CheckoutService;
use App\Services\HistoricalLocationService;
use App\Services\HistoryService;
use App\Services\HomeService;
use App\Services\JazzArtistDetailService;
use App\Services\JazzService;
use App\Services\MediaAssetService;
use App\Services\ProgramService;
use App\Services\RestaurantService;
use App\Services\ScheduleService;
use App\Services\AuthService;
use App\Services\CaptchaService;
use App\Services\SessionService;
use App\Services\CmsPageContentService;
use App\Services\StorytellingDetailService;
use App\Services\StorytellingService;

return static function (string $controllerClass): object {

    // ── Repositories (created once, reused everywhere) ──

    $cmsRepository = new CmsRepository();
    $mediaAssetRepository = new MediaAssetRepository();
    $eventRepository = new EventRepository();
    $eventSessionRepository = new EventSessionRepository();
    $eventSessionLabelRepository = new EventSessionLabelRepository();
    $eventSessionPriceRepository = new EventSessionPriceRepository();
    $eventTypeRepository = new EventTypeRepository();
    $venueRepository = new VenueRepository();
    $priceTierRepository = new PriceTierRepository();
    $scheduleDayConfigRepository = new ScheduleDayConfigRepository();
    $restaurantRepository = new RestaurantRepository();
    $restaurantImageRepository = new RestaurantImageRepository();
    $artistAlbumRepository = new ArtistAlbumRepository();
    $artistTrackRepository = new ArtistTrackRepository();
    $artistLineupMemberRepository = new ArtistLineupMemberRepository();
    $artistHighlightRepository = new ArtistHighlightRepository();
    $artistGalleryImageRepository = new ArtistGalleryImageRepository();
    $eventHighlightRepository = new EventHighlightRepository();
    $eventGalleryImageRepository = new EventGalleryImageRepository();
    $pageGalleryImageRepository = new PageGalleryImageRepository();
    $cuisineTypeRepository = new CuisineTypeRepository();
    $userAccountRepository = new UserAccountRepository();
    $passwordResetTokenRepository = new PasswordResetTokenRepository();
    $programRepository = new ProgramRepository();
    $orderRepository = new OrderRepository();
    $orderItemRepository = new OrderItemRepository();
    $paymentRepository = new PaymentRepository();
    $stripeWebhookEventRepository = new StripeWebhookEventRepository();

    // ── Services (built from shared repositories) ──

    $sessionService = new SessionService();
    $cmsContent = new CmsContentRepository($cmsRepository, $mediaAssetRepository);
    $cmsPageContentService = new CmsPageContentService($cmsContent);

    $cmsEventsService = new CmsEventsService(
        $eventRepository,
        $eventSessionRepository,
        $eventSessionLabelRepository,
        $eventSessionPriceRepository,
        $eventTypeRepository,
        $venueRepository,
        $priceTierRepository,
        $scheduleDayConfigRepository,
    );

    $scheduleService = new ScheduleService(
        $cmsContent,
        $eventSessionRepository,
        $eventSessionLabelRepository,
        $eventSessionPriceRepository,
        $eventTypeRepository,
        $scheduleDayConfigRepository,
    );

    $programService = new ProgramService();

    // ── Controller wiring ──

    return match ($controllerClass) {
        HomeController::class => new HomeController(
            new HomeService(
                $eventTypeRepository,
                $venueRepository,
                $restaurantRepository,
                $eventSessionRepository,
                $cmsContent,
            ),
            $sessionService,
        ),
        RestaurantController::class => new RestaurantController(
            new RestaurantService(
                $cmsContent,
                $restaurantRepository,
                $restaurantImageRepository,
                $cuisineTypeRepository,
            ),
            $sessionService,
        ),
        StorytellingController::class => new StorytellingController(
            new StorytellingService(
                $cmsContent,
                $pageGalleryImageRepository,
            ),
            new StorytellingDetailService(
                $cmsContent,
                $eventRepository,
                $eventSessionRepository,
                $eventSessionLabelRepository,
                $mediaAssetRepository,
                $eventHighlightRepository,
                $eventGalleryImageRepository,
            ),
            $sessionService,
            $scheduleService,
        ),
        JazzController::class => new JazzController(
            new JazzService(
                $cmsContent,
            ),
            new JazzArtistDetailService(
                $cmsContent,
                $eventRepository,
                $artistAlbumRepository,
                $artistTrackRepository,
                $artistLineupMemberRepository,
                $artistHighlightRepository,
                $artistGalleryImageRepository,
            ),
            $cmsPageContentService,
            $sessionService,
            $scheduleService,
        ),
        CmsEventsController::class => new CmsEventsController(
            $cmsEventsService,
            $sessionService,
        ),
        AuthController::class => new AuthController(
            new AuthService(
                $userAccountRepository,
                $passwordResetTokenRepository,
                new EmailService(),
            ),
            $sessionService,
            new CaptchaService(),
        ),
        CmsDashboardController::class => new CmsDashboardController(
            $sessionService,
            new CmsDashboardService($cmsRepository),
            new CmsEditService(
                $cmsRepository,
                $mediaAssetRepository,
                $eventRepository,
            ),
            new MediaAssetService($mediaAssetRepository),
        ),
        CheckoutController::class => new CheckoutController(
            $programService,
            $cmsPageContentService,
            $sessionService,
            new CheckoutService(
                $programRepository,
                $orderRepository,
                $orderItemRepository,
                $paymentRepository,
                $stripeWebhookEventRepository,
                new StripeService(
                    (string)(getenv('STRIPE_SECRET_KEY') !== false ? getenv('STRIPE_SECRET_KEY') : ''),
                    (string)(getenv('STRIPE_WEBHOOK_SECRET') !== false ? getenv('STRIPE_WEBHOOK_SECRET') : ''),
                ),
                new CheckoutRuntimeConfig(
                    (string)getenv('APP_URL'),
                    (float)(getenv('VAT_RATE') !== false ? getenv('VAT_RATE') : 0.21),
                ),
                Database::getConnection(),
            ),
            new StripeWebhookRequestFactory(),
        ),
        HistoryController::class => new HistoryController(
            new HistoryService(
                $cmsPageContentService,
            ),
            new HistoricalLocationService(),
            $cmsPageContentService,
            $sessionService,
            $scheduleService

        ),
        CmsAuthController::class => new CmsAuthController(
            new AuthService(
                $userAccountRepository,
                $passwordResetTokenRepository,
                new EmailService(),
            ),
            $sessionService,
        ),
        CmsMediaController::class => new CmsMediaController(
            new MediaAssetService($mediaAssetRepository),
            $sessionService,
        ),
        ProgramController::class => new ProgramController(
            $programService,
            $cmsPageContentService,
            $sessionService,
        ),
        CmsOrdersController::class => new CmsOrdersController(
            new CmsOrdersService(new CmsOrdersRepository(Database::getConnection())),
            $sessionService,
        ),
        CmsUsersController::class => new CmsUsersController(
            new CmsUsersService(new CmsUsersRepository(Database::getConnection())),
            $sessionService,
        ),
        default => new $controllerClass(),
    };
};