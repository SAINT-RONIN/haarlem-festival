<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\CheckoutController;
use App\Controllers\CmsArtistsController;
use App\Controllers\CmsJazzCardsController;
use App\Controllers\CmsScheduleDaysController;
use App\Controllers\CmsVenuesController;
use App\Controllers\EmployeeScannerController;
use App\Controllers\OrderHistoryController;
use App\Repositories\OrderHistoryRepository;
use App\Controllers\CmsAuthController;
use App\Controllers\CmsDashboardController;
use App\Controllers\CmsPageEditorController;
use App\Controllers\CmsPageImageController;
use App\Controllers\CmsEventsController;
use App\Controllers\CmsMediaController;
use App\Controllers\CmsOrdersController;
use App\Controllers\CmsUsersController;
use App\Controllers\HistoryController;
use App\Controllers\HomeController;
use App\Controllers\JazzController;
use App\Controllers\ProgramController;
use App\Controllers\RestaurantApiController;
use App\Controllers\ScannerController;
use App\Controllers\RestaurantController;
use App\Controllers\ScheduleApiController;
use App\Controllers\StorytellingController;
use App\Services\OrderCapacityRestorer;
use App\Http\Requests\StripeWebhookRequestFactory;
use App\Infrastructure\CheckoutRuntimeConfig;
use App\Infrastructure\Database;
use App\Infrastructure\EmailService;
use App\Infrastructure\PdfAssetStorage;
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
use App\Repositories\TicketRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\ArtistDetailRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\ArtistAlbumRepository;
use App\Repositories\ArtistGalleryImageRepository;
use App\Repositories\ArtistHighlightRepository;
use App\Repositories\ArtistLineupMemberRepository;
use App\Repositories\ArtistTrackRepository;
use App\Repositories\ScannerRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\StripeWebhookEventRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\PassPurchaseRepository;
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
use App\Services\CmsEventsService;
use App\Services\CmsScheduleDayService;
use App\Services\CmsEditService;
use App\Services\CmsItemEnricher;
use App\Services\CmsPreviewUrlResolver;
use App\Services\CmsOrdersService;
use App\Services\CmsUsersService;
use App\Services\CheckoutService;
use App\Services\StripeWebhookHandler;
use App\Services\TicketScannerService;
use App\Services\HistoricalLocationService;
use App\Services\HistoryService;
use App\Services\HomeService;
use App\Services\JazzArtistDetailService;
use App\Services\JazzService;
use App\Services\MediaAssetService;
use App\Services\ProgramService;
use App\Services\RestaurantDetailService;
use App\Services\RestaurantReservationService;
use App\Services\RestaurantService;
use App\Services\ScheduleDayVisibilityResolver;
use App\Services\ScheduleService;
use App\Services\AuthService;
use App\Services\AccountService;
use App\Services\CaptchaService;
use App\Services\ScannerService;
use App\Services\SessionService;
use App\Services\StorytellingDetailService;
use App\Services\StorytellingService;
use App\Services\InvoiceFulfillmentService;
use App\Services\OrderHistoryService;
use App\Services\TicketFulfillmentService;
use App\Repositories\InvoiceRepository;
use App\Infrastructure\InvoicePdfGenerator;
use App\Infrastructure\PdfTicketGenerator;
use App\Infrastructure\QrCodeGenerator;
use App\Utils\TicketCodeGenerator;

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
    $userAccountRepo    = fn() => $make('userAccountRepo', fn() => new UserAccountRepository($pdo()));
    $resetTokenRepo     = fn() => $make('resetTokenRepo', fn() => new PasswordResetTokenRepository($pdo()));
    $programRepo        = fn() => $make('programRepo', fn() => new ProgramRepository($pdo()));
    $passTypeRepo       = fn() => $make('passTypeRepo', fn() => new PassTypeRepository($pdo()));
    $priceTierRepo      = fn() => $make('priceTierRepo', fn() => new PriceTierRepository($pdo()));
    $reservationRepo    = fn() => $make('reservationRepo', fn() => new ReservationRepository($pdo()));
    $passPurchaseRepo   = fn() => $make('passPurchaseRepo', fn() => new PassPurchaseRepository($pdo()));
    $orderRepo          = fn() => $make('orderRepo', fn() => new OrderRepository($pdo()));
    $orderItemRepo      = fn() => $make('orderItemRepo', fn() => new OrderItemRepository($pdo()));
    $paymentRepo        = fn() => $make('paymentRepo', fn() => new PaymentRepository($pdo()));
    $ticketRepo         = fn() => $make('ticketRepo', fn() => new TicketRepository($pdo()));
    $invoiceRepo        = fn() => $make('invoiceRepo', fn() => new InvoiceRepository($pdo()));

    // ── Lazy service accessors (shared across multiple controllers) ──

    $cmsContent = fn() => $make('cmsContent', fn() => new CmsContentRepository($cmsRepo(), $mediaAssetRepo()));

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
    $orderCapacityRestorer = fn() => $make('orderCapacityRestorer', fn() => new OrderCapacityRestorer($orderItemRepo(), $eventSessionRepo()));
    $cmsItemEnricher = fn() => $make('cmsItemEnricher', fn() => new CmsItemEnricher($mediaAssetRepo()));
    $cmsPreviewUrlResolver = fn() => $make('cmsPreviewUrlResolver', fn() => new CmsPreviewUrlResolver());
    $emailService = fn() => $make('emailService', fn() => new EmailService());
    $pdfAssetStorage = fn() => $make('pdfAssetStorage', fn() => new PdfAssetStorage($mediaAssetRepo()));
    $ticketFulfillmentService = fn() => $make('ticketFulfillmentService', fn() => new TicketFulfillmentService(
        $orderRepo(),
        $orderItemRepo(),
        $eventSessionRepo(),
        $ticketRepo(),
        $mediaAssetRepo(),
        $userAccountRepo(),
        $emailService(),
        $pdfAssetStorage(),
        new QrCodeGenerator(),
        new PdfTicketGenerator(),
        new TicketCodeGenerator(),
    ));

    $invoiceFulfillmentService = fn() => $make('invoiceFulfillmentService', fn() => new InvoiceFulfillmentService(
        $orderRepo(),
        $orderItemRepo(),
        $eventSessionRepo(),
        $invoiceRepo(),
        new InvoicePdfGenerator(),
        $emailService(),
        $pdfAssetStorage(),
    ));

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
        $eventSessionLabel(),
        $eventSessionPrice(),
        $checkoutContentRepo(),
        $passTypeRepo(),
        $priceTierRepo(),
        $reservationRepo(),
    ));

    $authService = fn() => $make('authService', fn() => new AuthService(
        $pdo(),
        $userAccountRepo(),
        $resetTokenRepo(),
        $emailService(),
    ));

    $accountService = fn() => $make('accountService', fn() => new AccountService(
        $userAccountRepo(),
        $emailService(),
        $pdo(),
    ));

    // ── Lazy service singletons shared across multiple controllers ──

    $cmsArtistsService = fn() => $make('cmsArtistsService', fn() => new CmsArtistsService(new ArtistRepository($pdo())));
    $cmsEventsService  = fn() => $make('cmsEventsService', fn() => new CmsEventsService(
        $pdo(),
        $eventRepo(),
        $eventSessionRepo(),
        $eventSessionLabel(),
        $eventSessionPrice(),
        $eventTypeRepo(),
        $venueRepo(),
        $priceTierRepo(),
        $orderItemRepo(),
        $cmsRepo(),
        $mediaAssetRepo(),
    ));
    $mediaAssetService = fn() => $make('mediaAssetService', fn() => new MediaAssetService($mediaAssetRepo()));
    $restaurantService = fn() => $make('restaurantService', fn() => new RestaurantService(
        $globalContentRepo(),
        $restaurantContentRepo(),
        $eventRepo(),
        $mediaAssetRepo(),
    ));

    // ── Controller wiring — each arm only creates what it needs ──

    return match ($controllerClass) {
        HomeController::class => new HomeController(
            new HomeService(
                $eventTypeRepo(),
                $venueRepo(),
                $eventSessionRepo(),
                $cmsContent(),
                $globalContentRepo(),
            ),
            $sessionService,
        ),
        RestaurantController::class => new RestaurantController(
            $restaurantService(),
            new RestaurantDetailService(
                $restaurantContentRepo(),
                $eventRepo(),
                $mediaAssetRepo(),
                $globalContentRepo(),
            ),
            new RestaurantReservationService(
                $eventRepo(),
                $reservationRepo(),
            ),
            $programService(),
            $sessionService,
        ),
        StorytellingController::class => new StorytellingController(
            new StorytellingService(
                $globalContentRepo(),
                $storyContentRepo(),
            ),
            new StorytellingDetailService(
                $storyContentRepo(),
                $eventRepo(),
                $eventSessionRepo(),
                $eventSessionLabel(),
                $mediaAssetRepo(),
                $globalContentRepo(),
            ),
            $sessionService,
            $scheduleService(),
        ),
        JazzController::class => new JazzController(
            new JazzService(
                $globalContentRepo(),
                $jazzContentRepo(),
                $eventRepo(),
                new PassTypeRepository($pdo()),
            ),
            new JazzArtistDetailService(
                $eventRepo(),
                new ArtistRepository($pdo()),
                new ArtistDetailRepository(
                    new ArtistAlbumRepository($pdo()),
                    new ArtistTrackRepository($pdo()),
                    new ArtistLineupMemberRepository($pdo()),
                    new ArtistHighlightRepository($pdo()),
                    new ArtistGalleryImageRepository($pdo()),
                ),
            ),
            $sessionService,
            $scheduleService(),
        ),
        CmsEventsController::class => new CmsEventsController(
            $cmsEventsService(),
            $sessionService,
            $cmsArtistsService(),
        ),
        CmsVenuesController::class => new CmsVenuesController(
            $cmsEventsService(),
            $sessionService,
        ),
        CmsScheduleDaysController::class => new CmsScheduleDaysController(
            new CmsScheduleDayService(
                $scheduleDayConfig(),
                $eventTypeRepo(),
                $visibilityResolver(),
            ),
            $sessionService,
        ),
        CmsJazzCardsController::class => new CmsJazzCardsController(
            $cmsArtistsService(),
            $mediaAssetService(),
            $sessionService,
        ),
        AuthController::class => new AuthController(
            $authService(),
            $sessionService,
            new CaptchaService(),
        ),
        CmsDashboardController::class => new CmsDashboardController(
            $sessionService,
            new CmsDashboardService($cmsRepo()),
        ),
        CmsPageEditorController::class => new CmsPageEditorController(
            $sessionService,
            new CmsEditService(
                $cmsRepo(),
                $eventRepo(),
                new ArtistRepository($pdo()),
                $cmsItemEnricher(),
                $cmsPreviewUrlResolver(),
            ),
        ),
        CmsPageImageController::class => new CmsPageImageController(
            $sessionService,
            new CmsEditService(
                $cmsRepo(),
                $eventRepo(),
                new ArtistRepository($pdo()),
                $cmsItemEnricher(),
                $cmsPreviewUrlResolver(),
            ),
            $mediaAssetService(),
        ),
        CheckoutController::class => (function () use ($programService, $sessionService, $orderRepo, $orderItemRepo, $paymentRepo, $eventSessionRepo, $programRepo, $pdo, $checkoutContentRepo, $orderCapacityRestorer, $ticketFulfillmentService, $invoiceFulfillmentService, $passPurchaseRepo) {
            // Stripe setup is only needed for checkout routes, so other pages do not create it.
            $stripeService = new StripeService(
                (string) (getenv('STRIPE_SECRET_KEY') !== false ? getenv('STRIPE_SECRET_KEY') : ''),
                (string) (getenv('STRIPE_WEBHOOK_SECRET') !== false ? getenv('STRIPE_WEBHOOK_SECRET') : ''),
            );
            $runtimeConfig = new CheckoutRuntimeConfig(
                (string) getenv('APP_URL'),
                (float) (getenv('VAT_RATE') !== false ? getenv('VAT_RATE') : 0.21),
            );

            return new CheckoutController(
                $programService(),
                $sessionService,
                new CheckoutService(
                    $orderRepo(),
                    $orderItemRepo(),
                    $paymentRepo(),
                    $eventSessionRepo(),
                    $stripeService,
                    $runtimeConfig,
                    $pdo(),
                    $checkoutContentRepo(),
                    $orderCapacityRestorer(),
                    $ticketFulfillmentService(),
                    $passPurchaseRepo(),
                    $programRepo(),
                ),
                new StripeWebhookHandler(
                    $stripeService,
                    new StripeWebhookEventRepository($pdo()),
                    $orderRepo(),
                    $paymentRepo(),
                    $programRepo(),
                    $orderCapacityRestorer(),
                    $ticketFulfillmentService(),
                    $invoiceFulfillmentService(),
                    $pdo(),
                ),
                new StripeWebhookRequestFactory(),
            );
        })(),
        HistoryController::class => new HistoryController(
            new HistoryService(
                $globalContentRepo(),
                $historyContentRepo(),
            ),
            new HistoricalLocationService(
                $cmsContent(),
                $globalContentRepo(),
                $histLocContentRepo(),
            ),
            $sessionService,
            $scheduleService(),
        ),
        CmsAuthController::class => new CmsAuthController(
            $authService(),
            $sessionService,
        ),
        CmsMediaController::class => new CmsMediaController(
            $mediaAssetService(),
            $sessionService,
        ),
        ProgramController::class => new ProgramController(
            $programService(),
            $sessionService,
        ),
        CmsOrdersController::class => new CmsOrdersController(
            new CmsOrdersService(new CmsOrdersRepository($pdo()), $invoiceRepo(), $mediaAssetRepo()),
            $ticketFulfillmentService(),
            $sessionService,
        ),
        CmsUsersController::class => new CmsUsersController(
            new CmsUsersService(new CmsUsersRepository($pdo()), $userAccountRepo()),
            $sessionService,
        ),
        CmsArtistsController::class => new CmsArtistsController(
            $cmsArtistsService(),
            $sessionService,
        ),
        ScannerController::class => new ScannerController(
            new ScannerService(
                new ScannerRepository($pdo()),
                $ticketRepo(),
            ),
            $sessionService,
        ),
        EmployeeScannerController::class => new EmployeeScannerController(
            $sessionService,
            new TicketScannerService(
                $ticketRepo(),
            ),
        ),
        ScheduleApiController::class => new ScheduleApiController(
            $scheduleService(),
        ),
        RestaurantApiController::class => new RestaurantApiController(
            $restaurantService(),
            $sessionService,
        ),
        OrderHistoryController::class => new OrderHistoryController(
            new OrderHistoryService(new OrderHistoryRepository($pdo())),
            $sessionService,
        ),
        AccountController::class => new AccountController(
            $accountService(),
            $mediaAssetService(),
            $sessionService,
        ),
        default => new $controllerClass(),
    };
};
