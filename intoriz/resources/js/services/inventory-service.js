/**
 * Inventory API Service
 * Handles all API communication with the backend
 */
export class InventoryService {
    constructor(baseUrl = '/inventory/api') {
        this.baseUrl = baseUrl;
        this.headers = {
            'X-Requested-With': 'XMLHttpRequest',
        };

        // Add CSRF token if available
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.headers['X-CSRF-TOKEN'] = token.content;
        }
    }

    /**
     * Fetch items with pagination and filters
     */
    async getItems(params = {}) {
        const query = new URLSearchParams({
            page: params.page || 1,
            search: params.search || '',
            category: params.category || '',
            status: params.status || '',
            low_stock: params.low_stock ? 1 : 0,
        });

        const response = await fetch(`${this.baseUrl}/items?${query}`, {
            method: 'GET',
            headers: this.headers,
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch items: ${response.statusText}`);
        }

        return response.json();
    }

    /**
     * Get unique categories
     */
    async getCategories() {
        const response = await fetch(`${this.baseUrl}/categories`, {
            method: 'GET',
            headers: this.headers,
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch categories: ${response.statusText}`);
        }

        return response.json();
    }

    /**
     * Add a new inventory item
     */
    async addItem(data) {
        const response = await fetch(`${this.baseUrl}/items`, {
            method: 'POST',
            headers: {
                ...this.headers,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to add item');
        }

        return result;
    }

    /**
     * Update an inventory item
     */
    async updateItem(itemId, data) {
        const response = await fetch(`${this.baseUrl}/items/${itemId}`, {
            method: 'PUT',
            headers: {
                ...this.headers,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to update item');
        }

        return result;
    }

    /**
     * Delete an inventory item
     */
    async deleteItem(itemId) {
        const response = await fetch(`${this.baseUrl}/items/${itemId}`, {
            method: 'DELETE',
            headers: this.headers,
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to delete item');
        }

        return result;
    }

    /**
     * Import items from file (CSV/Excel)
     */
    async importFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        // Don't set Content-Type header - let browser set it with boundary
        const response = await fetch(`${this.baseUrl}/import`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.headers['X-CSRF-TOKEN'],
            },
            body: formData,
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to import file');
        }

        return result;
    }

    /**
     * Upload image
     */
    async uploadImage(file, itemId = null) {
        const formData = new FormData();
        formData.append('image', file);
        if (itemId) {
            formData.append('item_id', itemId);
        }

        const response = await fetch(`${this.baseUrl}/upload-image`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.headers['X-CSRF-TOKEN'],
            },
            body: formData,
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to upload image');
        }

        return result;
    }
}
