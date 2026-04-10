<?php
/**
 * Inline "Add New Venue" toggle form rendered inside the venue dropdown row.
 * No locals required — pure markup.
 */
?>
                        <div id="newVenueForm" class="hidden mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-900 mb-3">Create New Venue</h4>
                            <div class="space-y-3">
                                <div>
                                    <label for="NewVenueName" class="block text-xs font-medium text-gray-700 mb-1">
                                        Venue Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="NewVenueName"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm"
                                           placeholder="e.g., Patronaat, Jopenkerk">
                                </div>
                                <div>
                                    <label for="NewVenueAddress" class="block text-xs font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <input type="text" id="NewVenueAddress"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm"
                                           placeholder="e.g., Zijlsingel 2">
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" data-action="createVenue"
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                        Create Venue
                                    </button>
                                    <button type="button" data-toggle="newVenueForm"
                                            class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                        Cancel
                                    </button>
                                </div>
                                <p id="venueError" class="hidden text-xs text-red-600"></p>
                            </div>
                        </div>
