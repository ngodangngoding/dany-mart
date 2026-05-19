# 🤖 Laravel Agent Profile & Context

Dokumentasi ini berfungsi sebagai panduan konteks, arsitektur, dan standardisasi code bagi AI Agent / Developer dalam mengelola dan mengembangkan aplikasi ini.

---

## 📌 1. Project Overview
*   **Project Name:** [Nama Project Lu, misal: Dany Mart]
*   **Description:** [Penjelasan singkat aplikasi ini buat apa, misal: Sistem Kasir Terintegrasi]
*   **Laravel Version:** Laravel 11.x (atau sesuaikan)
*   **PHP Version:** PHP 8.2+
*   **Stack Utama:** 
    *   Backend: Laravel Eloquent ORM
    *   Frontend: Blade
    *   Database: MySQL

---

## 🛠️ 2. Core Architecture Rules
Setiap penulisan code baru atau modifikasi wajib mengikuti pattern berikut:

*   **Monolithic Controller Style:** Logic bisnis, validasi, integrasi Midtrans, logging, dan manipulasi database (DB Transaction) ditulis langsung di dalam method Controller terkait (belum dipisah ke Service/Repository).
*   **Models:** Gunakan strict type casting dan definisikan relationship dengan jelas.
*   **Views/Frontend:** Gunakan komponen yang reusable. Ikuti struktur styling yang sudah ada tanpa merusak layout global.
**Database Safety:** Wajib menggunakan `DB::beginTransaction()`, `DB::commit()`, dan `DB::rollBack()` di dalam blok `try-catch` saat melakukan *multi-table inserts* (seperti saat `store` order, payment, dan order items sekaligus).
*   **Routing:** Kelompokkan route berdasarkan modul menggunakan `Route::middleware()` dan pastikan penamaan route konsisten (`route('nama.index')`).

---

## 3. Core Models
Relasi model mengikuti struktur ERD dari migrasi utama aplikasi. Saat menambah fitur baru, gunakan relationship Eloquent yang sudah ada dan jaga konsistensi foreign key berikut:

*   **User**
    *   `hasMany(Order::class)` melalui `orders()`.
    *   `hasMany(Expense::class)` melalui `expenses()`.
    *   `hasMany(ProductStockHistory::class)` melalui `productStockHistories()`.
    *   Kolom penting: `name`, `username`, `email`, `password`, `role`, `photo`.
    *   Role valid: `admin`, `kasir`.
*   **Category**
    *   `hasMany(Product::class)` melalui `products()`.
    *   Kolom penting: `name`, `code`.
    *   `code` dapat dibuat otomatis dari nama kategori lewat model event `creating`.
*   **Product**
    *   `belongsTo(Category::class)` melalui `category()`.
    *   `hasMany(OrderItem::class)` melalui `orderItems()`.
    *   `hasMany(ProductStockHistory::class)` melalui `stockHistories()`.
    *   Foreign key: `category_id` mengarah ke `categories.id` dengan `onDelete('cascade')`.
    *   Kolom penting: `category_id`, `code`, `name`, `unit`, `purchase_price`, `selling_price`, `stock`.
    *   `code` bersifat unique dan dapat dibuat otomatis berdasarkan kode kategori.
*   **Order**
    *   `belongsTo(User::class)` melalui `user()`.
    *   `hasMany(OrderItem::class)` melalui `items()`.
    *   Foreign key: `user_id` mengarah ke `users.id` dengan `onDelete('cascade')`.
    *   Kolom penting: `user_id`, `order_number`, `total_amount`, `payment_method`, `payment_amount`, `change_amount`, `order_date`.
    *   `order_number` bersifat unique.
*   **OrderItem**
    *   `belongsTo(Order::class)` melalui `order()`.
    *   `belongsTo(Product::class)` melalui `product()`.
    *   Foreign key: `order_id` mengarah ke `orders.id` dengan `onDelete('cascade')`.
    *   Foreign key: `product_id` mengarah ke `products.id` dengan `onDelete('cascade')`.
    *   Kolom penting: `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`.
*   **Expense**
    *   `belongsTo(User::class)` melalui `user()`.
    *   Foreign key: `user_id` mengarah ke `users.id` dengan `onDelete('cascade')`.
    *   Kolom penting: `user_id`, `date`, `expense_category`, `description`, `amount`.
    *   Kategori valid: `Listrik`, `Gaji`, `Perlengkapan`, `Sewa`.
*   **ProductStockHistory**
    *   `belongsTo(Product::class)` melalui `product()`.
    *   `belongsTo(User::class)` melalui `user()`.
    *   Foreign key: `product_id` mengarah ke `products.id` dengan `onDelete('cascade')`.
    *   Foreign key: `user_id` mengarah ke `users.id` dengan `onDelete('cascade')`.
    *   Kolom penting: `product_id`, `user_id`, `added_stock`, `current_stock`, `note`.

Ringkasan ERD:
`User 1..* Orders`, `User 1..* Expenses`, `User 1..* ProductStockHistories`, `Category 1..* Products`, `Product 1..* OrderItems`, `Product 1..* ProductStockHistories`, dan `Order 1..* OrderItems`.

---

## 🎨 4. Coding Standards & Controller Styling Rules
AI Agent / Developer WAJIB mematuhi aturan penulisan syntax di bawah ini tanpa pengecualian. Jangan gunakan gaya modern bawaan Laravel jika bertentangan dengan aturan berikut:

### 🚫 A. Type Code & Method Signature
*   **No Strict Return Types:** Dilarang menggunakan type-hinting return pada method controller (contoh salah: `: View`, `: RedirectResponse`, `: JsonResponse`). Biarkan return type bersifat implisit/dinamis.
*   **No Route Model Binding:** Dilarang menangkap model langsung pada parameter method (contoh salah: `public function show(Category $category)`). Wajib menangkap ID manual (`$id`), lalu ambil data di dalam method menggunakan `Model::find($id)` atau `Model::findOrFail($id)`.

### 🧪 B. Validation Rules Syntax
*   **Pipe Syntax Only:** Aturan validasi wajib ditulis menggunakan string tunggal dengan pemisah pipa (`|`), dilarang menggunakan format array terpisah.
    *   *Benar:* `'name' => 'required|string|max:255|unique:categories,name'`
    *   *Salah:* `'name' => ['required', 'string', 'max:255', 'unique:categories,name']`
*   **Update Validation Flow:** Pada method `update()`, pastikan data dicari terlebih dahulu sebelum fungsi `$request->validate()` dipanggil. Ini wajib agar variabel ID data yang sedang diubah bisa disisipkan ke rule `unique` untuk mengabaikan (*ignore*) dirinya sendiri.
    *   *Contoh:* `'name' => 'required|string|max:255|unique:categories,name,' . $category->id`

### 📦 C. Data Response & View Handling
*   **Compact Function:** Wajib menggunakan fungsi `compact()` untuk melempar variabel dari Controller ke View Blade. Dilarang menggunakan format array berpasangan key-value biasa kecuali terpaksa.
    *   *Benar:* `return view('categories.index', compact('categories'));`
    *   *Salah:* `return view('categories.index', ['categories' => $categories]);`

### 🔒 D. Database Safety & Multi-Table Insert
*   **DB Transactions:** Proses manipulasi data yang melibatkan *multi-table inserts/updates* (lebih dari satu tabel) wajib dibungkus di dalam blok `try-catch` menggunakan Database Transaction.
*   **Template Struktur Transaction & Logging:**
    ```php
    try {
        DB::beginTransaction();
        
        // 1. Proses manipulasi data utama
        // 2. Proses mutasi stok otomatis wajib menggunakan method bawaan:
        //    $product->decrement('stock_quantity', $quantity);
        
        DB::commit();

        // 3. Wajib mencatat riwayat ke ActivityLog setelah commit sukses
        ActivityLog::create([
            'activity_type' => 'nama_aksi',
            'description' => 'Keterangan aksi menggunakan ID/Nama objek',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('nama.index')->with('success', 'Pesan sukses');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
    ```