<?php
/**
 * Per-session edit block rendered inside the sessions loop on the event edit page.
 *
 * Locals:
 * @var \App\ViewModels\Cms\CmsEventSessionViewModel    $session
 * @var \App\Models\EventSessionLabel[]                 $sessionLabels
 * @var \App\ViewModels\Cms\CmsSessionPriceViewModel[]  $sessionPrices
 * @var \App\ViewModels\Cms\CmsEventEditViewModel       $viewModel
 * @var \App\Models\PriceTier[]                         $priceTiers
 */
?>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-medium text-gray-900">
                                        <?= $session->formattedDateLong ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?= $session->formattedStartTime ?> -
                                        <?= $session->formattedEndTime ?: 'TBD' ?>
                                    </p>
                                </div>
                                <form action="/cms/sessions/<?= $session->eventSessionId ?>/delete" method="POST"
                                      data-confirm="Delete this session?">
                                    <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>

                            <!-- Session Details Form -->
                            <form action="/cms/sessions/<?= $session->eventSessionId ?>" method="POST"
                                  class="mb-4">
                                <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Start</label>
                                        <input type="datetime-local" name="StartDateTime"
                                               value="<?= $session->formattedDateTimeLocal ?>"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">End</label>
                                        <input type="datetime-local" name="EndDateTime"
                                               value="<?= $session->formattedEndDateTimeLocal ?>"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <?php if ($viewModel->eventTypeSlug === 'jazz'): ?>
                                        <div>
                                            <label class="block text-xs font-medium text-purple-600 mb-1">🎷 Seats
                                                Available</label>
                                            <input type="number" name="CapacityTotal" min="0"
                                                   value="<?= $session->capacityTotal ?>"
                                                   class="w-full px-2 py-1 text-sm border border-purple-300 rounded focus:ring-1 focus:ring-purple-500 bg-purple-50">
                                            <p class="text-[10px] text-gray-500 mt-0.5">
                                                Sold: <?= $session->soldTicketsTotal ?>
                                            </p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-purple-600 mb-1">🎷
                                                Hall/Stage</label>
                                            <input type="text" name="HallName"
                                                   value="<?= htmlspecialchars($session->hallName ?? '') ?>"
                                                   placeholder="Main Hall, Outdoor Stage..."
                                                   class="w-full px-2 py-1 text-sm border border-purple-300 rounded focus:ring-1 focus:ring-purple-500 bg-purple-50">
                                        </div>
                                    <?php else: ?>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Capacity</label>
                                            <input type="number" name="CapacityTotal" min="1"
                                                   value="<?= $session->capacityTotal ?>"
                                                   class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($viewModel->eventTypeSlug === 'history'): ?>
                                        <div>
                                            <label class="block text-xs font-medium text-amber-600 mb-1">🏛️ Ticket
                                                Label</label>
                                            <input type="text" name="HistoryTicketLabel"
                                                   value="<?= htmlspecialchars($session->historyTicketLabel ?? '') ?>"
                                                   placeholder="Group ticket - best value..."
                                                   class="w-full px-2 py-1 text-sm border border-amber-300 rounded focus:ring-1 focus:ring-amber-500 bg-amber-50">
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CTA Label</label>
                                        <input type="text" name="CtaLabel"
                                               value="<?= htmlspecialchars($session->ctaLabel ?? '') ?>"
                                               placeholder="Discover"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CTA URL</label>
                                        <input type="text" name="CtaUrl"
                                               value="<?= htmlspecialchars($session->ctaUrl ?? '') ?>"
                                               placeholder="/event/..."
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                    <!-- Ticket limit per person -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Ticket Limit / Person</label>
                                        <input type="number" name="CapacitySingleTicketLimit" min="1"
                                               value="<?= $session->capacitySingleTicketLimit ?>"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                        <p class="text-[10px] text-gray-400 mt-0.5">Max tickets per order</p>
                                    </div>
                                    <!-- Read-only: seats available -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Seats Available</label>
                                        <p class="px-2 py-1 text-sm bg-gray-100 rounded text-gray-700"><?= $session->seatsAvailable ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Single Tickets Sold</label>
                                        <p class="px-2 py-1 text-sm bg-gray-100 rounded text-gray-700"><?= $session->soldSingleTickets ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Reserved Seats Sold</label>
                                        <p class="px-2 py-1 text-sm bg-gray-100 rounded text-gray-700"><?= $session->soldReservedSeats ?></p>
                                    </div>
                                    <!-- Session Type -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Session Type</label>
                                        <input type="text" name="SessionType"
                                               value="<?= htmlspecialchars($session->sessionType ?? '') ?>"
                                               placeholder="e.g., Workshop, Tour"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <!-- Duration -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Duration (min)</label>
                                        <input type="number" name="DurationMinutes" min="0"
                                               value="<?= $session->durationMinutes ?? '' ?>"
                                               placeholder="e.g., 90"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <!-- Language -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Language</label>
                                        <select name="LanguageCode"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <option value="">Not specified</option>
                                            <option value="NL" <?= $session->languageCode === 'NL' ? 'selected' : '' ?>>Dutch (NL)</option>
                                            <option value="ENG" <?= $session->languageCode === 'ENG' ? 'selected' : '' ?>>English (ENG)</option>
                                            <option value="ZH" <?= $session->languageCode === 'ZH' ? 'selected' : '' ?>>Chinese (ZH)</option>
                                        </select>
                                    </div>
                                    <!-- Min Age -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Min Age</label>
                                        <input type="number" name="MinAge" min="0"
                                               value="<?= $session->minAge ?? '' ?>"
                                               placeholder="—"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <!-- Max Age -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Max Age</label>
                                        <input type="number" name="MaxAge" min="0"
                                               value="<?= $session->maxAge ?? '' ?>"
                                               placeholder="—"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <!-- Notes -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                                        <input type="text" name="Notes"
                                               value="<?= htmlspecialchars($session->notes) ?>"
                                               placeholder="Internal notes"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                </div>
                                <!-- Checkboxes row -->
                                <div class="flex flex-wrap gap-6 mt-4">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="IsFree" value="1" <?= $session->isFree ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        Free admission
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="ReservationRequired" value="1" <?= $session->reservationRequired ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        Reservation required
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="IsCancelled" value="1" <?= $session->isCancelled ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="text-red-600">Cancelled</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="IsActive" value="1" <?= $session->isActive ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        Active (visible on site)
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <button type="submit"
                                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Update Session
                                    </button>
                                </div>
                            </form>

                            <!-- Labels -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Labels</h4>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <?php foreach ($sessionLabels as $label): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-pink-100 text-pink-800 rounded text-sm">
                                                <?= htmlspecialchars($label->labelText) ?>
                                                <form action="/cms/labels/<?= $label->eventSessionLabelId ?>/delete"
                                                      method="POST" class="inline">
                                                    <input type="hidden" name="EventId"
                                                           value="<?= $viewModel->eventId ?>">
                                                    <button type="submit" class="text-pink-600 hover:text-pink-800"
                                                            title="Remove label">
                                                        &times;
                                                    </button>
                                                </form>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                                <form action="/cms/sessions/<?= $session->eventSessionId ?>/labels" method="POST"
                                      class="flex gap-2">
                                    <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                    <input type="text" name="LabelText"
                                           placeholder="New label (e.g., In Dutch, Age 16+)"
                                           class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    <button type="submit"
                                            class="px-3 py-1 text-sm bg-pink-600 text-white rounded hover:bg-pink-700">
                                        Add Label
                                    </button>
                                </form>
                            </div>

                            <!-- Prices -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Prices</h4>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <?php foreach ($sessionPrices as $price): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">
                                            <?= htmlspecialchars($price->tierName) ?>:
                                            <?= htmlspecialchars($price->currencyCode) ?> <?= number_format((float) $price->price, 2) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if (empty($sessionPrices)): ?>
                                        <span class="text-sm text-gray-500">No prices set</span>
                                    <?php endif; ?>
                                </div>
                                <!-- Add/Update Price Form -->
                                <form action="/cms/sessions/<?= $session->eventSessionId ?>/price" method="POST"
                                      class="flex flex-wrap gap-2 items-end">
                                    <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Price Tier</label>
                                        <select name="PriceTierId"
                                                class="px-2 py-1 text-sm border border-gray-300 rounded">
                                            <?php
                                            // History events only support Single (6) and Group (7) tickets
                                            $historyOnlyTierIds = [6, 7];
                                            foreach ($priceTiers as $tier):
                                                if ($viewModel->eventTypeSlug === 'history' && !in_array($tier->priceTierId, $historyOnlyTierIds, true)):
                                                    continue;
                                                endif;
                                            ?>
                                                <option value="<?= $tier->priceTierId ?>">
                                                    <?= htmlspecialchars($tier->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Price (€)</label>
                                        <input type="text" name="Price" inputmode="decimal"
                                               pattern="[0-9]+([.,][0-9]{1,2})?"
                                               value="0.00" placeholder="12.50"
                                               class="w-24 px-2 py-1 text-sm border border-gray-300 rounded">
                                    </div>
                                    <button type="submit"
                                            class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                        Set Price
                                    </button>
                                </form>
                            </div>
                        </div>
