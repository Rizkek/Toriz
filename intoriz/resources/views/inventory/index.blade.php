<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Management - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes skeleton-loading {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 1000px 100%;
            animation: skeleton-loading 2s infinite;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toast {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-overlay {
            animation: fadeIn 0.2s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <div id="app" class="min-h-screen">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
                        <p class="text-gray-600 mt-1">Manage your product inventory efficiently</p>
                    </div>
                    <button id="addItemBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition duration-200 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Item
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Action Bar -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search by name, SKU, or category..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                        <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">All Categories</option>
                    </select>
                </div>

                <!-- Quick Actions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actions</label>
                    <div class="flex gap-2">
                        <button id="importBtn" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition duration-200 ease-in-out" title="Import from file">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                        <button id="uploadPhotoBtn" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition duration-200 ease-in-out" title="Upload photo">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Items Count and Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-600 text-sm">Total Items</p>
                    <p id="totalItems" class="text-3xl font-bold text-gray-900 mt-1">0</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-600 text-sm">Low Stock</p>
                    <p id="lowStockItems" class="text-3xl font-bold text-orange-600 mt-1">0</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-600 text-sm">Total Value</p>
                    <p id="totalValue" class="text-3xl font-bold text-green-600 mt-1">$0.00</p>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <!-- Table Header with Loading State -->
                <div id="tableLoading" class="p-4 hidden">
                    <div class="space-y-3">
                        <div class="skeleton h-10 rounded"></div>
                        <div class="skeleton h-10 rounded"></div>
                        <div class="skeleton h-10 rounded"></div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="p-12 text-center hidden">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No items found</h3>
                    <p class="text-gray-600">Get started by adding your first inventory item</p>
                </div>

                <!-- Items Table -->
                <table id="itemsTable" class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200">
                        <!-- Items will be inserted here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing <span id="itemsFrom">0</span> to <span id="itemsTo">0</span> of <span id="itemsTotal">0</span> results
                </div>
                <div class="flex gap-2">
                    <button id="prevBtn" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition" disabled>Previous</button>
                    <button id="nextBtn" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition" disabled>Next</button>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed bottom-4 right-4 space-y-2 z-50"></div>

    <!-- Modals will be injected here via JavaScript -->
    <div id="modalsContainer"></div>

    <script type="module">
        import { InventoryService } from '/js/services/inventory-service.js';
        import { UIManager } from '/js/services/ui-manager.js';
        import { ToastManager } from '/js/services/toast-manager.js';

        // Initialize managers
        const inventoryService = new InventoryService();
        const uiManager = new UIManager();
        const toastManager = new ToastManager();

        // Global state
        let currentPage = 1;
        let currentFilters = {
            search: '',
            category: '',
        };

        // Initialize
        window.addEventListener('DOMContentLoaded', async () => {
            await loadInventory();
            await loadCategories();
            setupEventListeners();
        });

        // Load inventory items
        async function loadInventory(page = 1) {
            uiManager.showTableLoading(true);

            try {
                const response = await inventoryService.getItems({
                    page,
                    search: currentFilters.search,
                    category: currentFilters.category,
                });

                uiManager.renderTable(response.data);
                uiManager.updatePagination(response, page);
                updateStats(response.data);

                if (response.data.length === 0) {
                    uiManager.showEmptyState(true);
                } else {
                    uiManager.showEmptyState(false);
                }

                currentPage = page;
            } catch (error) {
                console.error('Failed to load inventory:', error);
                toastManager.error('Failed to load inventory items');
                uiManager.showEmptyState(true);
            } finally {
                uiManager.showTableLoading(false);
            }
        }

        // Load categories for filter
        async function loadCategories() {
            try {
                const categories = await inventoryService.getCategories();
                const select = document.getElementById('categoryFilter');
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Failed to load categories:', error);
            }
        }

        // Update statistics
        function updateStats(items) {
            const totalItems = items.length;
            const lowStock = items.filter(item => item.quantity < 10).length;
            const totalValue = items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('lowStockItems').textContent = lowStock;
            document.getElementById('totalValue').textContent = '$' + totalValue.toFixed(2);
        }

        // Setup event listeners
        function setupEventListeners() {
            // Add Item Button
            document.getElementById('addItemBtn').addEventListener('click', () => {
                uiManager.showAddItemModal(async (data) => {
                    try {
                        await inventoryService.addItem(data);
                        toastManager.success('Item added successfully');
                        await loadInventory(1);
                    } catch (error) {
                        toastManager.error(error.message || 'Failed to add item');
                    }
                });
            });

            // Search
            document.getElementById('searchInput').addEventListener('input', (e) => {
                currentFilters.search = e.target.value;
                loadInventory(1);
            });

            // Category Filter
            document.getElementById('categoryFilter').addEventListener('change', (e) => {
                currentFilters.category = e.target.value;
                loadInventory(1);
            });

            // Import Button
            document.getElementById('importBtn').addEventListener('click', () => {
                uiManager.showImportModal(async (file) => {
                    try {
                        const result = await inventoryService.importFile(file);
                        toastManager.success(`Import completed: ${result.imported} items processed`);
                        if (result.errors && result.errors.length > 0) {
                            result.errors.forEach(error => toastManager.warning(error));
                        }
                        await loadInventory(1);
                    } catch (error) {
                        toastManager.error(error.message || 'Import failed');
                    }
                });
            });

            // Upload Photo Button
            document.getElementById('uploadPhotoBtn').addEventListener('click', () => {
                uiManager.showPhotoUploadModal(async (file, itemId) => {
                    try {
                        const result = await inventoryService.uploadImage(file, itemId);
                        toastManager.success('Photo uploaded successfully');
                        if (itemId) {
                            await loadInventory(currentPage);
                        }
                    } catch (error) {
                        toastManager.error(error.message || 'Photo upload failed');
                    }
                });
            });

            // Pagination
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentPage > 1) {
                    loadInventory(currentPage - 1);
                }
            });

            document.getElementById('nextBtn').addEventListener('click', () => {
                loadInventory(currentPage + 1);
            });

            // Delegation for item actions (edit/delete)
            document.getElementById('tableBody').addEventListener('click', async (e) => {
                if (e.target.closest('[data-edit]')) {
                    const itemId = e.target.closest('[data-edit]').dataset.edit;
                    const item = currentItems.find(i => i.id == itemId);
                    if (item) {
                        uiManager.showEditItemModal(item, async (data) => {
                            try {
                                await inventoryService.updateItem(itemId, data);
                                toastManager.success('Item updated successfully');
                                await loadInventory(currentPage);
                            } catch (error) {
                                toastManager.error(error.message || 'Failed to update item');
                            }
                        });
                    }
                } else if (e.target.closest('[data-delete]')) {
                    const itemId = e.target.closest('[data-delete]').dataset.delete;
                    if (confirm('Are you sure you want to delete this item?')) {
                        try {
                            await inventoryService.deleteItem(itemId);
                            toastManager.success('Item deleted successfully');
                            await loadInventory(currentPage);
                        } catch (error) {
                            toastManager.error(error.message || 'Failed to delete item');
                        }
                    }
                }
            });
        }

        // Expose globally for use in templates
        window.loadInventory = loadInventory;
        window.inventoryService = inventoryService;
        window.uiManager = uiManager;
        window.toastManager = toastManager;
        window.currentItems = [];
    </script>
</body>
</html>
