/**
 * UI Manager
 * Handles all UI interactions including modals and table rendering
 */
export class UIManager {
    constructor() {
        this.modalsContainer = document.getElementById('modalsContainer') || this.createModalsContainer();
    }

    createModalsContainer() {
        const container = document.createElement('div');
        container.id = 'modalsContainer';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Show/hide table loading skeleton
     */
    showTableLoading(show) {
        const loader = document.getElementById('tableLoading');
        if (loader) {
            loader.classList.toggle('hidden', !show);
        }
    }

    /**
     * Show/hide empty state
     */
    showEmptyState(show) {
        const empty = document.getElementById('emptyState');
        if (empty) {
            empty.classList.toggle('hidden', !show);
        }
    }

    /**
     * Render inventory items table
     */
    renderTable(items) {
        const tbody = document.getElementById('tableBody');
        window.currentItems = items; // Store for later access

        if (!items || items.length === 0) {
            tbody.innerHTML = '';
            return;
        }

        tbody.innerHTML = items.map(item => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                    ${this.escapeHtml(item.sku)}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    <div class="font-medium">${this.escapeHtml(item.name)}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${item.description ? this.escapeHtml(item.description.substring(0, 50)) : ''}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${item.category ? `<span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">${this.escapeHtml(item.category)}</span>` : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 py-1 rounded text-xs font-medium ${item.quantity < 10 ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'}">
                        ${item.quantity} units
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    $${parseFloat(item.unit_price).toFixed(2)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${item.location ? this.escapeHtml(item.location) : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2 flex">
                    <button data-edit="${item.id}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 font-medium transition">Edit</button>
                    <button data-delete="${item.id}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium transition">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Update pagination controls
     */
    updatePagination(response, currentPage) {
        const from = (currentPage - 1) * 15 + 1;
        const to = Math.min(currentPage * 15, response.total);

        document.getElementById('itemsFrom').textContent = from;
        document.getElementById('itemsTo').textContent = to;
        document.getElementById('itemsTotal').textContent = response.total;

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.disabled = !response.prev_page_url;
        nextBtn.disabled = !response.next_page_url;
    }

    /**
     * Create and show Add Item Modal
     */
    showAddItemModal(onSubmit) {
        const modal = this.createModal('Add Inventory Item', `
            <form id="addItemForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU *</label>
                    <input type="text" name="sku" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., SKU-001">
                    <span class="text-xs text-red-500 hidden"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Product name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Product description"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity *</label>
                        <input type="number" name="quantity" required min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Price *</label>
                        <input type="number" name="unit_price" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                        <input type="text" name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Electronics">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input type="text" name="location" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Warehouse A">
                    </div>
                </div>
            </form>
        `, [
            { label: 'Cancel', class: 'bg-gray-500 hover:bg-gray-600', onclick: (modal) => modal.remove() },
            { label: 'Add Item', class: 'bg-blue-600 hover:bg-blue-700', onclick: async (modal) => {
                const form = document.getElementById('addItemForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const data = new FormData(form);
                const obj = Object.fromEntries(data);

                try {
                    await onSubmit(obj);
                    modal.remove();
                } catch (error) {
                    console.error('Error:', error);
                }
            }}
        ]);
    }

    /**
     * Create and show Edit Item Modal
     */
    showEditItemModal(item, onSubmit) {
        const modal = this.createModal(`Edit Item: ${item.name}`, `
            <form id="editItemForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU *</label>
                    <input type="text" name="sku" value="${this.escapeHtml(item.sku)}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name *</label>
                    <input type="text" name="name" value="${this.escapeHtml(item.name)}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">${this.escapeHtml(item.description || '')}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity *</label>
                        <input type="number" name="quantity" value="${item.quantity}" required min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Price *</label>
                        <input type="number" name="unit_price" value="${item.unit_price}" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                        <input type="text" name="category" value="${this.escapeHtml(item.category || '')}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input type="text" name="location" value="${this.escapeHtml(item.location || '')}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </form>
        `, [
            { label: 'Cancel', class: 'bg-gray-500 hover:bg-gray-600', onclick: (modal) => modal.remove() },
            { label: 'Update Item', class: 'bg-blue-600 hover:bg-blue-700', onclick: async (modal) => {
                const form = document.getElementById('editItemForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const data = new FormData(form);
                const obj = Object.fromEntries(data);

                try {
                    await onSubmit(obj);
                    modal.remove();
                } catch (error) {
                    console.error('Error:', error);
                }
            }}
        ]);
    }

    /**
     * Create and show Import Modal with drag & drop
     */
    showImportModal(onSubmit) {
        const modal = this.createModal('Import Inventory', `
            <div class="space-y-4">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900 dark:hover:bg-opacity-20 transition" id="dropZone">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-gray-700 dark:text-gray-300 font-medium">Drag and drop your file here</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">or click to select CSV/Excel file</p>
                    <p class="text-gray-400 text-xs mt-2">Maximum file size: 5MB</p>
                    <input type="file" id="fileInput" accept=".csv,.xlsx,.xls" class="hidden">
                </div>
                <div class="bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 text-sm mb-2">Required CSV/Excel Columns:</h4>
                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• <strong>sku</strong> - Product SKU (required)</li>
                        <li>• <strong>name</strong> - Product name (required)</li>
                        <li>• <strong>quantity</strong> - Stock quantity (required)</li>
                        <li>• <strong>unit_price</strong> - Price per unit</li>
                        <li>• <strong>description</strong> - Product description</li>
                        <li>• <strong>category</strong> - Product category</li>
                        <li>• <strong>location</strong> - Warehouse location</li>
                    </ul>
                </div>
                <div id="fileInfo" class="hidden">
                    <p class="text-sm text-green-600 dark:text-green-400"><strong>Selected:</strong> <span id="fileName"></span></p>
                </div>
            </div>
        `, [
            { label: 'Cancel', class: 'bg-gray-500 hover:bg-gray-600', onclick: (modal) => modal.remove() },
            { label: 'Import', class: 'bg-green-600 hover:bg-green-700', onclick: async (modal) => {
                const file = document.getElementById('fileInput').files[0];
                if (!file) {
                    alert('Please select a file');
                    return;
                }

                try {
                    await onSubmit(file);
                    modal.remove();
                } catch (error) {
                    console.error('Error:', error);
                }
            }, id: 'importBtn' }
        ]);

        this.setupDragDrop(modal);
    }

    /**
     * Create and show Photo Upload Modal with camera support
     */
    showPhotoUploadModal(onSubmit) {
        const modal = this.createModal('Upload Product Photo', `
            <div class="space-y-4">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200 dark:border-gray-700">
                    <button class="uploadTab flex-1 pb-2 px-4 text-center border-b-2 border-blue-600 text-blue-600 font-medium" data-tab="upload">
                        Upload File
                    </button>
                    <button class="uploadTab flex-1 pb-2 px-4 text-center border-b-2 border-transparent text-gray-600 dark:text-gray-400 font-medium" data-tab="camera" id="cameraTab">
                        Camera
                    </button>
                </div>

                <!-- Upload Tab -->
                <div id="uploadTab" class="uploadTabContent space-y-4">
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center cursor-pointer hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900 dark:hover:bg-opacity-20 transition" id="photoDropZone">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300 font-medium">Drag and drop image here</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">or click to select</p>
                        <p class="text-gray-400 text-xs mt-2">JPEG, PNG or WebP • Max 2MB</p>
                        <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                    </div>
                    <div id="photoPreview" class="hidden text-center">
                        <img id="previewImage" class="max-h-64 mx-auto rounded-lg">
                        <button type="button" id="clearPhotoBtn" class="mt-2 text-sm text-red-600 hover:text-red-800">Change photo</button>
                    </div>
                </div>

                <!-- Camera Tab -->
                <div id="cameraTab" class="uploadTabContent hidden space-y-4">
                    <div id="cameraContainer" class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-black">
                        <video id="cameraFeed" class="w-full" style="max-height: 400px; object-fit: cover;"></video>
                    </div>
                    <button type="button" id="takeShotBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 rounded-lg transition">
                        Take Photo
                    </button>
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                </div>
            </div>
        `, [
            { label: 'Cancel', class: 'bg-gray-500 hover:bg-gray-600', onclick: (modal) => {
                this.stopCamera();
                modal.remove();
            }},
            { label: 'Upload Photo', class: 'bg-purple-600 hover:bg-purple-700', onclick: async (modal) => {
                const file = document.getElementById('photoInput').files[0];
                if (!file) {
                    alert('Please select or capture a photo');
                    return;
                }

                try {
                    await onSubmit(file, null);
                    this.stopCamera();
                    modal.remove();
                } catch (error) {
                    console.error('Error:', error);
                }
            }}
        ]);

        this.setupPhotoDragDrop(modal);
        this.setupCameraTabs(modal);
    }

    /**
     * Create base modal structure
     */
    createModal(title, content, actions = []) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';

        const modal = document.createElement('div');
        modal.className = 'bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 max-h-screen overflow-auto';

        modal.innerHTML = `
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">${title}</h2>
                <div class="mb-6">${content}</div>
                <div class="flex gap-3 justify-end">
                    ${actions.map(action => `
                        <button type="button" class="px-4 py-2 rounded-lg text-white font-medium transition ${action.class}" id="${action.id || ''}">
                            ${action.label}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;

        backdrop.appendChild(modal);
        this.modalsContainer.appendChild(backdrop);

        // Attach click handlers
        const buttons = modal.querySelectorAll('button[type="button"]');
        buttons.forEach((btn, index) => {
            if (actions[index] && actions[index].onclick) {
                btn.addEventListener('click', () => actions[index].onclick(backdrop));
            }
        });

        // Close on backdrop click
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                backdrop.remove();
            }
        });

        return backdrop;
    }

    /**
     * Setup drag and drop for import
     */
    setupDragDrop(modal) {
        const dropZone = modal.querySelector('#dropZone');
        const fileInput = modal.querySelector('#fileInput');
        const fileInfo = modal.querySelector('#fileInfo');
        const fileName = modal.querySelector('#fileName');

        // Click to select
        dropZone.addEventListener('click', () => fileInput.click());

        // File input change
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileInfo.classList.remove('hidden');
            }
        });

        // Drag over
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        // Drop
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileName.textContent = files[0].name;
                fileInfo.classList.remove('hidden');
            }
        });
    }

    /**
     * Setup drag and drop for photos
     */
    setupPhotoDragDrop(modal) {
        const dropZone = modal.querySelector('#photoDropZone');
        const fileInput = modal.querySelector('#photoInput');
        const preview = modal.querySelector('#photoPreview');
        const previewImg = modal.querySelector('#previewImage');
        const clearBtn = modal.querySelector('#clearPhotoBtn');

        const showPreview = (file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                dropZone.classList.add('hidden');
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        };

        // Click to select
        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (e) => {
            if (e.target.files[0]) {
                showPreview(e.target.files[0]);
            }
        });

        // Clear preview
        clearBtn.addEventListener('click', () => {
            fileInput.value = '';
            dropZone.classList.remove('hidden');
            preview.classList.add('hidden');
        });

        // Drag over
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-purple-500');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-purple-500');
        });

        // Drop
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-purple-500');

            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                fileInput.files = files;
                showPreview(files[0]);
            }
        });
    }

    /**
     * Setup camera tabs and functionality
     */
    setupCameraTabs(modal) {
        const tabs = modal.querySelectorAll('.uploadTab');
        const contents = modal.querySelectorAll('.uploadTabContent');
        const cameraTab = modal.querySelector('#cameraTab');
        const cameraContainer = modal.querySelector('#cameraContainer');
        const video = modal.querySelector('#cameraFeed');
        const canvas = modal.querySelector('#cameraCanvas');
        const takeShotBtn = modal.querySelector('#takeShotBtn');
        const photoInput = modal.querySelector('#photoInput');

        // Check camera support
        const hasCamera = navigator.mediaDevices && navigator.mediaDevices.getUserMedia;
        if (!hasCamera) {
            cameraTab.disabled = true;
            cameraTab.classList.add('opacity-50', 'cursor-not-allowed');
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', async (e) => {
                const tabName = e.target.dataset.tab;

                // Update tab styles
                tabs.forEach(t => {
                    if (t.dataset.tab === tabName) {
                        t.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                        t.classList.remove('border-transparent', 'text-gray-600');
                    } else {
                        t.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');
                        t.classList.add('border-transparent', 'text-gray-600');
                    }
                });

                // Update content visibility
                contents.forEach(content => {
                    if (content.id === tabName + 'Tab') {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });

                // Initialize camera if needed
                if (tabName === 'camera' && hasCamera) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment' }
                        });
                        video.srcObject = stream;
                    } catch (error) {
                        console.error('Camera access denied:', error);
                    }
                }
            });
        });

        if (hasCamera) {
            takeShotBtn.addEventListener('click', () => {
                if (video.srcObject) {
                    const ctx = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0);

                    canvas.toBlob((blob) => {
                        const file = new File([blob], 'camera_photo.jpg', { type: 'image/jpeg' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        photoInput.files = dataTransfer.files;

                        // Show preview
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            modal.querySelector('#previewImage').src = e.target.result;
                            modal.querySelector('#photoDropZone').classList.add('hidden');
                            modal.querySelector('#photoPreview').classList.remove('hidden');
                            modal.querySelector('#uploadTab').classList.add('hidden');
                            modal.querySelector('#cameraTab').classList.add('hidden');
                            modal.querySelector('.flex.border-b').classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }, 'image/jpeg', 0.9);
                }
            });
        }
    }

    /**
     * Stop camera stream
     */
    stopCamera() {
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        });
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
