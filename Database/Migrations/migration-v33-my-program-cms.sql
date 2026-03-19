-- migration-v33-my-program-cms.sql
--
-- Adds CMS content for the My Program page (CmsPageId = 9) and
-- the Checkout page (CmsPageId = 10).
--
-- My Program sections:
--   CmsSectionId 79 = main  (page text, headings, button labels)
--
-- Checkout sections:
--   CmsSectionId 80 = main  (page text, form labels, button labels)
--
-- Also updates the Storytelling schedule section CTA button text
-- from 'Discover' to 'Add to program' (CmsSectionId 36).
--
-- Safe to run multiple times: all INSERT statements use INSERT IGNORE.
-- ---------------------------------------------------------------

-- ---------------------------------------------------------------
-- CmsPage
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(9,  'my-program', 'My Program'),
(10, 'checkout',   'Checkout');

-- ---------------------------------------------------------------
-- CmsSection
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(79, 9,  'main'),
(80, 10, 'main');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 79 — My Program main section (IDs 855–862)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(855, 79, 'page_title',                'HEADING',     'My Program',                          NULL, NULL, '2026-03-19 00:00:00'),
(856, 79, 'selected_events_heading',   'HEADING',     'Your Selected Events',                NULL, NULL, '2026-03-19 00:00:00'),
(857, 79, 'pay_what_you_like_message', 'TEXT',        'Choose the amount you want to pay for this story. Any contribution is welcome and supports the initiative sharing their story.\nYou can adjust the amount before confirming your reservation.', NULL, NULL, '2026-03-19 00:00:00'),
(858, 79, 'clear_button_text',         'BUTTON_TEXT', 'CLEAR MY PROGRAMS',                  NULL, NULL, '2026-03-19 00:00:00'),
(859, 79, 'continue_exploring_text',   'TEXT',        'Continue exploring events',           NULL, NULL, '2026-03-19 00:00:00'),
(860, 79, 'payment_overview_heading',  'HEADING',     'Payment Overview',                    NULL, NULL, '2026-03-19 00:00:00'),
(861, 79, 'tax_label',                 'TEXT',        'VAT (21%)',                           NULL, NULL, '2026-03-19 00:00:00'),
(862, 79, 'checkout_button_text',      'BUTTON_TEXT', 'Continue to Checkout',               NULL, NULL, '2026-03-19 00:00:00');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 80 — Checkout main section (IDs 863–878)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(863, 80, 'page_title',                'HEADING',     'Checkout',                                               NULL, NULL, '2026-03-19 00:00:00'),
(864, 80, 'back_button_text',          'BUTTON_TEXT', 'Back to My Program',                                     NULL, NULL, '2026-03-19 00:00:00'),
(865, 80, 'payment_overview_heading',  'HEADING',     'Payment Overview',                                       NULL, NULL, '2026-03-19 00:00:00'),
(866, 80, 'personal_info_heading',     'HEADING',     'Personal Information',                                   NULL, NULL, '2026-03-19 00:00:00'),
(867, 80, 'personal_info_subtext',     'TEXT',        'Please fill in your details to complete the reservation', NULL, NULL, '2026-03-19 00:00:00'),
(868, 80, 'first_name_label',          'TEXT',        'First Name',                                             NULL, NULL, '2026-03-19 00:00:00'),
(869, 80, 'first_name_placeholder',    'TEXT',        'Enter your first name',                                  NULL, NULL, '2026-03-19 00:00:00'),
(870, 80, 'last_name_label',           'TEXT',        'Last Name',                                              NULL, NULL, '2026-03-19 00:00:00'),
(871, 80, 'last_name_placeholder',     'TEXT',        'Enter your last name',                                   NULL, NULL, '2026-03-19 00:00:00'),
(872, 80, 'email_label',               'TEXT',        'Email Address',                                          NULL, NULL, '2026-03-19 00:00:00'),
(873, 80, 'email_placeholder',         'TEXT',        'Enter your email address',                               NULL, NULL, '2026-03-19 00:00:00'),
(874, 80, 'payment_methods_heading',   'HEADING',     'Payment Method',                                         NULL, NULL, '2026-03-19 00:00:00'),
(875, 80, 'save_details_label',        'TEXT',        'Save my details',                                        NULL, NULL, '2026-03-19 00:00:00'),
(876, 80, 'save_details_subtext',      'TEXT',        'for faster checkout next time',                          NULL, NULL, '2026-03-19 00:00:00'),
(877, 80, 'pay_button_text',           'BUTTON_TEXT', 'Pay Now',                                                NULL, NULL, '2026-03-19 00:00:00'),
(878, 80, 'tax_label',                 'TEXT',        'VAT (21%)',                                              NULL, NULL, '2026-03-19 00:00:00');

