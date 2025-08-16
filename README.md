````
# Attendance API

API ini buat untuk kebutuhan Fullstack Developer Challenge Test dari fleetify.id

---

## 📦 Instalasi

1. Clone repository:

```bash
git clone <repo-url>
cd backend-attendance
````

2. Install dependencies:

```bash
composer install
```

3. Copy `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

4. Sesuaikan konfigurasi database di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=root
DB_PASSWORD=
```

5. Jalankan migrasi:

```bash
php artisan migrate:fresh
```

6. Jalankan server:

```bash
php artisan serve
```

Server akan berjalan di `http://127.0.0.1:8000`.

---

## 🔄 Endpoint API

### 1. Departemen

| Method | Endpoint                 | Keterangan                                           |
| ------ | ------------------------ | ---------------------------------------------------- |
| GET    | `/api/departements`      | List semua departemen                                |
| POST   | `/api/departements`      | Tambah departemen                                    |
| GET    | `/api/departements/{id}` | Detail departemen                                    |
| PUT    | `/api/departements/{id}` | Update departemen                                    |
| DELETE | `/api/departements/{id}` | Hapus departemen (gagal jika masih dipakai employee) |

**Contoh Body POST / PUT**:

```json
{
    "departement_name": "Finance",
    "max_clock_in_time": "08:30:00",
    "max_clock_out_time": "17:30:00"
}
```

---

### 2. Employee

| Method | Endpoint              | Keterangan                                              |
| ------ | --------------------- | ------------------------------------------------------- |
| GET    | `/api/employees`      | List semua employee                                     |
| POST   | `/api/employees`      | Tambah employee                                         |
| GET    | `/api/employees/{id}` | Detail employee                                         |
| PUT    | `/api/employees/{id}` | Update employee                                         |
| DELETE | `/api/employees/{id}` | Hapus employee (semua attendance terkait ikut terhapus) |

**Contoh Body POST / PUT**:

```json
{
    "employee_id": "EMP003",
    "departement_id": 1,
    "name": "Siti Aminah",
    "address": "Jl. Anggrek No. 3"
}
```

---

### 3. Attendance

| Method | Endpoint                            | Keterangan                                          |
| ------ | ----------------------------------- | --------------------------------------------------- |
| POST   | `/api/attendance/check-in`          | Absen Masuk                                         |
| PUT    | `/api/attendance/check-out/{id}`    | Absen Keluar (gunakan `id` dari tabel attendance)   |
| GET    | `/api/attendance/logs`              | List log absensi (filter: `date` & `department_id`) |
| GET    | `/api/attendance/logs/{employeeId}` | List semua log absensi per employee                 |
| DELETE | `/api/attendance/logs/{employeeId}` | Hapus semua log absensi per employee                |

**Contoh Body POST check-in**:

```json
{
    "employee_id": 1
}
```

**Filter Logs per Tanggal & Departemen**:

```
GET /api/attendance/logs?date=2025-08-16&department_id=1
```

**Hasil filter akan menampilkan status ketepatan waktu absen masuk dan keluar** berdasarkan maksimal waktu di departemen masing-masing:

-   `status_in`: Tepat Waktu / Terlambat
-   `status_out`: Tepat Waktu / Pulang Lebih Awal

---

### 4. Contoh Response Logs

**GET /api/attendance/logs?date=2025-08-16&department_id=1**

```json
[
    {
        "id": 1,
        "attendance_id": "dad3433f-dd0c-4dd9-8e71-4dbc282032c0",
        "employee_id": 1,
        "clock_in": "2025-08-16 07:55:00",
        "clock_out": "2025-08-16 17:05:00",
        "status_in": "Terlambat",
        "status_out": "Tepat Waktu",
        "employee": {
            "id": 1,
            "employee_id": "EMP001",
            "departement_id": 1,
            "name": "Teguh Saputra",
            "departement": {
                "id": 1,
                "departement_name": "IT",
                "max_clock_in_time": "08:00:00",
                "max_clock_out_time": "17:00:00"
            }
        }
    }
]
```

---

## 🛠 Notes

-   Absensi **check-out** harus menggunakan `attendance.id`, bukan `employee_id`.
-   Departemen tidak bisa dihapus jika masih ada employee yang terkait.
-   Menghapus employee akan otomatis menghapus semua absensi terkait.

---

## 🔧 Tools

-   PHP 8+
-   Laravel 10+
-   MySQL
-   Composer
-   Postman / Insomnia untuk testing API

---

## 📌 Postman / Insomnia Collection

Import file **Attendance API.postman_collection.json** untuk langsung mencoba semua endpoint.

```

```
