# RESTful API - Hướng dẫn Test với Postman

## Cài đặt

1. Tải và cài đặt [Postman](https://www.postman.com/downloads/)
2. Import collection (hoặc tạo thủ công theo hướng dẫn bên dưới)

## Base URL

```
http://127.0.0.1:8000/api
```

---

## Tổng quan 23 Endpoints

```
PUBLIC (không cần đăng nhập)
  GET  /api/jobs                          Danh sách tin tuyển dụng
  GET  /api/jobs/{id}                     Chi tiết tin tuyển dụng

AUTH (không cần token)
  POST /api/auth/register                 Đăng ký
  POST /api/auth/login                    Đăng nhập

PROTECTED (cần Bearer Token)
  POST /api/auth/logout                   Đăng xuất
  GET  /api/auth/me                       Thông tin người dùng hiện tại
  PUT  /api/auth/me                       Cập nhật hồ sơ

  GET  /api/cvs                           Danh sách CV của tôi
  POST /api/cvs                           Tạo CV mới
  GET  /api/cvs/{id}                      Chi tiết CV
  PUT  /api/cvs/{id}                      Cập nhật CV
  DELETE /api/cvs/{id}                   Xóa CV

  GET  /api/applications                  Danh sách đơn ứng tuyển của tôi
  POST /api/applications                  Ứng tuyển công việc
  GET  /api/applications/{id}            Chi tiết đơn ứng tuyển

ADMIN / HR (cần token + quyền)
  GET  /api/admin/jobs                    Danh sách tất cả tin (admin)
  POST /api/admin/jobs                    Tạo tin tuyển dụng (admin)
  PUT  /api/admin/jobs/{id}              Cập nhật tin tuyển dụng
  DELETE /api/admin/jobs/{id}            Xóa tin tuyển dụng (admin)

  GET  /api/admin/jobs/{id}/applications Danh sách ứng viên (admin/HR)
  GET  /api/admin/applications            Tất cả đơn ứng tuyển (admin)
  PUT  /api/admin/applications/{id}/status Cập nhật trạng thái (admin/HR)
  DELETE /api/admin/applications/{id}    Xóa đơn ứng tuyển (admin/HR)
```

---

## Hướng dẫn chi tiết từng Endpoint

### 1. REGISTER - Đăng ký tài khoản

```
POST {{baseURL}}/auth/register
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
    "name": "Nguyen Van A",
    "email": "nguyenvana@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response 201:**
```json
{
    "success": true,
    "message": "Đăng ký thành công!",
    "data": {
        "user": {
            "id": 1,
            "name": "Nguyen Van A",
            "email": "nguyenvana@example.com",
            "avatar": null,
            "role": null,
            "plan": null,
            "created_at": "2026-03-29T10:00:00+00:00"
        },
        "token": "1|abc123xyz...",
        "token_type": "Bearer"
    }
}
```

---

### 2. LOGIN - Đăng nhập

```
POST {{baseURL}}/auth/login
Content-Type: application/json
```

**Body:**
```json
{
    "email": "nguyenvana@example.com",
    "password": "password123"
}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Đăng nhập thành công!",
    "data": {
        "user": {
            "id": 1,
            "name": "Nguyen Van A",
            "email": "nguyenvana@example.com",
            "role": "user",
            "plan": null,
            "created_at": "2026-03-29T10:00:00+00:00"
        },
        "token": "2|def456...",
        "token_type": "Bearer"
    }
}
```

**Response 401 (sai mật khẩu):**
```json
{
    "success": false,
    "message": "Email hoặc mật khẩu không đúng."
}
```

---

### 3. LOGOUT - Đăng xuất

```
POST {{baseURL}}/auth/logout
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Đăng xuất thành công!"
}
```

---

### 4. GET ME - Lấy thông tin user hiện tại

```
GET {{baseURL}}/auth/me
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Thông tin người dùng.",
    "data": {
        "id": 1,
        "name": "Nguyen Van A",
        "email": "nguyenvana@example.com",
        "avatar": null,
        "role": "user",
        "plan": null,
        "created_at": "2026-03-29T10:00:00+00:00"
    }
}
```

---

### 5. UPDATE PROFILE - Cập nhật hồ sơ

```
PUT {{baseURL}}/auth/me
Authorization: Bearer {token}
Content-Type: application/json
```

**Body (gửi field nào cần update):**
```json
{
    "name": "Nguyen Van B",
    "password": "newpassword123"
}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Cập nhật hồ sơ thành công!",
    "data": { ... }
}
```

---

### 6. LIST JOBS - Danh sách tin tuyển dụng (public)

```
GET {{baseURL}}/jobs
```

**Query Parameters (tùy chọn):**
```
?search=Laravel         Tìm theo tiêu đề, công ty, địa điểm
?job_type=full-time    Lọc theo loại công việc
?location=HCM          Lọc theo địa điểm
?page=2                Phân trang
```

**Response 200:**
```json
{
    "success": true,
    "message": "Danh sách tin tuyển dụng.",
    "data": [
        {
            "id": 1,
            "title": "Senior Laravel Developer",
            "description": "...",
            "location": "Ho Chi Minh City",
            "job_type": "full-time",
            "salary_min": 15000000,
            "salary_max": 30000000,
            "salary_currency": "VND",
            "company_name": "TechCorp Vietnam",
            "company_logo": "http://127.0.0.1:8000/storage/company_logos/logo.png",
            "status": "published",
            "applications_count": 12,
            "published_at": "2026-03-20T10:00:00+00:00",
            "created_at": "2026-03-15T08:00:00+00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 10,
        "total": 25
    },
    "links": { ... }
}
```

---

### 7. SHOW JOB - Chi tiết tin tuyển dụng

```
GET {{baseURL}}/jobs/{id}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Chi tiết tin tuyển dụng.",
    "data": {
        "id": 1,
        "title": "Senior Laravel Developer",
        "description": "Chúng tôi đang tìm kiếm...",
        "location": "Ho Chi Minh City",
        "job_type": "full-time",
        "salary_min": 15000000,
        "salary_max": 30000000,
        "company_name": "TechCorp Vietnam",
        "company_description": "Công ty công nghệ hàng đầu...",
        "company_logo": "http://127.0.0.1:8000/storage/company_logos/logo.png",
        "contact_email": "hr@techcorp.vn",
        "contact_phone": "0901234567",
        "status": "published",
        "user": {
            "id": 2,
            "name": "HR TechCorp",
            "avatar": null,
            "email": "hr@techcorp.vn"
        },
        "applications_count": 12,
        "views_count": 150,
        "published_at": "2026-03-20T10:00:00+00:00"
    }
}
```

---

### 8. CREATE CV - Tạo CV

```
POST {{baseURL}}/cvs
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "title": "CV Frontend Developer",
    "template_id": 1,
    "personal_info": {
        "name": "Nguyen Van A",
        "email": "nguyenvana@example.com",
        "phone": "0901234567",
        "address": "TP.HCM",
        "summary": "3 năm kinh nghiệm frontend..."
    },
    "objective": "Tìm kiếm vị trí Frontend Developer...",
    "theme_color": "#3B82F6",
    "font_family": "Inter",
    "visibility": "private",
    "is_draft": false
}
```

**Response 201:**
```json
{
    "success": true,
    "message": "Tạo CV thành công!",
    "data": {
        "id": 1,
        "title": "CV Frontend Developer",
        "slug": "cv-frontend-developer-abc123",
        "personal_info": { ... },
        "objective": "...",
        "theme_color": "#3B82F6",
        "visibility": "private",
        "is_draft": false,
        "sections": [],
        "created_at": "2026-03-29T10:00:00+00:00"
    }
}
```

---

### 9. LIST CVS - Danh sách CV của tôi

```
GET {{baseURL}}/cvs
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Danh sách CV.",
    "data": [
        {
            "id": 1,
            "title": "CV Frontend Developer",
            "slug": "cv-frontend-developer-abc123",
            "template": { "id": 1, "name": "Modern", "thumbnail": "..." },
            "visibility": "private",
            "is_draft": false,
            "created_at": "2026-03-29T10:00:00+00:00"
        }
    ],
    "meta": { ... }
}
```

---

### 10. SHOW CV - Chi tiết CV

```
GET {{baseURL}}/cvs/{id}
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Chi tiết CV.",
    "data": {
        "id": 1,
        "title": "CV Frontend Developer",
        "personal_info": { ... },
        "sections": [
            {
                "id": 1,
                "type": "experience",
                "title": "Kinh nghiệm làm việc",
                "sort_order": 1,
                "items": [
                    {
                        "id": 1,
                        "content": {
                            "position": "Frontend Developer",
                            "company": "TechCorp",
                            "start_date": "2023-01",
                            "end_date": "2025-03",
                            "description": "Phát triển Vue.js..."
                        },
                        "sort_order": 1
                    }
                ]
            }
        ]
    }
}
```

---

### 11. UPDATE CV - Cập nhật CV

```
PUT {{baseURL}}/cvs/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "title": "CV Senior Frontend Developer",
    "personal_info": {
        "name": "Nguyen Van A",
        "email": "nguyenvana@example.com",
        "phone": "0909999999",
        "summary": "5 năm kinh nghiệm..."
    }
}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Cập nhật CV thành công!",
    "data": { ... }
}
```

---

### 12. DELETE CV - Xóa CV

```
DELETE {{baseURL}}/cvs/{id}
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "success": true,
    "message": "Xóa CV thành công!"
}
```

---

### 13. APPLY JOB - Ứng tuyển công việc

```
POST {{baseURL}}/applications
Authorization: Bearer {token}
Content-Type: multipart/form-data (hoặc application/json)
```

**Body (multipart):**
```
job_post_id: 1
full_name: Nguyen Van A
email: nguyenvana@example.com
phone: 0901234567
cv_file: [chọn file PDF/DOC]
cover_letter: Tôi rất quan tâm đến vị trí này...
```

**Body (JSON):**
```json
{
    "job_post_id": 1,
    "full_name": "Nguyen Van A",
    "email": "nguyenvana@example.com",
    "phone": "0901234567",
    "cv_id": 1,
    "cover_letter": "Tôi rất quan tâm đến vị trí này..."
}
```

**Response 201:**
```json
{
    "success": true,
    "message": "Ứng tuyển thành công!",
    "data": {
        "id": 1,
        "job_post_id": 1,
        "full_name": "Nguyen Van A",
        "email": "nguyenvana@example.com",
        "status": "pending",
        "applied_at": "2026-03-29T10:00:00+00:00"
    }
}
```

**Response 409 (đã ứng tuyển rồi):**
```json
{
    "success": false,
    "message": "Bạn đã ứng tuyển tin này rồi."
}
```

---

### 14. LIST MY APPLICATIONS - Danh sách đơn ứng tuyển của tôi

```
GET {{baseURL}}/applications
Authorization: Bearer {token}
```

**Query Parameters:**
```
?status=pending      Lọc theo trạng thái
?page=2             Phân trang
```

**Response 200:**
```json
{
    "success": true,
    "message": "Danh sách đơn ứng tuyển.",
    "data": [
        {
            "id": 1,
            "job_post_id": 1,
            "job_post": {
                "id": 1,
                "title": "Senior Laravel Developer",
                "company_name": "TechCorp Vietnam",
                "status": "published"
            },
            "full_name": "Nguyen Van A",
            "email": "nguyenvana@example.com",
            "status": "pending",
            "applied_at": "2026-03-29T10:00:00+00:00"
        }
    ],
    "meta": { ... }
}
```

---

### 15. ADMIN - Tạo tin tuyển dụng

```
POST {{baseURL}}/admin/jobs
Authorization: Bearer {token} (user có role=admin)
Content-Type: multipart/form-data
```

**Body:**
```
title: Backend PHP Developer
description: Chúng tôi cần tuyển...
location: Ha Noi
job_type: full-time
salary_min: 20000000
salary_max: 35000000
salary_currency: VND
company_name: TechCorp Vietnam
contact_email: hr@techcorp.vn
status: published
company_logo: [chọn file ảnh]
```

**Response 201:**
```json
{
    "success": true,
    "message": "Tạo tin tuyển dụng thành công!",
    "data": { ... }
}
```

---

### 16. ADMIN - Cập nhật trạng thái đơn ứng tuyển

```
PUT {{baseURL}}/admin/applications/{id}/status
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "status": "reviewing",
    "notes": "Hồ sơ phù hợp, mời phỏng vấn."
}
```

**Trạng thái hợp lệ:** `pending`, `reviewing`, `interview`, `rejected`, `accepted`

**Response 200:**
```json
{
    "success": true,
    "message": "Cập nhật trạng thái thành công!",
    "data": {
        "id": 1,
        "status": "reviewing",
        "notes": "Hồ sơ phù hợp, mời phỏng vấn.",
        "job_post": { ... },
        "user": { ... }
    }
}
```

---

## Cài đặt trong Postman

### Tạo Environment

1. **Postman** → **Environments** → **Add**
2. Tên: `Laravel API`
3. Variable: `baseURL`
4. Initial value: `http://127.0.0.1:8000/api`
5. Save

### Cài đặt Authentication

1. Tạo Collection `CVactive API`
2. Trong **Authorization** tab của collection:
   - Type: `Bearer Token`
   - Token: `{{token}}`
3. **Auth sẽ tự động được gửi** cho tất cả requests trong collection

### Test Flow

```
Bước 1: POST /auth/register  → Lưu token vào {{token}}
Bước 2: GET  /auth/me        → Xác nhận đăng nhập thành công
Bước 3: POST /cvs           → Tạo CV mới
Bước 4: GET  /cvs           → Xem danh sách CV
Bước 5: POST /applications   → Ứng tuyển công việc
Bước 6: GET  /applications   → Xem đơn đã ứng tuyển
Bước 7: POST /auth/logout    → Đăng xuất
```

### Script tự động lưu token (Tests tab)

```javascript
// Sau khi login/register thành công, chạy script này
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.collectionVariables.set("token", jsonData.data.token);
        console.log("Token saved: " + jsonData.data.token);
    }
}
```

---

## Các mã lỗi thường gặp

| HTTP Code | Ý nghĩa | Nguyên nhân |
|-----------|---------|------------|
| 200 | OK | Thành công |
| 201 | Created | Tạo mới thành công |
| 400 | Bad Request | Dữ liệu không hợp lệ |
| 401 | Unauthorized | Chưa đăng nhập / Token hết hạn |
| 403 | Forbidden | Không có quyền truy cập |
| 404 | Not Found | Resource không tồn tại |
| 409 | Conflict | Đã ứng tuyển rồi / Email trùng |
| 422 | Unprocessable | Validation lỗi |
| 500 | Server Error | Lỗi server |

---

## Validation Error Response (422)

```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "Email đã được sử dụng."
        ],
        "password": [
            "Mật khẩu phải có ít nhất 8 ký tự."
        ]
    }
}
```
