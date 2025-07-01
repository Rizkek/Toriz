// --- KONTROL TEMA (DARK MODE) ---
const themeToggleBtn = document.getElementById('theme-toggle');
const themeToggleMobileBtn = document.getElementById('theme-toggle-mobile');
const darkIcon = themeToggleBtn.querySelector('#theme-toggle-dark-icon');
const lightIcon = themeToggleBtn.querySelector('#theme-toggle-light-icon');

themeToggleMobileBtn.innerHTML = darkIcon.outerHTML + lightIcon.outerHTML;

function toggleIconsUI(isDarkMode) {
    themeToggleBtn.querySelector('#theme-toggle-dark-icon').classList.toggle('hidden', !isDarkMode);
    themeToggleBtn.querySelector('#theme-toggle-light-icon').classList.toggle('hidden', isDarkMode);
    themeToggleMobileBtn.querySelector('#theme-toggle-dark-icon').classList.toggle('hidden', !isDarkMode);
    themeToggleMobileBtn.querySelector('#theme-toggle-light-icon').classList.toggle('hidden', isDarkMode);
}

function setTheme(isDarkMode) {
    localStorage.setItem('color-theme', isDarkMode ? 'dark' : 'light');
    document.documentElement.classList.toggle('dark', isDarkMode);
    toggleIconsUI(isDarkMode);
}

const toggleTheme = () => setTheme(!document.documentElement.classList.contains('dark'));
themeToggleBtn.addEventListener('click', toggleTheme);
themeToggleMobileBtn.addEventListener('click', toggleTheme);

const initialTheme = localStorage.getItem('color-theme') === 'dark' || 
    (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
setTheme(initialTheme);


// --- KONTROL NAVIGASI (MOBILE) ---
const mobileNavButtons = document.querySelectorAll('.nav-btn');
const listSection = document.querySelector('.w-full.md\\:w-2\\/3');
const formSection = document.getElementById('page-form');
const pageTitle = document.getElementById('page-title');

const pageTitles = {
    'list-section': 'Daftar Stok',
    'page-form': 'Tambah Produk',
};

function showMobilePage(pageId) {
    const isDesktop = window.innerWidth >= 768;
    if (isDesktop) return;

    listSection.classList.toggle('hidden', pageId !== 'list-section');
    formSection.classList.toggle('hidden', pageId !== 'page-form');
    pageTitle.textContent = pageTitles[pageId] || 'Stok Toko';
    
    mobileNavButtons.forEach(btn => {
        const isTargetButton = btn.dataset.page === pageId;
        btn.classList.toggle('text-indigo-600', isTargetButton);
        btn.classList.toggle('dark:text-indigo-400', isTargetButton);
        btn.classList.toggle('text-slate-500', !isTargetButton);
        btn.classList.toggle('dark:text-slate-400', !isTargetButton);
    });
}

mobileNavButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        showMobilePage(button.dataset.page);
    });
});

// Atur ulang tampilan saat ukuran jendela berubah
window.addEventListener('resize', () => {
    const isDesktop = window.innerWidth >= 768;
    if (isDesktop) {
        listSection.classList.remove('hidden');
        formSection.classList.remove('hidden');
    } else {
        showMobilePage('list-section'); // Default ke halaman list di mobile
    }
});


// --- SCRIPT MANAJEMEN PRODUK ---
const productForm = document.getElementById("productForm");
const formTitle = document.getElementById('form-title');
const cancelEditBtn = document.getElementById('cancel-edit-btn');

function resetForm() {
    productForm.reset();
    document.getElementById('productId').value = '';
    formTitle.textContent = 'Tambah Produk Baru';
    cancelEditBtn.classList.add('hidden');
}
cancelEditBtn.addEventListener('click', resetForm);

function editProduct(productId) {
    const products = JSON.parse(localStorage.getItem("products") || '[]');
    const product = products.find(p => p.id == productId);

    if (product) {
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('quantity').value = product.quantity;
        document.getElementById('price').value = product.price;
        document.getElementById('category').value = product.category;
        document.getElementById('expiryDate').value = product.expiryDate || '';

        formTitle.textContent = 'Edit Produk';
        cancelEditBtn.classList.remove('hidden');
        
        const isDesktop = window.innerWidth >= 768;
        if (!isDesktop) {
            showMobilePage('page-form');
        } else {
            formSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

productForm.addEventListener("submit", function (event) {
    event.preventDefault();
    const productId = document.getElementById('productId').value;
    
    const productData = {
        id: productId || Date.now().toString(),
        name: document.getElementById('productName').value.trim(),
        quantity: document.getElementById('quantity').value.trim(),
        expiryDate: document.getElementById('expiryDate').value.trim(),
        price: document.getElementById('price').value.trim(),
        category: document.getElementById("category").value.trim(),
    };

    if (!productData.name || !productData.price || !productData.category) {
        showMessage("Nama, harga, dan kategori wajib diisi!", "error");
        return;
    }

    saveProduct(productData);
    showMessage(productId ? "Produk berhasil diperbarui!" : "Produk berhasil ditambahkan!", "success");
    
    resetForm();
    loadAndRenderTable();
    
    const isDesktop = window.innerWidth >= 768;
    if (!isDesktop) {
        showMobilePage('list-section');
    }
});

function saveProduct(productData) {
    let products = JSON.parse(localStorage.getItem("products") || '[]');
    const existingIndex = products.findIndex(p => p.id == productData.id);

    if (existingIndex > -1) {
        products[existingIndex] = productData;
    } else {
        products.push(productData);
    }
    localStorage.setItem("products", JSON.stringify(products));
}

function loadAndRenderTable() {
    const tableBody = document.getElementById("productTableBody");
    tableBody.innerHTML = '';
    const products = JSON.parse(localStorage.getItem("products") || '[]');
    products.forEach(product => {
        const newRow = tableBody.insertRow();
        newRow.className = "border-b border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50";
        newRow.dataset.id = product.id;

        newRow.innerHTML = `
            <td class="p-4">
                <div class="font-medium text-slate-900 dark:text-white">${product.name}</div>
                <div class="text-xs text-slate-500">${product.category}</div>
            </td>
            <td class="p-4 text-center">${product.quantity}</td>
            <td class="p-4 text-center">Rp ${new Intl.NumberFormat('id-ID').format(product.price || 0)}</td>
            <td class="p-4 text-center space-x-2">
                <button class="edit-btn p-1 text-slate-500 hover:text-blue-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z"></path></svg></button>
                <button class="delete-btn p-1 text-slate-500 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
            </td>`;

        newRow.querySelector('.edit-btn').onclick = () => editProduct(newRow.dataset.id);
        newRow.querySelector('.delete-btn').onclick = () => deleteProduct(newRow.dataset.id);
    });
}

function deleteProduct(productId) {
    if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
        let products = JSON.parse(localStorage.getItem("products") || '[]');
        products = products.filter(p => p.id != productId);
        localStorage.setItem("products", JSON.stringify(products));
        loadAndRenderTable();
        showMessage("Produk berhasil dihapus!", "success");
    }
}

function searchProducts() {
    const filter = document.getElementById("searchInput").value.toLowerCase();
    const selectedCategory = document.getElementById("filterCategory").value;
    document.querySelectorAll("#productTableBody tr").forEach(row => {
        const nameMatch = row.cells[0].children[0].textContent.toLowerCase().includes(filter);
        const categoryMatch = selectedCategory === "Semua" || row.cells[0].children[1].textContent === selectedCategory;
        row.style.display = (nameMatch && categoryMatch) ? "" : "table-row";
    });
}
document.getElementById("filterCategory").addEventListener("change", searchProducts);

function showMessage(message, type) {
    const messageBox = document.createElement('div');
    let typeClasses = '', icon = '';
    if (type === 'success') {
        typeClasses = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        icon = `<svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;
    } else if (type === 'error') {
        typeClasses = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        icon = `<svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`;
    }
    messageBox.className = `flex items-center p-4 mb-4 text-sm rounded-lg shadow-md ${typeClasses}`;
    messageBox.innerHTML = `${icon}<div>${message}</div>`;
    document.getElementById('messageBox').appendChild(messageBox);
    setTimeout(() => {
        messageBox.style.transition = 'opacity 0.5s ease';
        messageBox.style.opacity = '0';
        setTimeout(() => messageBox.remove(), 500);
    }, 3000);
}

const importBtn = document.getElementById('importBtn');
const fileInput = document.getElementById('fileInput');
importBtn.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', handleFile);

function handleFile(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const worksheet = workbook.Sheets[workbook.SheetNames[0]];
            const products = XLSX.utils.sheet_to_json(worksheet);
            if (products.length === 0) {
                showMessage("File Excel kosong atau format tidak sesuai.", "error"); return;
            }
            products.forEach(product => {
                const newProduct = {
                    id: Date.now().toString() + Math.random(),
                    name: product['Nama Produk'] || product.name,
                    quantity: product.Quantity || product.quantity || 0,
                    price: product.Harga || product.price || 0,
                    category: product.Kategori || product.category,
                    expiryDate: product['Tanggal Kadaluwarsa'] || product.expiryDate || ''
                };
                if (newProduct.name && newProduct.category) {
                    saveProduct(newProduct);
                }
            });
            loadAndRenderTable();
            showMessage(`${products.length} produk berhasil diimpor!`, 'success');
        } catch (error) {
            showMessage("Gagal memproses file Excel.", "error");
        }
    };
    reader.readAsArrayBuffer(file);
    event.target.value = '';
}

// Inisialisasi awal
window.onload = () => {
    loadAndRenderTable();
    const isDesktop = window.innerWidth >= 768;
    if (!isDesktop) {
        showMobilePage('list-section');
    }
};