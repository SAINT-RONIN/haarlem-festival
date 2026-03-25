<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CheckoutController;
use App\Controllers\CmsArtistsController;
use App\Controllers\CmsAuthController;
use App\Controllers\CmsDashboardController;
use App\Controllers\CmsEventsController;
use App\Controllers\CmsMediaController;
use App\Controllers\CmsOrdersController;
use App\Controllers\CmsRestaurantsController;
use App\Controllers\CmsUsersController;
use App\Controllers\HistoryController;
use App\Controllers\HomeController;
use App\Controllers\JazzController;
use App\Controllers\ProgramController;
use App\Controllers\RestaurantController;
use App\Controllers\ScheduleApiController;
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
use App\Repositories\ArtistRepository;
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
use App\Repositories\PassTypeRepository;
use App\Repositories\PriceTierRepository;
use App\Repositories\ScheduleDayConfigRepository;
use App\Repositories\VenueRepository;
use App\Repositories\CheckoutContentRepository;
use App\Repositories\GlobalContentRepository;
use App\Repositories\HistoricalLocationContentRepository;
use App\Repositories\HistoryContentRepository;
use App\Repositories\JazzContentRepository;
use App\Repositories\RestaurantContentRepository;
use App\Repositories\ScheduleContentRepository;
use App\Repositories\StorytellingContentRepository;
use App\Services\CmsArtistsService;
use App\Services\CmsDashboardService;
use App\Services\CmsRestaurantsService;
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
use App\Helpers\ScheduleDayVisibilityResolver;
use App\Services\GlobalUiContentLoader;
use App\Services\StorytellingService;

/**
 * Lazy dependency container — only creates the repositories and services
 * that the matched controller actually needs. Shared dependencies are
 * cached via the $make() helper so they're created at most once per request.
 */
return static function (string $controllerClass): object {

    // Lazy singleton factory — caches each dependency on first creation
    $singletons = [];
    $make = static function (string $key, callable $factory) use (&$singletons): object {
        return $singletons[$key] ??= $factory();
    };

    // SessionService is needed by almost every controller
    $sessionService = new SessionService();

    // ── Shared PDO connection (lazy singleton) ──

    $pdo = fn() => $make('pdo', fn() => Database::getConnection());

    // ── Lazy repository accessors (shared across multiple controllers) ──

    $cmsRepo            = fn() => $make('cmsRepo', fn() => new CmsRepository($pdo()));
    $mediaAssetRepo     = fn() => $make('mediaAssetRepo', fn() => new MediaAssetRepository($pdo()));
    $eventRepo          = fn() => $make('eventRepo', fn() => new EventRepository($pdo()));
    $eventSessionRepo   = fn() => $make('eventSessionRepo', fn() => new EventSessionRepository($pdo()));
    $eventSessionLabel  = fn() => $make('eventSessionLabel', fn() => new EventSessionLabelRepository($pdo()));
    $eventSessionPrice  = fn() => $make('eventSessionPrice', fn() => new EventSessionPriceRepository($pdo()));
    $eventTypeRepo      = fn() => $make('eventTypeRepo', fn() => new EventTypeRepository($pdo()));
    $venueRepo          = fn() => $make('venueRepo', fn() => new VenueRepository($pdo()));
    $scheduleDayConfig  = fn() => $make('scheduleDayConfig', fn() => new ScheduleDayConfigRepository($pdo()));
    $restaurantRepo     = fn() => $make('restaurantRepo', fn() => new RestaurantRepository($pdo()));
    $userAccountRepo    = fn() => $make('userAccountRepo', fn() => new UserAccountRepository($pdo()));
    $resetTokenRepo     = fn() => $make('resetTokenRepo', fn() => new PasswordResetTokenRepository($pdo()));
    $programRepo        = fn() => $make('programRepo', fn() => new ProgramRepository($pdo()));
    $orderRepo          = fn() => $make('orderRepo', fn() => new OrderRepository($pdo()));
    $orderItemRepo      = fn() => $make('orderItemRepo', fn() => new OrderItemRepository($pdo()));
    $paymentRepo        = fn() => $make('paymentRepo', fn() => new PaymentRepository($pdo()));

    // ── Lazy service accessors (shared across multiple controllers) ──

    $cmsContent = fn() => $make('cmsContent', fn() => new CmsContentRepository($cmsRepo(), $mediaAssetRepo()));
    $cmsPageContent = fn() => $make('cmsPageContent', fn() => new CmsPageContentService($cmsContent()));
    $globalUiLoader = fn() => $make('globalUiLoader', fn() => new GlobalUiContentLoader($globalContentRepo()));

    // ── Domain content repositories (wrap CmsContentRepository with typed returns) ──

    $globalContentRepo     = fn() => $make('globalContentRepo', fn() => new GlobalContentRepository($cmsContent()));
    $scheduleContentRepo   = fn() => $make('scheduleContentRepo', fn() => new ScheduleContentRepository($cmsContent()));
    $checkoutContentRepo   = fn() => $make('checkoutContentRepo', fn() => new CheckoutContentRepository($cmsContent()));
    $jazzContentRepo       = fn() => $make('jazzContentRepo', fn() => new JazzContentRepository($cmsContent()));
    $storyContentRepo      = fn() => $make('storyContentRepo', fn() => new StorytellingContentRepository($cmsContent()));
    $restaurantContentRepo = fn() => $make('restaurantContentRepo', fn() => new RestaurantContentRepository($cmsContent()));
    $historyContentRepo    = fn() => $make('historyContentRepo', fn() => new HistoryContentRepository($cmsContent()));
    $histLocContentRepo    = fn() => $make('histLocContentRepo', fn() => new HistoricalLocationContentRepository($cmsContent()));

    $visibilityResolver = fn() => $make('visibilityResolver', fn() => new ScheduleDayVisibilityResolver($scheduleDayConfig()));

    $scheduleService = fn() => $make('scheduleService', fn() => new ScheduleService(
        $scheduleContentRepo(),
        $eventSessionRepo(),
        $eventSessionLabel(),
        $eventSessionPrice(),
        $eventTypeRepo(),
        $visibilityResolver(),
    ));

    $programService = fn() => $make('programService', fn() => new ProgramService(
        $programRepo(),
        $eventSessionRepo(),
        $eventSessionPrice(),
    ));

    $authService = fn() => $make('authService', fn() => new AuthService(
        $pdo(),
        $userAccountRepo(),
        $resetTokenRepo(),
        new EmailService(),
    ));

    // ── Controller wiring — each arm only creates what it needs ──

    return match ($controllerClass) {
        HomeController::class => new HomeController(
            new HomeService(
                $eventTypeRepo(),
                $venueRepo(),
                $restaurantRepo(),
                $eventSessionRepo(),
                $cmsContent(),
                $globalContentRepo(),
                $globalUiLoader(),
            ),
            $sessionService,
        ),
        RestaurantController::class => new RestaurantController(
            new RestaurantService(
                $globalContentRepo(),
                $restaurantContentRepo(),
                $restaurantRepo(),
                new RestaurantImageRepository($pdo()),
                new CuisineTypeRepository($pdo()),
                $globalUiLoader(),
            ),
            $sessionService,
        ),
        StorytellingController::class => new StorytellingController(
            new StorytellingService(
                $globalContentRepo(),
                $storyContentRepo(),
                $globalUiLoader(),
            ),
            new StorytellingDetailService(
                $storyContentRepo(),
                $eventRepo(),
                $eventSessionRepo(),
                $eventSessionLabel(),
                $mediaAssetRepo(),
                $globalUiLoader(),
            ),
            $sessionService,
            $scheduleService(),
        ),
        JazzController::class => new JazzController(
            new JazzService(
                $globalContentRepo(),
                $jazzContentRepo(),
                new PassTypeRepository($pdo()),
                $globalUiLoader(),
            ),
            new JazzArtistDetailService(
                $jazzContentRepo(),
                $eventRepo(),
                new ArtistAlbumRepository($pdo()),
                new ArtistTrackRepository($pdo()),
                new ArtistLineupMemberRepository($pdo()),
                new ArtistHighlightRepository($pdo()),
                new ArtistGalleryImageRepository($pdo()),
            ),
            $sessionService,
            $scheduleService(),
        ),
        CmsEventsController::class => new CmsEventsController(
            new CmsEventsService(
                $pdo(),
                $eventRepo(),
                $eventSessionRepo(),
                $eventSessionLabel(),
                $eventSessionPrice(),
                $eventTypeRepo(),
                $venueRepo(),
                new PriceTierRepository($pdo()),
                $scheduleDayConfig(),
                $orderItemRepo(),
                $visibilityResolver(),
            ),
            $sessionService,
            new CmsArtistsService(new ArtistRepository($pdo())),
            new CmsRestaurantsService($restaurantRepo()),
        ),
        AuthController::class => new AuthController(
            $authService(),
            $sessionService,
            new CaptchaService(),
        ),
        CmsDashboardController::class => new CmsDashboardController(
            $sessionService,
            new CmsDashboardService($cmsRepo()),
            new CmsEditService(
                $cmsRepo(),
                $mediaAssetRepo(),
                $eventRepo(),
            ),
            new MediaAssetService($mediaAssetRepo()),
        ),
        CheckoutController::class => new CheckoutController(
            $programService(),
            $checkoutContentRepo(),
            $sessionService,
            new CheckoutService(
                $programRepo(),
                $orderRepo(),
                $orderItemRepo(),
                $paymentRepo(),
                $eventSessionRepo(),
                new StripeWebhookEventRepository($pdo()),
                new StripeService(
                    (string)(getenv('STRIPE_SECRET_KEY') !== false ? getenv('STRIPE_SECRET_KEY') : ''),
                    (string)(getenv('STRIPE_WEBHOOK_SECRET') !== false ? getenv('STRIPE_WEBHOOK_SECRET') : ''),
                ),
                new CheckoutRuntimeConfig(
                    (string)getenv('APP_URL'),
                    (float)(getenv('VAT_RATE') !== false ? getenv('VAT_RATE') : 0.21),
                ),
                $pdo(),
            ),
            new StripeWebhookRequestFactory(),
        ),
        HistoryController::class => new HistoryController(
            new HistoryService(
                $globalContentRepo(),
                $historyContentRepo(),
                $globalUiLoader(),
            ),
            new HistoricalLocationService(
                $cmsContent(),
                $globalContentRepo(),
                $histLocContentRepo(),
                $globalUiLoader(),
            ),
            $sessionService,
            $scheduleService(),
        ),
        CmsAuthController::class => new CmsAuthController(
            $authService(),
            $sessionService,
        ),
        CmsMediaController::class => new CmsMediaController(
            new MediaAssetService($mediaAssetRepo()),
            $sessionService,
        ),
        ProgramController::class => new ProgramController(
            $programService(),
            $checkoutContentRepo(),
            $sessionService,
        ),
        CmsOrdersController::class => new CmsOrdersController(
            new CmsOrdersService(new CmsOrdersRepository($pdo())),
            $sessionService,
        ),
        CmsUsersController::class => new CmsUsersController(
            new CmsUsersService(new CmsUsersRepository($pdo()), $userAccountRepo()),
            $sessionService,
        ),
        CmsRestaurantsController::class => new CmsRestaurantsController(
            new CmsRestaurantsService($restaurantRepo()),
            $sessionService,
        ),
        CmsArtistsController::class => new CmsArtistsController(
            new CmsArtistsService(new ArtistRepository($pdo())),
            $sessionService,
        ),
        ScheduleApiController::class => new ScheduleApiController(
            $scheduleService(),
        ),
        default => new $controllerClass(),
    };
};
